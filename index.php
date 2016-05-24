<?php
/**
 * Created by PhpStorm.
 * User: xinkon
 * Date: 16-5-22
 * Time: 下午2:53
 */
define("DE_BUG", true);
define("ROOT", __DIR__);
define("MODULE_LIST", 'web,admin');
define("DEFAULT_MODULE", 'web');
define('APP_ROOT','app');
include 'vendor/autoload.php';
include 'vendor/xinkon/start.php';
\library\route::run();          //运行
\library\debug::run();     //页面调试以及运行信息