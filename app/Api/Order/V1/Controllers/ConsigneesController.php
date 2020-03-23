<?php

namespace Api\Order\V1\Controllers;

use App\Controller;
use Api\Order\V1\Requests\ConsigneeRequest;
use Order\Entities\Consignee;

class ConsigneesController extends Controller
{
    protected $country;

    public function __construct(
        Consignee $consignee)
    {
        $this->consignee = $consignee;
    }

    public function update(ConsigneeRequest $request, $receipt_sn)
    {
        $validated = $request->validated();
        if (!$validated) {
            return $this->response->error('缺少必要参数', 500);
        }

        $this->consignee->where(['receipt_sn' => $receipt_sn])
        ->update($validated);

        return $this->response->array(['msg' => 'success']);
    }
}
