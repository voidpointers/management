<?php

namespace Api\Common\V1\Controllers;

use Aggregate\Services\ReceiptService;
use Api\Common\V1\Transforms\ShopTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Common\Entities\Shop;

class ShopsController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function index(Request $request)
    {
        $query = $request->input('query', '');

        $columns = ['*'];
        if ('few' == $query) {
            $columns = ['shop_id', 'shop_name', 'user_id', 'username', 'icon'];
        }

        $data = Shop::where(['status' => 1])
        ->select($columns)
        ->paginate($request->get('limit', 30));

        $aggregates = $this->receiptService->count();

        $data->each(function ($item) use ($aggregates) {
            $item->count_receipts = $aggregates[$item->shop_id]->total ?? 0;
        });

        return $this->response->paginator(
            $data,
            ShopTransformer::class
        );
    }

    public function store(Request $request)
    {

    }
}
