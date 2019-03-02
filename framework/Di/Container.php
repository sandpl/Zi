<?php
/**
 * @filename Container.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */

namespace zi\di;

use zi\Component;
use ReflectionClass;

class Container extends Component
{

    /**
     * @var 用于保存单例对象
     * ['类名|接口名|别名' => '类的实例|null']
     */
    private $singletons = [];

    /**
     * @var array 用来保存构造函数的参数，以对象类型为键
     * ['类名|接口名|别名' => [参数数组]]
     */
    private $params = [];

    /**
     * @var array 用来保存依赖的定义
     * ['类名|接口名|别名' => 'callable|含有class键的数组']
     */
    private $definitions = [];

    /**
     * @var array 用于缓存reflectionClass
     * ['类名|接口名|别名' => 'reflectionClass实例']
     */
    private $reflections = [];

    /**
     * @var array 用于缓存依赖信息
     * ['类名|接口名|别名' => ' 依赖实例 ']
     */
    private $dependencies = [];


    public function get($id, $params = [], $config = [])
    {
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        } elseif (!isset($this->definitions[$id])) {    //没有注册直接返回
            return $this->build($id, $params, $config);
        }

        // 注意这里创建了 $_definitions[$class] 数组的副本
        $definition = $this->definitions[$id];
        if (is_callable($definition, true)) {
            $params = $this->resolveDependencies($id);
            $object = call_user_func($definition, $this, $params, $config);
        } elseif (is_array($definition)) {
            $concrete = $definition['class'];
            unset($definition['class']);

            $config = array_merge($definition, $config);
            $params = $this->mergeParams($id, $params);

            if ($concrete === $id) {
                //递归终止的必要条件
                $object = $this->build($id, $params, $config);
            } else {
                //递归
                $object = $this->get($concrete, $params, $config);
            }
        } elseif (is_object($definition)) {
            return $this->singletons[$id] = $definition;    //对象保存为单例
        } else {
            throw new \Exception('Unexpected object definition type: ' . gettype($definition));
        }

        if (array_key_exists($id, $this->singletons)) { //如果是单例则保存为对象
            // singleton
            $this->singletons[$id] = $object;
        }

        return $object;
    }

    protected function mergeParams($class, $params)
    {
        if (empty($this->params[$class])) {
            return $params;
        } elseif (empty($params)) {
            return $this->params[$class];
        }

        $ps = $this->params[$class];
        foreach ($params as $index => $value) {
            $ps[$index] = $value;
        }

        return $ps;
    }


    /**
     *
     * // register a class name as is. This can be skipped.
     * $container->set('zi\db\Connection');
     *
     * // register an interface
     * // When a class depends on the interface, the corresponding class
     * // will be instantiated as the dependent object
     * $container->set('zi\mail\MailInterface', 'zi\swiftmailer\Mailer');
     *
     * // register an alias name. You can use $container->get('foo')
     * // to create an instance of Connection
     * $container->set('foo', 'zi\db\Connection');
     *
     * // register a class with configuration. The configuration
     * // will be applied when the class is instantiated by get()
     * $container->set('zi\db\Connection', [
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     * // register an alias name with class configuration
     * // In this case, a "class" element is required to specify the class
     * $container->set('db', [
     *     'class' => 'zi\db\Connection',
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // register a PHP callable
     * // The callable will be executed when $container->get('db') is called
     * $container->set('db', function ($container, $params, $config) {
     *     return new \zi\db\Connection($config);
     * });
     * @param $id
     * @param $definition
     * @param $params
     * @return
     */
    public function set($id, $definition = [], array $params = [])
    {
        $this->definitions[$id] = $this->normalizeDefinition($id, $definition);
        $this->params[$id] = $params;
        unset($this->singletons[$id]);
        return $this;
    }

    public function setSingleton($id, $definition = [], array $params = [])
    {
        $this->definitions[$id] = $this->normalizeDefinition($id, $definition);
        $this->params[$id] = $params;
        $this->singletons[$id] = null;
        return $this;

    }

    /**
     * definition 的最终格式
     * [
     *     'class' => 'zi\db\Connection',      必须有
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]
     * @param $id
     * @param $definition
     * @return array
     */
    protected function normalizeDefinition($id, $definition)
    {
        if (empty($definition)) {
            return ['class' => $id];
        } elseif (isstring($definition)) {
            return ['class' => $definition];
        } elseif (is_callable($definition, true) || is_object($definition)) {
            return $definition;
        } elseif (is_array($definition)) {
            if (!isset($definition['class'])) {
                if (strpos($id, '\\') !== false) {
                    $definition['class'] = $id;
                } else {
                    //throw new InvalidConfigException('A class definition requires a "class" member.');
                }
            }
            return $definition;
        }
        //throw new InvalidConfigException("Unsupported definition type for \"$class\": " . gettype($definition));
    }

    public function has($id)
    {
        return isset($this->definitions[$id]);
    }

    public function clear()
    {
        $this->definitions = $this->params = $this->singletons = [];
    }

    public function remove($id)
    {
        unset($this->definitions[$id], $this->singletons[$id], $this->params[$id]);
    }

    /**
     * 获取一个类的反射和依赖
     * @param $class
     * @return array
     * @throws \ReflectionException
     */
    public function getDependencies($class)
    {
        if (isset($this->reflections[$class])) {
            return [$this->reflections[$class], $this->dependencies[$class]];
        }
        $dependencies = [];
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $param) {
                if ($param->isDefaultValueAvailable()) {
                    //构造函数有默认值了，将默认值作为依赖，既然是默认值，那肯定就是简单类型了
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    $c = $param->getClass();
                    $dependencies[] = Instance::of(null === $c ?: $c->getName());
                }
            }
        }
        $this->reflections[$class] = $reflection;
        $this->dependencies[$class] = $dependencies;
        return [$reflection, $dependencies];

    }

    /**
     * 解析依赖， 返回格式为 __construct 的参数数组
     * @param $dependencies
     * @return mixed
     */
    protected function resolveDependencies($dependencies)
    {
        foreach ($dependencies as $index => $dependency) {
            if ($dependency instanceof Instance) {
                //向容器索要所依赖的实例，递归调用 Z\di\Container::get()
                $dependencies[$index] = $this->get($dependency->id);    //id 为实例的唯一标识
            }
        }
        return $dependencies;
    }

    protected function build($class, $params, $config = [])
    {
        /* @var $reflection ReflectionClass */
        list($reflection, $dependencies) = $this->getDependencies($class);

        //用传入的 $params 的内容补充、覆盖到依赖信息中
        foreach ($params as $index => $param) {
            $dependencies[$index] = $param;
        }

        $dependencies = $this->resolveDependencies($dependencies);  //

        if (!$reflection->isInstantiable()) {
            throw new \Exception("不能实例化");
        }

        $object = $reflection->newInstanceArgs($dependencies);
        foreach ($config as $name => $value) {
            $object->$name = $value;
        }
        //TODO 对象满足一定的公共方法
        return $object;
    }

























    public function __construct()
    {
        echo "我被创建了";
    }






    /**
     * 为服务添加别名
     * @param $alias
     * @param $abstract
     * @return mixed
     */
    public function alias($alias, $abstract)
    {
        // TODO: Implement alias() method.
    }



}