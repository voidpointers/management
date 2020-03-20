<?php

namespace Api\Receipt\V1\Controllers;

use Api\Controller;
use Api\Receipt\V1\Requests\ConsigneeRequest;
use Dingo\Api\Http\Request;
use Receipt\Entities\Consignee;
use Receipt\Repositories\ConsigneeRepository;
use System\Repositories\CountryRepository;

class ConsigneesController extends Controller
{
    protected $consigneeRepository;

    protected $countryRepository;

    public function __construct(
        ConsigneeRepository $consigneeRepository,
        CountryRepository $countryRepository)
    {
        $this->consigneeRepository = $consigneeRepository;
        $this->countryRepository = $countryRepository;
    }

    public function update(ConsigneeRequest $request, $receipt_sn)
    {
        $validated = $request->validated();
        if (!$validated) {
            return $this->response->error('缺少必要参数', 500);
        }

        $this->consigneeRepository->updateWhere(
            ['receipt_sn' => $receipt_sn], $validated
        );

        return $this->response->array(['msg' => 'success']);
    }
}
