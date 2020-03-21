<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

if (!function_exists('generate_unique_id')) {
    /**
     * 通过redis生成唯一值
     *
     * @param string $key
     * @return integer
     */
    function generate_unique_id($seed = 1000000, $key = 'logistics-primary-key')
    {
        $key = strtoupper($key);
        if ($seed > Redis::get($key)) {
            Redis::set($key, $seed);
        }
        return Redis::incr($key);
    }
}

if (!function_exists('get_last_sql')) {
    /**
     * 获取最近一次执行的指令
     *
     * @return string
     * @access public
     */
    function get_last_sql()
    {
        // Register a database query listener with the connection.
        DB::listen(function ($sql) {
            $query = $sql->sql;
            if ($sql->bindings) {
                foreach ($sql->bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $query = preg_replace('/\?/', $value, $query, 1);
                }
            }
            dump($query);
        });
    }
}

if (!function_exists('generate_package_sn')) {
    /**
     * 生成包裹编号
     */
    function generate_package_sn()
    {
        $seed = generate_unique_id(1000000, 'package-primary-key');
        return mt_rand(10, 99) . $seed . mt_rand(0, 9);
    }
}

if (!function_exists('camelize')) {

    function camelize($uncamelized_words, $separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));

        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }
}
