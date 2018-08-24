<?php

/**
 * ThoseIndexed：用于支持集合搜索查询远程sphinx的搜索结果.
 *
 * @author  Cheng Liu <cheng.liu@geneegroup.com>
 *
 * $products = thoseIndexed('product')
 *			->filter(['name'=>'乙醇', 'product'=>'百灵威'])
 *			->fetch(0,20);
 **/

namespace Gini\Module;

class ThoseIndexed extends \Gini\ORMIterator {

    private $_h;

    public function __construct($name)
    {
        $this->_h = \Gini\IoC::construct('\Gini\ORM\ThoseIndexed\\'.$name);
    }

    /**
    * 根据相关的搜索条件去远程rpc生成该类型搜索token，以便后续进行搜索查询.
    *
    * @return new ThoseIndexed
    *
    * @author Cheng Liu <cheng.liu@geneegroup.com>
    **/
    public function filter(array $criteria)
    {
        $this->_h->filter($criteria);

        return $this;
    }

    /**
    * 根据搜索token和查询条数信息通过rpc获取详细的类型数据信息.
    *
    * @return mixed [id => mix data, ...]
    *
    * @author Cheng Liu <cheng.liu@geneegroup.com>
    **/
    public function fetch($start = 0, $step = 20)
    {
        return $this->_h->fetch($start, $step);
    }

    public function total()
    {
        return $this->_h->total();
    }
}
