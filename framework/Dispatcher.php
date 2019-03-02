<?php
/**
 * @filename Dispatcher.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */


class Dispatcher
{
    protected $namespaceName = null;
    protected $controllerName = null;
    protected $actionName = null;
    protected $params = null;


    protected $controllerSuffix = "Controller";



    public function __construct()
    {
        $this->params = [];
    }

    /**
     * @return array|null
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array|null $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return null
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param null $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return null
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param null $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return null
     */
    public function getNamespaceName()
    {
        return $this->namespaceName;
    }

    /**
     * @param null $namespaceName
     */
    public function setNamespaceName($namespaceName)
    {
        $this->namespaceName = $namespaceName;
    }

    public function dispatch()
    {
        $controllerName = $this->getNamespaceName() . ucfirst($this->getControllerName()) . $this->controllerSuffix;

        $actionName = $this->getActionName();

        $params = $this->params;


        //TODO 循环调度器 的实现

        if (!class_exists($this->controllerName)) {
            throw new Exception('没有找到类');
        }

        if (!is_callable($controllerName, $actionName)) {
            throw new Exception('没有找到方法');
        }

        $controller = new $controllerName ();


        $returnResponse = call_user_func_array([$controller, $actionName], $params);

        return $returnResponse;

    }


}