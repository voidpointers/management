<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
        // 'middleware' => 'api.auth'
], function ($api) {
    $api->group([
        'namespace' => 'Api\Receipt\V1\Controllers',
        'prefix' => 'order',
    ], function ($api) {
        $api->resource('receipts', 'ReceiptsController');
        $api->get('export', 'ReceiptsController@export');
        $api->post('import', 'ReceiptsController@import');
        $api->post('close', 'ReceiptsController@close');
        $api->get('sales/export', 'SalesController@export');
        $api->post('transaction/{receipt_sn}/create', 'TransactionsController@create');
        $api->post('consignee/{receipt_sn}/update', 'ConsigneesController@update');
    });
    $api->group([
        'namespace' => 'Api\Package\V1\Controllers',
    ], function ($api) {
        $api->resource('packages', 'PackagesController');
        $api->post('dispatch', 'PackagesController@delivery');
        $api->post('print', 'LogisticsController@labels');
        $api->get('tracking/info/{order_number}', 'LogisticsController@trackInfo');
        $api->post('logistics/create', 'LogisticsController@create');
    });
    $api->group([
        'namespace' => 'Api\Logistics\V1\Controllers',
        'prefix' => 'logistics',
    ], function ($api) {
        $api->get('provider/lists', 'ProvidersController@lists');
        $api->get('channel/lists', 'ChannelsController@lists');
    });
    $api->group([
        'namespace' => 'Api\Common\V1\Controllers',
        'prefix' => 'common'
    ], function ($api) {
        $api->get('country/lists', 'CountriesController@lists');
        $api->post('register', 'RegisterController@register');
        $api->post('login', 'AuthorizationsController@login');
        $api->resource('shops', 'ShopsController');
        $api->post('upload', 'FilesController@fileUpload');
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
    });
});
