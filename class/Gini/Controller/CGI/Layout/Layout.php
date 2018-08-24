<?php

namespace Gini\Controller\CGI\Layout;

abstract class Layout extends \Gini\Controller\CGI\Layout {

    protected $selected = null;
    
    function __preAction($action, &$params){
        $this->view = V('layout/layout');
    }

    function __postAction($action, &$params, $response) {
        $this->view->header = V('layout/header');
        $this->view->sidebar = V('layout/sidebar');
        return parent::__postAction($action, $params, $response);
    }
}
