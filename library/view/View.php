<?php

// +----------------------------------------------------------------------
// | Library for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://library.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

namespace library\view;

use think\Db;
use think\db\Query;
use library\Controller;

/**
 * 基础视图管理器
 * Class View
 * @package library\view
 */
class View
{
    /**
     * 数据库操作对象
     * @var \think\db\Query
     */
    protected $db;

    /**
     * 当前操作控制器引用
     * @var \library\Controller
     */
    protected $class;

    /**
     * 当前请求对象
     * @var \think\Request
     */
    protected $request;

    /**
     * View constructor.
     * @param string|Query $dbQuery
     */
    public function __construct($dbQuery)
    {
        $this->request = app('request');
        $this->db = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
    }

    /**
     * 应用初始化
     * @param Controller $controller
     * @return mixed
     */
    public function apply($controller)
    {
        $this->class = $controller;
        if (method_exists($this, 'init')) {
            return $this->init();
        }
    }

}