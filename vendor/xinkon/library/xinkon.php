<?php
/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/04/04
 * Time: 17:21
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


class xinkon
{
    static $classMap = [];
    static $run_time = [];

    static public function start()
    {
        self::$run_time['start'] = microtime(true);
        self::$run_time['memory_start'] = memory_get_usage();
        spl_autoload_register('self::autoload'); //自动加载
        self::set_classMap(XINKON . DIR . "start.php");
        self::set_classMap(XINKON . DIR . "library" . DIR . "xinkon.php");
        self::set_classMap(XINKON . DIR . "common" . DIR . "define.php");
    }

    static function autoload($class_name)
    {
        $class_name = strtolower($class_name);
        //框架内 //主应用
        $class_name_ = XINKON . DIR . $class_name . ".php";
        $class_name_ = str_replace("\\", DIR, $class_name_);
        if (is_file($class_name_)) {
            if (in_array($class_name_, self::$classMap)) {
                return true;
            } else {
                self::set_classMap($class_name_);
                include $class_name_;
                return true;
            }
        }
        //应用 //
        $class_name_ = ROOT . '/' . $class_name . ".php";
        $class_name_ = str_replace("\\", DIR, $class_name_);
        if (is_file($class_name_)) {
            if (in_array($class_name_, self::$classMap)) {
                return true;
            } else {
                self::set_classMap($class_name_);
                include $class_name_;
                return true;
            }
        }
    }

    static public function get()
    {
        return self::$classMap;
    }

    static public function set_classMap($value)
    {
        if (DE_BUG) {
            self::$classMap[] = $value;
        }
        return true;
    }
}