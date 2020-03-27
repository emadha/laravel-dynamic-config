<?php

namespace EmadHa\DynamicConfig;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use mysql_xdevapi\Exception;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!class_exists('CreateSiteConfigTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_site_config_table.php.stub' => database_path('migrations/' . $timestamp . '_create_site_config_table.php'),
                ], 'migrations');
            }

            $this->publishes([
                __DIR__ . '/../config/site-config.php' => config_path('emadha/site-config.php'),
            ], 'config');
        }

        $this->initConfig();
    }

    private function initConfig()
    {

        # Check if the table exists
        if (!Schema::hasTable(config('emadha.site-config.table'))) {

            # Don't crash, Log the error instead
            Log::error(sprintf(
                    get_class($this) . " is missing the the dynamic config table [`%s`]. you might need to do `php artisan vendor:publish` && `php artisan migrate`",
                    config('emadha.site-config.table'))
            );

            return false;
        }

        # Create a new collection of what's dynamic
        $DefaultConfig = collect([]);

        # Return the config entries containing ['dynamic'=>true] key
        collect(config()->all())->each(function ($value, $key) use (&$DefaultConfig) {

            # Check if the current config key has dynamic key set to it, and it's true
            if (array_key_exists(config('emadha.site-config.dynamic_key'), $value)
                && $value[config('emadha.site-config.dynamic_key')] == true) {

                # unset that dynamic value
                unset($value[config('emadha.site-config.dynamic_key')]);

                # Add that to the DynamicConfig collection
                $DefaultConfig->put($key, $value);
            }

        });

        # Keep the defaults for reference
        config([config('emadha.site-config.defaults_key') => $DefaultConfig]);

        # Flatten the config table data
        $prefixedKeys = $this->prefixKey(null, $DefaultConfig->all());

        # Insert the flattened data into database
        foreach ($prefixedKeys as $_key => $_value) {

            # Get the row from database if it exists,
            # If not, add it using the value from the actual config file.
            DynamicConfig::firstOrCreate(['k' => $_key], ['v' => $_value]);

        }

        # Build the Config array
        $DynamicConfig = DynamicConfig::all();

        # Check if auto deleting orphan keys is enabled
        # and delete those if they don't exists in the actual config file
        if (config('emadha.site-config.auto_delete_orphan_keys') == true) {

            # Check for orphan keys
            $orphanKeys = array_diff_assoc($DynamicConfig->pluck('v', 'k')->toArray(), $prefixedKeys);

            # Delete orphan keys
            DynamicConfig::whereIn('k', array_keys($orphanKeys))->delete();

        }

        # Store these config into the config() helper, but as model objects
        # Thus making Model's method accessible from here
        # example: config('app.name')->revert().
        # Available methods are `revert`, `default` and `setTo($value)`
        $DynamicConfig->map(function ($config) use ($DefaultConfig) {
            config([$config->k => $config]);
        });

    }

    public function prefixKey($prefix, $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::prefixKey($prefix . $key . '.', $value));
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
