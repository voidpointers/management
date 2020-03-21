<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Voidpointers\Etsy\Facades\Etsy;

class CategoriesController extends Controller
{
    public function index()
    {
        return Etsy::findAllTopCategory();
    }

    public function sub(Request $request)
    {
        return Etsy::findAllTopCategoryChildren([
            'params' => ['tag' => $request->input('tag')],
        ]);
    }

    public function third(Request $request)
    {
        return Etsy::findAllSubCategoryChildren([
            'params' => [
                'tag' => $request->input('tag'),
                'subtag' => $request->input('subtag'),
            ]
        ]);
    }

    public function taxonomy()
    {
        return Etsy::getSellerTaxonomy();
    }
}
