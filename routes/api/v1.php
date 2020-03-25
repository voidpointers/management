<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
        // 'middleware' => 'api.auth'
], function ($api) {
    $api->group([
        'namespace' => 'Api\Order\V1\Controllers',
        'prefix' => 'order',
    ], function ($api) {
        $api->resource('receipts', 'ReceiptsController');
        $api->get('export', 'ReceiptsController@export');
        $api->post('import', 'ReceiptsController@import');
        $api->post('close', 'ReceiptsController@close');
        $api->get('sales/export', 'SalesController@export');
        $api->post('transaction/{receipt_sn}/create', 'TransactionsController@create');
        $api->resource('consignees', 'ConsigneesController');
    });
    $api->group([
        'namespace' => 'Api\Package\V1\Controllers',
        'prefix' => 'package'
    ], function ($api) {
        $api->resource('logistics', 'LogisticsController');
        $api->resource('packages', 'PackagesController');
        $api->post('dispatch', 'PackagesController@delivery');
        $api->post('print', 'LogisticsController@labels');
        $api->get('tracking/info/{order_number}', 'LogisticsController@trackInfo');
    });
    $api->group([
        'namespace' => 'Api\Common\V1\Controllers',
        'prefix' => 'common'
    ], function ($api) {
        $api->resource('countries', 'CountriesController');
        $api->resource('shops', 'ShopsController');
        $api->resource('providers', 'ProvidersController');
        $api->resource('channels', 'ChannelsController');
        $api->post('upload', 'FilesController@upload');
        $api->post('register', 'UsersController@register');
        $api->post('login', 'UsersController@login');
        $api->get('redirect', 'AuthController@redirect');
    });
    $api->group([
        'namespace' => 'Api\Etsy\V1\Controllers',
        'prefix' => 'etsy'
    ], function ($api) {
        $api->resource('receipts', 'ReceiptsController');
    });
    $api->group([
        'namespace' => 'Api\Customer\V1\Controllers',
        'prefix' => 'customer'
    ], function ($api) {
        $api->resource('messages', 'MessagesController');
        $api->resource('drafts', 'DraftsController');
        $api->post('drafts/approve', 'DraftsController@approve');
    });
    $api->group([
        'namespace' => 'Api\Product\V1\Controllers',
        'prefix' => 'product'
    ], function ($api) {
        $api->get('listings/pull', 'ListingsController@pull');
        $api->resource('listings', 'ListingsController');
    });
});
