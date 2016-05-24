<?php

/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/4/7
 * Time: 15:03
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

class log
{

    private static $drive = false;
    private static $config = false;
    private static $handel = false;

    static public function init()
    {
        if(is_resource(self::$handel)){
            return true;
        }
        $config = config::get("log");
        if (empty($config) || !is_array($config)) {
            throw new \Exception("没有日志配置信息");
        }

        switch ($config['log_type']) {
            case false;
                return;
            case  "file";
                self::$drive = "file";
                break;
            case  "db";
                self::$drive = "db";
                break;
            default;
                throw new \Exception("不支持的日志记录类型");
        }
        $config = $config['log_' . self::$drive];
        if (is_array($config) && !empty($config)) {
            self::$config = $config;
        } else {
            throw new \Exception("日志类型" . self::$drive . "配置信息有误");
        }
        if (self::$handel === false) {
            $class = ('library\\log\\') . self::$drive;
            self::$handel = new $class($config);
            return true;
        } else {
            return true;
        }
    }

    static public function write($msg, $type = "log")
    {
        self::init();
        if (is_array($msg)) $msg = serialize($msg);
        return self::$handel->save($msg, $type);
    }


}