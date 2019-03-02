<?php
/**
 * @filename Applicationplication.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */

namespace zi;

class Application extends Component     //被继承的时候应该自动被加载
{
    public function __construct()
    {

        parent::__construct();

        Zi::$app = $this;
        $this->init();
        $this->bootStrap(); //      启动应用
    }

    //提供一个用户初始化的接口
    public function init()
    {

    }


    //整个处理流程被包裹
    public function run() {

        $response = $this->handleRequest();

        $this->end($response);


    }

    protected function bootStrap()
    {
        //设置错误处理函数
        //register_error_handle
        //autoload



    }

    //处理请求
    public function handleRequest()
    {


    }

    //结束请求
    public function end($status, $response)
    {
        $response->send();
        exit($status);
    }



}