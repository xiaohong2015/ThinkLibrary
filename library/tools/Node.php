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
// | gitee 开源项目：https://gitee.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

namespace library\tools;

/**
 * 控制器节点管理器
 * Class Node
 * @package library\tools
 */
class Node
{

    /**
     * 获取当前访问节点
     * @return string
     */
    public static function getCurrentNode()
    {
        $request = request();
        list($module, $controller, $action) = [
            $request->module(), $request->controller(), $request->action(),
        ];
        return self::parseControllerNodeString("{$module}/{$controller}") . '/' . strtolower($action);
    }

    /**
     * 获取节点列表
     * @param string $dir 控制器根路径
     * @param array $nodes 额外数据
     * @return array
     */
    public static function getNodeTree($dir, $nodes = [])
    {
        foreach (self::scanDirFile($dir) as $file) {
            list($matches, $filename) = [[], str_replace(DIRECTORY_SEPARATOR, '/', $file)];
            if (!preg_match('|/(\w+)/controller/(.+)|', $filename, $matches)) {
                continue;
            }
            $className = env('app_namespace') . str_replace('/', '\\', substr($matches[0], 0, -4));
            if (class_exists($className)) {
                foreach (get_class_methods($className) as $funcName) {
                    if (strpos($funcName, '_') === 0 || $funcName === 'initialize') {
                        continue;
                    }
                    $controller = str_replace('/', '.', substr($matches[2], 0, -4));
                    $nodes[] = self::parseControllerNodeString("{$matches[1]}/{$controller}") . '/' . strtolower($funcName);
                }
            }
        }
        return $nodes;
    }

    /**
     * 驼峰转下划线规则
     * @param string $node 节点名称
     * @return string
     */
    public static function parseControllerNodeString($node)
    {
        $nodes = [];
        foreach (explode('/', $node) as $str) {
            $dots = [];
            foreach (explode('.', $str) as $dot) {
                $dots[] = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_"));
            }
            $nodes[] = join('.', $dots);
        }
        return trim(join('/', $nodes), '/');
    }

    /**
     * 获取所有PHP文件
     * @param string $dir 目录
     * @param array $data 额外数据
     * @param string $ext 有文件后缀
     * @return array
     */
    public static function scanDirFile($dir, $data = [], $ext = 'php')
    {
        foreach (scandir($dir) as $_dir) {
            if (strpos($_dir, '.') !== 0) {
                $tmpPath = realpath($dir . DIRECTORY_SEPARATOR . $_dir);
                if (is_dir($tmpPath)) {
                    $data = array_merge($data, self::scanDirFile($tmpPath));
                } elseif (pathinfo($tmpPath, 4) === $ext) {
                    $data[] = $tmpPath;
                }
            }
        }
        return $data;
    }

}