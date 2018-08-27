<?php

namespace Gini\CGI\Response;

class Redirect implements \Gini\CGI\Response
{
    private $_content;

    public function __construct($content = null)
    {
        $this->_content = $content;
    }

    public function output()
    {
        header('Content-Type: text/javascript');
        if ($this->_content) {
            file_put_contents('php://output', 
            'window.location.replace("' .(string) $this->_content . '")');
        }
        else {
            file_put_contents('php://output', 
            'window.location.reload()');
        }
    }

    public function content()
    {
        return $this->_content;
    }
}
