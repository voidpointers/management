<?php

namespace Api\Customer\V1\Controllers;

use Api\Customer\V1\Transforms\TemplateTransformer;
use App\Controller;
use Customer\Entities\Template;
use Dingo\Api\Http\Request;
use Order\Entities\Consignee;
use Order\Entities\Logistics;
use Order\Entities\Receipt;

class TemplatesController extends Controller
{
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function index(Request $request)
    {
        $applay = $this->template->apply($request);

        $data = $applay->orderBy('id', 'desc')
        ->paginate((int) $request->get('limit', 30));

        return $this->response->paginator(
            $data, new TemplateTransformer
        );
    }

    public function show(Request $request, $template_id)
    {
        $data = $this->template->find($template_id);

        return $this->response->item(
            $data, new TemplateTransformer
        );
    }

    public function store(Request $request)
    {
        if (!$request->has('attachments')) {
            $request->offsetSet('attachments', '');
        }
        $data = $this->template->firstOrCreate($request->all());

        return $this->response->item(
            $data, new TemplateTransformer
        );
    }

    public function update(Request $request, $template_id)
    {
        $this->template->where(['id' => $template_id])
        ->update($request->all());

        return ['msg' => 'success'];
    }

    public function placeholder(Request $request)
    {
        $receipt_sn = $request->input('receipt_sn', '');
        $data = config('placeholders');
        foreach ($data as $key => $datum) {
            switch ($datum['key']) {
                case 'receipt_id':
                    $data[$key]['true_value'] = Receipt::where(['receipt_sn' => $receipt_sn])->value('receipt_id');
                    break;
                case 'consignee':
                    $data[$key]['true_value'] = Consignee::where(['receipt_sn' => $receipt_sn])->value('name');
                    break;
                case 'consignee_address':
                    $data[$key]['true_value'] = Consignee::where(['receipt_sn' => $receipt_sn])->value('formatted_address');
                    break;
                case 'trade_no':
                    $data[$key]['true_value'] = Logistics::where(['receipt_sn' => $receipt_sn])->value('tracking_code');
                    break;
                case 'trade_url':
                    $data[$key]['true_value'] = Logistics::where(['receipt_sn' => $receipt_sn])->value('tracking_url');
                    break;
                case 'cur_date':
                    $data[$key]['true_value'] = date("Y-m-d");
                    break;
            }
            $data[$key]['true_value'] = $data[$key]['true_value'] ?? '';
        }
        return $this->response->array($data);
    }
}
