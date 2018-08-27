<?php

namespace Gini\Module {

    class Eqfront {

        static function setup() {
            date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');

            setlocale(LC_MONETARY, \Gini\Config::get('system.locale') ?: 'zh_CN');
            \Gini\I18N::setup();
        }
    }
}

namespace {

    if (function_exists('thoseIndexed')) {
        die('thoseIndexed() was declared by other libraries, which may cause problems!');
    } else {
        function thoseIndexed($name) {
            return \Gini\IoC::construct('\Gini\Module\ThoseIndexed', $name);
        }
    }
    
}
