<?php

namespace Gini\Controller\CGI\AJAX;

use \Gini\CGI\Response;

class Equipment extends \Gini\Controller\CGI {

    public $module;
    /**
     *  初始化参数
     */
    public function __construct() {
        $remote = \Gini\Config::get('server.remote');
        $this->module = $remote['module'];
        $this->rest = \Gini\ORM\ThoseIndexed\Object::getRest();
    }
    /**
     *  表单的保存功能包括新增和编辑
     * 
     * @return json 成功返回success 失败返回error
     */
    public function actionSave() {
        $response['result'] = 'error';
        $form = $this->form('post');
        $data = $this->rest->post($this->module, $form);
        if (is_array($data)) {
            $response['result'] = 'success';
        }
        return new Response\JSON($response, $code);
    }
    /**
     *  删除功能接口
     * 
     * @return json 删除成功返回success 删除失败返回error
     */
    public function actionDelete($id = 0) {
        $response['result'] = 'error';
        $res = $this->rest->delete($this->module.'/'.$id);
        if ($res) {
            $response['result'] = 'success';
        }             
        return new Response\JSON($response, $code);
    }
}
