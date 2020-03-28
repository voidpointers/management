<?php

use Common\Entities\Shop;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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

if (!function_exists('generate_uniqid')) {
    /**
     * 数据库生成唯一ID
     */
    function generate_uniqid()
    {
        DB::select("REPLACE INTO unique_id_generator (ticket) VALUES ('a')", [], false);
        $insert = DB::select('SELECT LAST_INSERT_ID() as id', [], false);

        return $insert[0]->id;
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

if (!function_exists('custom_log')) {
    /**
     * 自定义日志
     *
     * @param string $level
     * @param string $path
     * @param $msg
     */
    function custom_log(string $level, string $path, $msg)
    {
        $log = new Logger('');
        try {
            $log->pushHandler(new StreamHandler(storage_path('logs/' . $path)));
        } catch (Exception $e) {

        }

        $msg = is_array($msg) ? json_encode($msg) : $msg;
        $log->log($level, $msg);
    }
}

if (!function_exists('shop_id')) {
    function shop_id()
    {
        return Cache::store('array')->get('shop_id') ?? 0;
    }
}

if (!function_exists('current_user')) {
    function current_user()
    {
        $shop_id = Cache::store('array')->get('shop_id');
        return (int) config('shops')[$shop_id]['user_id'];
    }
}

if (!function_exists('set_shop')) {
    function set_shop($params)
    {
        Cache::store('file')->set('shop_' . key($params), current($params));
        return $params;
    }
}

if (!function_exists('get_shop')) {
    function get_shop($shop_id = 0)
    {
        $shop = Cache::store('file')->get('shop_' . $shop_id);
        if (!$shop) {
            $shop = (array) DB::table('shops')->where('shop_id', $shop_id)->first();
            Cache::store('file')->set('shop_' . $shop_id, $shop);
        }
        return $shop;
    }
}
