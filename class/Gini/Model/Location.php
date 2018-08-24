<?php

namespace Gini\Model;

class Location {
    /**
     * 获取位置的下拉框数组
     * 
     * @return array 返回下拉框数组
     */
    public static function getData() {
        $data = thoseIndexed('equipment')->fetch(0, $total);
        $location = array();
        if (count($data['data']) > 0) {
            foreach ($data['data'] as $v) {
                if ($v['location']) {
                    $location[] = $v['location'];
                }
            }
        }
        return array_unique($location);
    }
}
