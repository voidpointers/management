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
        $api->get('export/{type}', 'ReceiptsController@export');
        $api->post('import', 'ReceiptsController@import');
        $api->post('close', 'ReceiptsController@close');
        $api->get('pull', 'ReceiptsController@pull');
        $api->resource('transactions', 'TransactionsController');
        $api->resource('consignees', 'ConsigneesController');
        $api->resource('logistics', 'LogisticsController');
    });
    $api->group([
        'namespace' => 'Api\Package\V1\Controllers',
        'prefix' => 'package'
    ], function ($api) {
        $api->resource('logistics', 'LogisticsController');
        $api->resource('packages', 'PackagesController');
        $api->post('dispatch', 'PackagesController@delivery');
        $api->get('labels', 'LogisticsController@labels');
        $api->get('tracks/{order_number}', 'LogisticsController@trackInfo');
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
        $api->get('auth/redirect', 'AuthController@redirect');
        $api->get('auth/approve', 'AuthController@approve');
    });
    $api->group([
        'namespace' => 'Api\Etsy\V1\Controllers',
        'prefix' => 'etsy'
    ], function ($api) {
        $api->resource('receipts', 'ReceiptsController');
        $api->resource('shops', 'ShopsController');
    });
    $api->group([
        'namespace' => 'Api\Customer\V1\Controllers',
        'prefix' => 'customer'
    ], function ($api) {
        $api->get('messages/{convo_id}/history', 'MessagesController@history');
        $api->get('templates/placeholder', 'TemplatesController@placeholder');
        $api->resource('messages', 'MessagesController');
        $api->post('messages/pending', 'MessagesController@pending');
        $api->post('drafts/approve', 'DraftsController@approve');
        $api->resource('drafts', 'DraftsController');
        $api->post('conversations/draft/approve', 'DraftsController@approve');
        $api->resource('receipts', 'ReceiptsController');
        $api->resource('templates', 'TemplatesController');
    });
    $api->group([
        'namespace' => 'Api\Product\V1\Controllers',
        'prefix' => 'product'
    ], function ($api) {
        $api->get('listings/pull', 'ListingsController@pull');
        $api->resource('listings', 'ListingsController');
    });
});
