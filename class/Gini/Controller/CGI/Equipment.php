<?php

namespace Gini\Controller\CGI;

use Gini\Model\Location;

class Equipment extends Layout\Layout {
    /**
     * 渲染页面body显示内容
     * 
     * @param int start 分页开始标记
     * @return view body显示内容
     */
    public function __index($start = 1) {
        $filter = [
            'sortby' => 'ctime',
            'order' => 'desc',
        ];
        $form = $this->form('get');
        //搜索条件整理
        if ($form) {
            foreach ($form as $key => $value) {
                if (isset($this->fields()[$key]) && $value && $key != 'atime') {
                    if (isset($this->fields()[$key]['operate'])) {
                        $filter[$key] = [$this->fields()[$key]['operate'], $value];
                    } else {
                        $filter[$key] = $value;
                    }
                }
            }
        }
        //针对日期控件做处理
        if (isset($form['atime']) && $form['atime'][0] =='' && $form['atime'][1] != '' ) {
            $filter['atime'] = ['le', $form['atime'][1]];
        } else if (isset($form['atime']) && $form['atime'][0] !='' && $form['atime'][1] == '') {
            $filter['atime'] = ['ge', $form['atime'][1]];
        } else if (isset($form['atime']) && $form['atime'][0] != '' && $form['atime'][1] != '') {
            $filter['atime'] = array_merge(['bt'], $form['atime']);
        }
        $_SESSION['filter'] = $filter;
        $equipment = thoseIndexed('equipment')->filter($filter); 
        $data = $equipment->fetch($start);
        $total = $equipment->total();
        
        $pagination = \Gini\Model\Widget::factory('pagination', [
            'uri' => "equipment",
            'total' => $total,
            'start' => $start,
            'form' => $form
        ]);

        $search = \Gini\Model\Widget::factory('search', [
            'form' => $form,
            'action' => "equipment",
            'method' => 'GET',
            'fields' => $this->fields()
        ]);

        $this->view->body = V('equipment/index', [
            'form' => $form,
            'pagination' => $pagination,
            'searchForm' => $search,
            'data' => $data['data']
        ]);
    }
    /**
     * 显示新增界面
     * 
     * @return view form表单显示的内容
     */
    public function actionAdd () {
        $this->view->body = V('equipment/form', [
            'fields' => \Gini\Config::get('equipment.form')
        ]);
    }
    /**
     * 显示编辑页面
     * 
     * @return view 显示编辑界面的内容
     */
    public function actionEdit($id = 0) {
        $rest = \Gini\ORM\ThoseIndexed\Object::getRest();
        $data = $rest->get('equipment/'.$id);
        $field = \Gini\Config::get('equipment.form');
        foreach ($field as $key => &$v) {
            if (isset($data[$key])) {
                $v['value'] = $data[$key];
            }
        }
        $field['id'] = [
            'title' => 'id',
            'type' => 'hidden',
            'value' => $id
        ];
        $this->view->body = V('equipment/form', [
            'fields' => $field
        ]);
    }
    /**
     * 打印方法
     * 
     * @return view 返回view视图
     */
    public function actionPrint() {
        $filter = $_SESSION['filter'];
        $data = thoseIndexed('equipment')->filter($filter)->fetch(0); 
        $this->view = V('equipment/print', [
            'data' => $data['data']
        ]);
    }
    /**
     * 导出方法
     * 
     * @return view 导出方法
     */
    public function actionExport () {
        $filter = $_SESSION['filter'];
        $data = thoseIndexed('equipment')->filter($filter)->fetch(0);
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);
        $active = $excel->getActiveSheet();
        
        $active->setCellValue('A1', '仪器名称');
        $active->setCellValue('B1', '仪器英文名称');
        $active->setCellValue('C1', '仪器型号');
        $active->setCellValue('D1', '放置地点');
        $active->setCellValue('E1', '制作国家');
        $active->setCellValue('F1', '生产厂家');
        
        $index = 2;
        foreach ($data['data'] as $v) {
            $active->setCellValue('A' . $index, H($v['name']));
            $active->setCellValue('B' . $index, H($v['en_name']));
            $active->setCellValue('C' . $index, H($v['mode_no']));
            $active->setCellValue('D' . $index, H($v['location']));
            $active->setCellValue('E' . $index, H($v['manu_at']));
            $active->setCellValue('F' . $index, H($v['manu_facturer']));
            $index ++;
        }

        header("Accept-Ranges:bytes");
        header("Content-type: text/xls");
        header('Content-Disposition: attachment; filename=' . time() . '.xls');
        header("Pragma:no-cache");
        header("Expires: 0");

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $writer->save('php://output');
        exit;
    }
    
    /**
     * 搜索需要的字段
     * 
     * @return array 返回搜索框需要的字段数组
     */
    protected function fields() {
        $data = \Gini\Config::get('equipment.search');
        $data['location']['values'] = Location::getData();
        return $data;
    }
    
}
