<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Voidpointers\Etsy\Facades\Etsy;

class ShopsController extends Controller
{
    public function index(Request $request)
    {
        return Etsy::getShop([
            'params' => $request->all(),
            'associations' => [
                'User' => ['associations' => ['User']]
            ]
        ]);
    }

    public function update(Request $request)
    {
        return Etsy::updateShop([
            'params' => $request->all()
        ]);
    }
}
