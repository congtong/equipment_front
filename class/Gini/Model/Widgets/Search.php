<?php

namespace Gini\Model\Widgets;

class Search extends \Gini\View {

    function __construct($vars = NULL) {
        foreach ($vars['fields'] as $key => &$field) {
            if ($vars['form'][$key]) {
                $field['value'] = $vars['form'][$key];
            }
        }
        parent::__construct('widgets/search', $vars);
    }
}
