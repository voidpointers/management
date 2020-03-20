<?php

namespace Api\Etsy\V1\Controllers;

use Api\Controller;
use Dingo\Api\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {

    }

    public function show($user_id, Request $request)
    {
        $user = \Etsy::getUser(['params' => ['user_id' => $user_id]]);
        return $user;
    }
}
