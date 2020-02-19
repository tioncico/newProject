<?php
/**
 * Created by PhpStorm.
 * User: fushu
 * Date: 2019/12/18
 * Time: 11:01
 */

namespace App\HttpController\Api\Admin;


use EasySwoole\Http\Message\Status;
use EasySwoole\Utility\File;

class UploadFile extends BaseController
{
    /**
     * @api {get|post} /Api/Admin/UploadFile/image
     * @apiName image
     * @apiGroup /Api/Admin/file
     * @apiPermission user
     * @apiDescription 图片上传
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {"code":0,"data":{customServiceId："....",},"msg":"success"}
     * @author: tioncico < 1067197739@qq.com >
     */

    function file()
    {
        $filePath = $this->fileUpload();
        if ($filePath) {
            $this->writeJson(Status::CODE_OK, $filePath, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, false, '文件上传失败');
        }
    }

    private function fileUpload()
    {
        try {
            $file = $this->request()->getUploadedFile('file');
            if (!$file) $file = $this->request()->getUploadedFile('upload');
            $extensionName = substr(strrchr($file->getClientFilename(), '.'), 1);
            $fileName = date('ymdHis') . rand(1000, 9999);
            $savePath = "/Temp/" . date('Ymd');
            $saveFullPath = $savePath . '/' . $fileName . '.' . $extensionName;
            File::moveFile($file->getTempName(), EASYSWOOLE_ROOT.'/Static' . $saveFullPath);
            return $saveFullPath;
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}