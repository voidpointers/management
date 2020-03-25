<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('picture')) {
            return $this->response->error('请选择要上传的文件', 500);
        }
        $picture = $request->file('picture');
        if (!$picture->isValid()) {
            return $this->response->error('无效的上传文件', 500);
        }

        $extension = $picture->getClientOriginalExtension();
        // 文件名
        $file_name = $picture->getClientOriginalName();
        // 生成新的统一格式的文件名
        $file = md5($file_name . time() . mt_rand(1, 10000)) . '.' . $extension;
        // 图片保存路径
        $save_path = '/images/' . $file;

        // 将文件保存到本地 storage/app/public/images 目录下，先判断同名文件是否已经存在，如果存在直接返回
        if (Storage::disk('public')->has($save_path)) {
            return $this->response->array([$save_path]);
        }

        // 否则执行保存操作，保存成功将访问路径返回给调用方
        if ($picture->storePubliclyAs('images', $file, ['disk' => 'public'])) {
            return $this->response->array([$save_path]);
        }

        return $this->response->error('文件上传失败', 500);
    }
}
