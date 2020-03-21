<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Voidpointers\Etsy\Facades\Etsy;

class UsersController extends Controller
{
    public function show($user_id, Request $request)
    {
        return Etsy::getUser(['params' => ['user_id' => $user_id]]);
    }
}
