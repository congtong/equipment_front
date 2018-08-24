<?php

namespace Gini\ORM\ThoseIndexed;

class Object {
    private $_name;
    private $_tableName;
    protected $_total;
    protected static $_REST = [];
    protected $_criteria = [];
    public static function getRest($type = 'equipment') {
        if (!isset(self::$_REST[$type])) {
            try {
                $remote = \Gini\Config::get('server.remote');
                $api = $remote['api'];
                $rest = \Gini\REST::of($api);
                self::$_REST[$type] = $rest;
            } catch (\Gini\Rest\Exception $e) {
                die('rest init error');
            }
        }
        return self::$_REST[$type];
    }

    public function filter (array $criteria)
    {
        $this->_criteria = $criteria;
    }

    public function fetch ($start = 1, $step = 20)
    {
        $rest = $this->getREST();
		if (!$rest) return [];
        if ($start && $step) {
            $this->_criteria['limit'] = [($start - 1) * $step, $step];
        }
		try {
            $name = $this->name();
            $objects = $rest->get($name, $this->_criteria);
            $this->_total = $objects['total'] ? : 0;
            unset($objects['total']);
			return $objects;
		} catch (\Gini\REST\Exception $e) {
			return [];
		}
    }

    public function total () {
        if ($this->_total) {
            return $this->_total;
        }
        else {
            $this->fetch();
            return $this->_total;
        }
    }

    public function ormRelations() {
        return false;
    }

    public function structure () {
        return false;
    }

    public function db () {
        return false;
    }

    public function adjustTable() {
        return false;
    }

    public function name ()
    {
        if (!isset($this->_name)) {
            $this->_prepareName();
        }
        return $this->_name;
    }

    private function _prepareName ()
    {
        // remove Gini/ORM/ThoseIndexed
        list(, , , $name) = explode('/', str_replace('\\', '/', strtolower(get_class($this))), 4);
        $this->_name = $name;
        $this->_tableName = str_replace('/', '_', $name);
    }

    public function manyStructure ()
    {
        return false;
    }

}
