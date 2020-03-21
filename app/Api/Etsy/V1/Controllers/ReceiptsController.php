<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Voidpointers\Etsy\Facades\Etsy;

/**
 * 收据控制器
 */
class ReceiptsController extends Controller
{
    public function index(Request $request)
    {
        return Etsy::findAllShopReceipts([
            'params' => $request->all(),
            'associations' => [
                'Transactions' => ['associations' => ['MainImage']]
            ]
        ]);
    }
}
