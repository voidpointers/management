<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Common\Entities\Shop;
use Voidpointers\Etsy\Facades\Etsy;

class AuthController extends Controller
{
    public function redirect(Request $request)
    {
        set_shop([shop_id() => $request->all()]);

        return Etsy::authorize(env('ETSY_REDIRECT_URI'));
    }

    public function approve(Request $request)
    {
        $credentials = Etsy::approve($request->get('oauth_token'), $request->get('oauth_verifier'));

        return $this->store($credentials);
    }

    public function store($credentials)
    {
        $shop = Etsy::findAllUserShops([
            'params' => [
                'user_id' => (Etsy::getUserDetails())->uid
            ]
        ]);

        $data = $shop['results'][0];
        $data['access_token'] = $credentials->getIdentifier();
        $data['access_secret'] = $credentials->getSecret();

        return (new Shop)->store($data);
    }
}
