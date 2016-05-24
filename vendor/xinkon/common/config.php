<?php
/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/4/7
 * Time: 16:47
 *
 *                *****************************************************
 *                #                                                   #
 *                #                       _oo0oo_                     #
 *                #                      o8888888o                    #
 *                #                      88" . "88                    #
 *                #                      (| -_- |)                    #
 *                #                      0\  =  /0                    #
 *                #                    ___/`---'\___                  #
 *                #                  .' \\|     |# '.                 #
 *                #                 / \\|||  :  |||# \                #
 *                #                / _||||| -:- |||||- \              #
 *                #               |   | \\\  -  #/ |   |              #
 *                #               | \_|  ''\---/''  |_/ |             #
 *                #               \  .-\__  '-'  ___/-. /             #
 *                #             ___'. .'  /--.--\  `. .'___           #
 *                #          ."" '<  `.___\_<|>_/___.' >' "".         #
 *                #         | | :  `- \`.;`\ _ /`;.`/ - ` : | |       #
 *                #         \  \ `_.   \_ __\ /__ _/   .-` /  /       #
 *                #     =====`-.____`.___ \_____/___.-`___.-'=====    #
 *                #                       `=---='                     #
 *                #     ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~   #
 *                #             佛祖保佑             永无BUG          #
 *                #---------------------------------------------------#
 *                #          佛曰:                                    #
 *                #             写字楼里写字间，写字间里程序员；      #
 *                #             程序人员写程序，又拿程序换酒钱。      #
 *                #             酒醒只在网上坐，酒醉还来网下眠；      #
 *                #             酒醉酒醒日复日，网上网下年复年。      #
 *                #             但愿老死电脑间，不愿鞠躬老板前；      #
 *                #             奔驰宝马贵者趣，公交自行程序员。      #
 *                #             别人笑我忒疯癫，我笑自己命太贱；      #
 *                #             不见满街漂亮妹，哪个归得程序员？      #
 *                *****************************************************
 * 
//
*/
return [

    'route' => [
        'default_controller' => 'index',
        'default_method' => 'index',
        'bind_controller' => '',  //绑定控制器(谨慎设置)
        'bind_method' => '',       //绑定方法(谨慎设置)
    ],

    //数据库(只能用pdo连接)
    'db' => [
        // 数据库类型
        'type' => 'mysql',
        //数据库名
        'dbname' => "livexk",
        // 服务器地址
        'hostname' => 'localhost',
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => 'root',
        // 数据库连接端口
        'hostport' => '3306',
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => '',
        //是否长链接
        "connect" => true,
        // 数据库调试模式
        'debug' => DE_BUG,
    ],

    //模板配置
    'template' => [
        'view_path' => APP_ROOT . '/' . MODULE . '/view/', // 模板路径
        'cache_path' => 'runtime/cache/', // 缓存路径
        'view_suffix' => '.html', // 默认模板文件后缀
        'cache_suffix' => '.php', // 默认模板缓存后缀
        'tpl_deny_func_list' => 'echo,exit', // 模板引擎禁用函数
        'tpl_deny_php' => false, // 默认模板引擎是否禁用PHP原生代码
        'tpl_begin' => '{', // 模板引擎普通标签开始标记
        'tpl_end' => '}', // 模板引擎普通标签结束标记
        'strip_space' => false, // 是否去除模板文件里面的html空格与换行
        'tpl_cache' => true, // 是否开启模板编译缓存,设为false则每次都会重新编译
        'compile_type' => 'file', // 模板编译类型
        'cache_prefix' => '', // 模板缓存前缀标识，可以动态改变
        'cache_time' => 0, // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
        'cache_record_file' => 'cache_record_file', // 记录模板更新时间的文件
        'layout_on' => false, // 布局模板开关
        'layout_name' => 'layout', // 布局模板入口文件
        'layout_item' => '{__CONTENT__}', // 布局模板的内容替换标识
        'taglib_begin' => '{', // 标签库标签开始标记
        'taglib_end' => '}', // 标签库标签结束标记
        'taglib_load' => true, // 是否使用内置标签库之外的其它标签库，默认自动检测
        'taglib_build_in' => 'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
        'taglib_pre_load' => '', // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
        'display_cache' => false, // 模板渲染缓存
        'cache_id' => '', // 模板缓存ID
        'tpl_replace_string' => [],
        'tpl_var_identify' => 'array', // .语法变量识别，array|object|'', 为空时自动识别
        'template'=>'default'
    ],

    //写日志配置文件
    'log' => [
        'log_type' => "file",  //日志类型 file文件形式  db数据库形式 false 不记录日志
        'log_db' => [            //日志为数据库形式时配置
            'db_name' => '',         //数据库名称(不带数据库前缀,会自动追加前缀)
        ],
        'log_file' => [           //日志为文件形式配置
            '_dir' => ROOT . '/runtime/log/',        //日志目录
            '_log_size' => 1024 * 1024 * 2,         //单个日志大小
            '_log_fix' => '.log'          //日志文件后缀
        ],
    ],

    //缓存
    'cache' => [
        'cache_type' => 'redis',   //缓存类型 file 文件形式   redis / memcached
        'cache_file' => [           //缓存为文件形式配置
            '_dir' => ROOT . '/runtime/cache/',        //缓存目录
            '_log_fix' => '.log'          //文件后缀
        ],
        'cache_redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'timeout' => false,
            'expire' => false,
            'persistent' => false,
            'length' => 0,
            'prefix' => '',
        ]
    ],

    //session 储存
    'session' => [
        'id' => '',
        'var_session_id' => '', // SESSION_ID的提交变量,解决flash上传跨域
        'prefix' => 'xinkon',
        'type' => 'redis',  //缓存类型 file 文件形式   redis / memcached ()
        'auto_start' => true,

        'session_file' => [    //type 等于file

        ],
        'session_redis' => [    //类型为redis
            'host' => '127.0.0.1',  // redis主机
            'port' => 6379,         // redis端口
            'password' => 'xinkon',           // 密码
            'expire' => 3600,         // 有效期(秒)
            'timeout' => 0,            // 超时时间(秒)
            'persistent' => true,         // 是否长连接
            'session_name' => '',           // sessionkey前缀
        ],
        'session_memcached' => [ //类型为 memcached
            'host' => '127.0.0.1', // memcache主机
            'port' => 1121, // memcache端口
            'expire' => 3600, // session有效期
            'timeout' => 0, // 连接超时时间（单位：毫秒）
            'session_name' => '', // memcache key前缀
        ],
    ],


];
