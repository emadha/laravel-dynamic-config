<?php

namespace EmadHa\DynamicConfig;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DynamicConfig
 *
 * @property mixed v
 * @package EmadHa\DynamicConfig
 */
class DynamicConfig extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * DynamicConfig constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('emadha.site-config.table'));
    }

    /**
     * Update the current key value
     *
     * @param $value
     *
     * @return bool
     */
    public function setTo($value)
    {
        return $this->update(['v' => $value]);
    }

    /**
     * Get the default value of the specified key
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function default()
    {
        return config(
            config('emadha.site-config.defaults_key') . '.' . $this->k
        );
    }

    /**
     * Revert the current key to it's original value
     * from the actual config file
     *
     * @return mixed
     */
    public function revert()
    {
        return config($this->k)->setTo(
            config(config('emadha.site-config.defaults_key') . '.' . $this->k)
        );
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->v;
    }

}

