<?php
/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/04/04
 * Time: 17:24
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


class route
{
    static $route = [
        "module" => DEFAULT_MODULE,
        "controller" => '',
        "method" => '',
    ];

    static public function init()
    {
        if (!isset($_GET['r'])) {
            $data = self::$route;
        } else {
            $data = explode('/', $_GET['r']);
        }
        if (count($data) > 3) throw new \Exception("连接有误");
        self::$route['module'] = empty($data[0]) ? self::$route['module'] : $data[0];
        self::$route['controller'] = empty($data[1]) ? self::$route['controller'] : $data[1];
        self::$route['method'] = empty($data[2]) ? self::$route['method'] : $data[2];

        $module_list = explode(',', MODULE_LIST);

        if (!in_array(self::$route['module'], $module_list)) {
            throw new \Exception(self::$route['module'] . "模块不存在");
        }
        self::define();
    }

    static private function define()
    {
        define("MODULE", self::$route['module']);
        define("MODULE_PATH", APP_ROOT . DIR . self::$route['module']);
        if (!empty(self::$route['controller'])) define("CONTROLLER", self::$route['controller']);
        if (!empty(self::$route['method'])) define("METHOD", self::$route['method']);
    }

    static private function check_route()
    {
        $route = config::get("route");
        if (empty(self::$route['controller'])) {
            self::$route['controller'] = $route['default_controller'];
            define("CONTROLLER", self::$route['controller']);
        }
        if (empty(self::$route['method'])) {
            self::$route['method'] = $route['default_method'];
            define("METHOD", self::$route['method']);
        }
    }


    static public function run()
    {
        self::check_route();
        if (!file_exists(MODULE_PATH . '/controllers')) {
            mkdir(MODULE_PATH . '/controllers', 0777, true);
        }
        $controller_path = MODULE_PATH . '/controllers/' . self::$route['controller'] . ".php";
        $controller_path = str_replace("\\", DIR, $controller_path);
        if (!file_exists($controller_path)) {
            throw   new \Exception($controller_path . "文件不存在");
        }
        $namespace = MODULE_PATH . '\\' . "controllers\\" . self::$route['controller'];
        $namespace = str_replace("/", '\\', $namespace);

        if (!class_exists($namespace)) {
            throw   new \Exception($namespace . "方法不存在");
        }
        $class = new $namespace;
        $method = self::$route['method'];
        $reflect = new \ReflectionMethod($class, $method);

        //绑定参数
        $tem = [];
        if ($reflect->getNumberOfParameters() > 0) {
            foreach ($reflect->getParameters() as $v) {
                foreach ($v as $vv) {
                    $result = self::check_str($_REQUEST[$vv]);
                    if ($result !== false) {
                        $tem[$vv] = $result;
                    }
                }
            }
        }
        $reflect->invokeArgs(isset($class) ? $class : null, $tem);
    }


    static private function html($data)
    {
        foreach ($data as $k => &$value) {
            $value = static::check_str($value);
        }
        return $data;
    }


    static private function filter_keyword($string)
    {
        $keyword = 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile';
        $arr = explode('|', $keyword);
        $result = str_ireplace($arr, '', $string);
        return $result;
    }

    static protected function check_id($id)
    {
        $result = false;
        if ($id !== '' && !is_null($id)) {
            $var = static::filter_keyword($id); // 过滤sql与php文件操作的关键字
            if ($var !== '' && !is_null($var) && is_numeric($var)) {
                $result = intval($var);
            }
        }
        return $result;
    }

    /**
     * 检查输入的字符是否合法，合法返回对应id，否则返回false
     * @param string $string
     * @return mixed
     */
    static protected function check_str(&$string)
    {
        $result = false;
        $var = self::filter_keyword($string); // 过滤sql与php文件操作的关键字
        if (!empty($var)) {
            if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
                $var = addslashes($string); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
            }
//$var = str_replace( "_", "\_", $var ); // 把 '_'过滤掉
            $var = str_replace("%", "\%", $var); // 把 '%'过滤掉
            $var = nl2br($var); // 回车转换
            $var = htmlspecialchars($var); // html标记转换
            $result = $var;
        }
        return $result;
    }

}