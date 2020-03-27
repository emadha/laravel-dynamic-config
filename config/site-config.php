<?php
return [
    /* The Config database table name */
    'table'                   => 'conf',

    /*
     * The key that defines which config file should be loaded dynamically
     * and store into the database
     * Add that key to any config file to make it dynamic.
     */
    'dynamic_key'             => 'dynamics',

    /*
     * they key which will have the defaults of a config key
     * example: config('defaults.app.name'); This is added on runtime.
     */
    'defaults_key'            => 'defaults',

    /*
     * Delete orphan keys
     * if set to true and delete a key from the actual config file,
     * that key will be deleted from database.
     */
    'auto_delete_orphan_keys' => true,
];