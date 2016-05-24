<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace library;


use library\exception\errorstart;

class error
{
    static public $notice_data = [];

    /**
     * 注册异常处理
     * @return void
     */
    public static function init()
    {
        error_reporting(0);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }


    /**
     * Exception Handler
     * @param  \Exception $exception
     * @return bool  true-禁止往下传播已处理过的异常
     */
    public static function appException($exception)
    {

        // 收集异常数据
        // 调试模式，获取详细的错误信息
        $data = [
            'name' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
//            'trace' => $exception->getTrace(),
            /*       'code' => self::getCode($exception),
                 'source' => self::getSourceCode($exception),

                 'tables' => [
                     'GET Data' => $_GET,
                     'POST Data' => $_POST,
                     'Files' => $_FILES,
                     'Cookies' => $_COOKIE,
                     'Session' => isset($_SESSION) ? $_SESSION : [],
                     'Server/Request Data' => $_SERVER,
                     'Environment Variables' => $_ENV,
                 ],*/
        ];
        // 输出错误信息
        self::output($exception, $data);
        // 禁止往下传播已处理过的异常
        return true;
    }

    /**
     * Error Handler
     * @param  integer $errno 错误编号
     * @param  integer $errstr 详细错误信息
     * @param  string $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @return bool  true-禁止往下传播已处理过的异常
     */
    public static function appError($errno, $errstr, $errfile = null, $errline = 0, array $errcontext = [])
    {
        // 将错误信息托管至 think\exception\ErrorException
        if (in_array($errno, [E_NOTICE])) {
            $vars = [
                'name' => "appError",
                'file' => $errfile,
                'line' => $errline,
                'message' => $errstr,
                'code' => $errno,
            ];
            $str = '错误名:' . $vars['name'] . ' , 错误文件:' . $vars['file'] . ' , 错误信息:' . $vars['message'] . ' , 错误行号:' . $vars['line'];
//            log::write($str,"NOTICE");
        }
        $exception = new errorstart($errno, $errstr, $errfile, $errline, $errcontext);
        self::appException($exception);
        // 禁止往下传播已处理过的异常
        return true;
    }

    /**
     * Shutdown Handler
     * @return bool true-禁止往下传播已处理过的异常; false-未处理的异常继续传播
     */
    public static function appShutdown()
    {

        if ($error = error_get_last()) {
            // 将错误信息托管至think\ErrorException
            $exception = new errorstart(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );

            /**
             * Shutdown handler 中的异常将不被往下传播
             * 所以，这里我们必须手动传播而不能像 Error handler 中那样 throw
             */

            self::appException($exception);
            // 禁止往下传播已处理过的异常
            return true;
        }
        return false;
    }

    /**
     * 输出异常信息
     * @param  \Exception $exception
     * @param  Array $vars 异常信息
     * @return void
     */
    public static function output($exception, array $vars)
    {
        http_response_code(500);
        if (DE_BUG) {
            if (empty($vars)) {
                echo "没有错误信息";
                return;
            }
            $str = '<div style="background: #999;padding: 5px;font-size: 13px;color: #eee"><hr>系统严重报错:<br>';

            $str .= '错误名:' . $vars['name'] . ' , 错误文件:' . $vars['file'] . ' , 错误信息:' . $vars['message'] . ' , 错误行号:' . $vars['line'] . '<br>';
            $str .= "</div>";
            echo $str;
            die;
        } else {
            $str = '<div style="background: #999;padding: 5px;font-size: 13px;color: #eee"><hr>系统严重报错:<br>';
            $str .= "程序出错了";
            $str .= "</div>";
            echo $str;
        }

//        log::write($str,"ERROR");

    }


}
