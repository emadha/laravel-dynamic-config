<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(config('emadha.site-config.table'))) {
            Schema::create(config('emadha.site-config.table'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('k', 500)->unique();
                $table->text('v')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('emadha.site-config.table'));
    }
}
