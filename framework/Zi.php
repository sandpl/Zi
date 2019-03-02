<?php
/**
 * @filename Zi.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-26
 */

namespace zi;
use zi\di\Container;

define("DS", DIRECTORY_SEPARATOR);

defined('ZI_BEGIN_TIME') or define('ZI_BEGIN_TIME', microtime(true));

defined('ZI_PATH') or define('ZI_PATH', __DIR__ . DS);   //获取的是源文件所在的路径
//echo ZI_PATH;die;


define("BASE_PATH", dirname(getcwd()) . DS);    //getcwd获得的是执行路径（index文件所在路径）

define("APP_PATH", BASE_PATH . 'application' . DS);

class Zi
{
    public static $classMap = [
        'zi\Application' => ZI_PATH .'Application.php',
        'zi\Component' => ZI_PATH .'Component.php',
        'zi\Router' => ZI_PATH .'Router.php',
        'zi\Controller' => ZI_PATH .'Controller.php',

        'zi\http\Request' => ZI_PATH . DS . 'Request.php',
        'zi\http\Response' => ZI_PATH . DS . 'Response.php',
        'zi\di\Container' => ZI_PATH .'Di' . DS . 'Container.php',
    ];

    public static $app;

    public static $container;

    //加载classMap中的函数
    public static function autoload($className) {
        //echo $className . "\n";
        if (isset(static::$classMap[$className])) {
            $classFile = static ::$classMap[$className];

            echo $classFile;
        } else {
            return;
        }
        require $classFile;

    }

    //对框架进行配置
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

}

spl_autoload_register(['zi\Zi', 'autoload'], true, true);   //注册自己的自动加载类
//在没有spl函数的作用下，类会不会被加载，以及类外的函数会不会起作用

//use引入的只是空间名称，真身并没有被引入
//php引入文件必须要走require 或 include
//万变不离其宗，不要被假象所迷惑
Zi::$container = new Container();


