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
            'fields' => $this->saveField()
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
        $field = $this->saveField();
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

    public function actionPrint(){
        $data = thoseIndexed('equipment')->fetch(0); 
        $this->view = V('equipment/print', [
            'data' => $data['data']
        ]);
    }
    public function actionExport () {
        
        $data = thoseIndexed('equipment')->fetch(0);
        require_once APP_PATH . '/vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);
        $active = $excel->getActiveSheet();
        
        $active->setCellValue('A1', '仪器名称');
        $active->setCellValue('B1', '仪器英文名称');
        $active->setCellValue('C1', '仪器型号');
        $active->setCellValue('D1', '放置地点');
        
        $index = 2;
        foreach ($data['data'] as $v) {
            $active->setCellValue('A' . $index, H($v['name']));
            $active->setCellValue('B' . $index, H($v['en_name']));
            $active->setCellValue('C' . $index, H($v['en_name']));
            $active->setCellValue('D' . $index, H($v['en_name']));
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
     * 获取放置位置数据
     * 
     * @return array 返回下拉框列表数据
     */
    public function getLocation() { 
        return Location::getData();
    }
    /**
     * 表单需要的字段
     * 
     * @return array 返回需要显示的字段数组
     */
    protected function saveField() {
        return [
            'name' => [
                'title' => '仪器名称',
                'type' => 'text',
                'require' => true,
                'value' => '',
            ],
            'en_name' => [
                'title' => '英文名称',
                'type' => 'text',
                'require' => false,
                'value' => '',
            ],
            'mode_no' => [
                'title' => '型号',
                'type' => 'text',
                'require' => false,
                'value' => '',
            ],
            'specification' => [
                'title' => '规格',
                'type' => 'text',
                'require' => false,
                'value' => '',
            ],
            'manu_at' => [
                'title' => '制作国家',
                'type' => 'text',
                'require' => false,
                'value' => '',
            ],
            'manu_facturer' => [
                'title' => '生产厂家',
                'type' => 'text',
                'require' => false,
                'value' => '',
            ],
            'location' => [
                'title' => '放置地点',
                'type' => 'text',
                'requoire' => false,
                'value' => '',
            ],
            'tech_specs' => [
                'title' => '主要规格及技术指标',
                'type' => 'textarea',
                'require' => false,
                'value' => '',
            ],
            'incharges' => [
                'title' => '负责人',
                'type' => 'select',
                'require' => true,
                'options' => $this->peopoleOptions(),
                'value' => '',
            ],
            'contacts' => [
                'title' => '联系人',
                'type' => 'select',
                'require' => true,
                'options' => $this->peopoleOptions(),
                'value' => '',
            ],
            'school_level' => [
                'title' => '校级设备',
                'type' => 'radio',
                'options' => $this->optionsRaido(),
                'require' => true,
                'value' => '',
            ],
            'yiqikong_share' => [
                'title' => '进驻仪器控',
                'type' => 'radio',
                'options' => $this->optionsRaido(),
                'require' => true,
                'value' => '',
            ]
        ];
    }
    /**
     * radio需要的测试数据无相关表设计
     * 
     * @return array 返回选项数组
     */
    protected function optionsRaido() {
        return [
            1 => '是',
            0 => '否'
        ];
    }
    /**
     * select需要的测试数据，无人员表的设计
     * 
     * @return array 返回选项数组
     */
    protected function peopoleOptions() {
        return [
            '1' => '用户1',
            '2' => '用户2',
        ];
    }
    /**
     * 搜索需要的字段
     * 
     * @return array 返回搜索框需要的字段数组
     */
    protected function fields() {
        return [
            'name' => [
                'title' => '仪器名称',
                'operate' => 'li',
                'type'  => 'text'
            ],
            'ref_no' => [
                'title' => '仪器编号',
                'type' => 'text'
            ],
            'group' => [
                'title' => '组织机构',
                'type'  => 'text'
            ],
            'location' => [
                'title' => '放置位置',
                'type'  => 'select',
                'values' => $this->getLocation()
            ],
            'atime' => [
                'title' => '入网时间',
                'operate' => 'bt',
                'type'  => 'time'
            ]
        ];
    }
    
}
