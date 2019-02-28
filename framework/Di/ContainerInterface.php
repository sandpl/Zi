<?php
/**
 * @filename ContainerInterface.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */

namespace zi\di;

interface ContainerInterface
{
    /**
     * 注册一个服务到容器
     * @param $id
     * @param $definition
     * @param $shared
     * @return mixed
     */
    public function set($id, $definition, $shared);


    /**
     * 从容器中获取一个服务
     * 当传入未注册为服务标识的类名时，自动将类名注册为服务，并返回类实例
     * @param $id
     * @return mixed
     */
    public function get($id);


    /**
     * 为服务添加别名
     * @param $alias
     * @param $abstract
     * @return mixed
     */
    public function alias($alias, $abstract);

    /**
     * 查询容器中是否存在某个服务
     * @param $id
     * @return mixed
     */
    public function has($id);

    /**
     * 从容器中删除一个服务
     * @param $id
     * @return mixed
     */
    public function remove($id);

    /**
     * 清空容器
     * @return mixed
     */
    public function clear();
}