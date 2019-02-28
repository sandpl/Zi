<?php
/**
 * @filename Component.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-26
 */

namespace zi;
class Component
{
    public function __construct($config = [])
    {
        if (!empty($config)) {
            Zi::configure($this, $config);
        }
        $this->init();
    }

    public function init()
    {

    }


}