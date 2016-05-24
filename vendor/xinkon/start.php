<?php
/**
 * Created by PhpStorm.
 * User: xinkon
 * Date: 16-5-3
 * Time: 下午7:59
 */
define("XINKON", __DIR__);

require 'library/xinkon.php';
require 'common/define.php';
\library\xinkon::start();      //开始并注册自动加载
\library\error::init();        //注册报错
\library\route::init();        //路由
\library\config::init();       //加载config

