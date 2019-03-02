<?php
/**
 * @filename Router.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */


class Router
{

    protected $defultNameSpaceName = null;

    protected $defaultControllerName = 'index';

    protected $defaultActionName = 'index';

    protected $defaultParams = [];

    protected $namespaceName = null;

    protected $controllerName = null;

    protected $actionName = null;

    protected $params = [];

    protected $routes = [];


    protected $dispatcher;




    public function setDefaults(array $defaults)
    {
        if (isset($defaults['namespace'])) {
            $this->defaultNamespaceName = $defaults['namespace'];
        }
        if (isset($defaults['controller'])) {
            $this->defaultControllerName = $defaults['controller'];
        }
        if (isset($defaults['action'])) {
            $this->defaultActionName = $defaults['action'];
        }
        if (isset($defaults['params'])) {
            $this->defaultParams = $defaults['params'];
        }

        return $this;
    }


    public function getNamespaceName()
    {
        return $this->namespaceName ?? $this->defaultNamespaceName;
    }

    public function getControllerName()
    {
        return $this->controllerName ?? $this->defaultControllerName;
    }

    public function getActionName()
    {
        return $this->actionName ?? $this->defaultActionName;
    }

    public function getParams()
    {
        return $this->params ?? $this->defaultParams;
    }

}