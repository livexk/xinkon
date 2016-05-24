<?php
/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/4/8
 * Time: 14:21
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

namespace library;
class config
{
    static $config = false;
    static $path = [];

    static public function init()
    {
        if (self::$config === false) self::get_config();
    }

    private static function get_config()
    {
        static $_sys_ = [], $_com_ = [], $_app_ = [];
        //加载系统配置
        $sys = XINKON . DIR . 'common' . DIR . 'config.php';
        if (is_file($sys) && !in_array($sys, self::$path)) {
            self::$path[] = $sys;
            xinkon::set_classMap($sys);
            $_sys_ = include $sys;
        }
        //加载公共配置
        $com = ROOT . '/' . APP_ROOT . DIR . 'common/config.php';
        if (is_file($com) && !in_array($com, self::$path)) {
            self::$path[] = $com;
            xinkon::set_classMap($com);
            $_com_ = include $com;
        }
        //加载应用配置
        $app = ROOT . '/' . MODULE_PATH . '/config.php';
        if (is_file($app) && !in_array($app, self::$path)) {
            self::$path[] = $app;
            xinkon::set_classMap($app);
            $_app_ = include $app;
        }
        self::$config = array_merge((array)$_sys_, (array)$_com_, (array)$_app_);
    }


    static public function set($name, $value)
    {
        self::$config[$name] = $value;
        return true;
    }

    static public function get($name)
    {

        if (isset(self::$config[$name])) return self::$config[$name];
        return false;
    }

    static public function getAll()
    {
        return static::$config;
    }

}