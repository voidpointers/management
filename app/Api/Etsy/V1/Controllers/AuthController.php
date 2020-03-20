<?php

namespace Api\Etsy\V1\Controllers;

use Api\Controller;
use Dingo\Api\Http\Request;
use Shop\Entities\Shop;

class AuthController extends Controller
{
    public function redirect()
    {
        return redirect(\Etsy::authorize(env('ETSY_REDIRECT_URI')));
    }

    public function approve(Request $request)
    {
        $credentials = \Etsy::approve($request->get('oauth_token'), $request->get('oauth_verifier'));

        return $this->store($credentials);
    }

    public function store($credentials)
    {
        $user = \Etsy::getUserDetails();
        $shop = \Etsy::findAllUserShops([
            'params' => [
                'user_id' => $user->uid
            ]
        ]);

        (new Shop)->store($shop['results'], $credentials);
        return $shop;
    }
}
