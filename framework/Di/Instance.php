<?php
/**
 * @filename Instance.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-26
 */


namespace zi\di;


class Instance
{
    // 仅有的属性，用于保存类名、接口名或者别名
    public $id;

    protected function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param $id
     * @return Instance
     */
    public static function of($id)
    {
        //return new self($id); 这里new self如果被其它类继承， 取得的仍然是父类
        return new static($id); //new static 谁调用返回的就是谁， 相当于延迟绑定
    }

    //确保获取到的内容，是对象
    public static function ensure()
    {

    }





}