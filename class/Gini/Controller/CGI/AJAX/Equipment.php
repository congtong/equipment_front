<?php

namespace Gini\Controller\CGI\AJAX;

use \Gini\CGI\Response;
use \Gini\CGI\Validator;

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
        $validator = new Validator;
        try {
            $except = ['ctime'];
            $validator
                ->validate('name', !!$form['name'], T('仪器名称必填'))
                ->validate('incharges', !!$form['incharges'], T('负责人必填'))
                ->validate('contacts', !!$form['contacts'], T('联系人必填'));
            if (isset($form['share']) && $form['share'] == 1 ) {
                $validator
                    ->validate('domain', !!$form['domain'], T('主要测试和研究领域必填'))
                    ->validate('refer_charge_rule', !!$form['refer_charge_rule'], T('参考收费标准必填'))
                    ->validate('open_calendar', !!$form['open_calendar'], T('开放机时安排必填'))
                    ->validate('assets_code', !!$form['assets_code'], T('固定资产分类编必填'))
                    ->validate('certification', !!$form['certification'], T('仪器认证情况必填'))
                    ->validate('classification_code', !!$form['classification_code'], T('共享分类编码必填'))
                    ->validate('manu_certification', !!$form['manu_certification'], T('生产厂商资质必填'))
                    ->validate('manu_country_code', !!$form['manu_country_code'], T('产地国别必填'))
                    ->validate('share_level', !!$form['share_level'], T('共享特色代码必填'));
            }
            $validator->done();
            $data = $this->rest->post($this->module, $form);
            if (is_array($data)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => T('保存成功'),
                ];
                return \Gini\IoC::construct('\Gini\CGI\Response\Redirect', 'equipment');
            }
        } catch (Validator\Exception $e) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => T('保存失败'),
            ];
            return \Gini\IoC::construct('\Gini\CGI\Response\Redirect');
        }
    }
    /**
     *  删除功能接口
     * 
     * @return json 删除成功返回success 删除失败返回error
     */
    public function actionDelete($id = 0) {
        $res = true;
        $validator = new Validator;
        try {
            $validator->validate('id', $id && is_numeric($id) ? true : false, T('id格式不正确'));
            $validator->done();
            $res = $this->rest->delete($this->module.'/'.$id);
        } catch (\Validator\Exception $e) {
            $res = false;
        }
        if ($res) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => T('删除成功'),
            ];
            return \Gini\IoC::construct('\Gini\CGI\Response\Redirect','equipment');
        } else {
            return \Gini\IoC::construct('\Gini\CGI\Response\Redirect');
        }
    }
}
