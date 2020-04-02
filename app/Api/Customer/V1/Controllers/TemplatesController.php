<?php

namespace Api\Customer\V1\Controllers;

use Api\Customer\V1\Transforms\TemplateTransformer;
use App\Controller;
use Customer\Entities\Template;
use Dingo\Api\Http\Request;

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
        $data = $this->template->firstOrCreate($request->all());

        return $this->response->item(
            $data, new TemplateTransformer
        );
    }

    public function update(Request $request, $template_id)
    {
        $data = $this->template->where(['id' => $template_id])
        ->update($request->all());

        return $this->response->item(
            $data, new TemplateTransformer
        );
    }
}
