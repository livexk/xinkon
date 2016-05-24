<?php
/**
 * Created by PhpStorm.
 * User: xinkon
 * Date: 16-5-4
 * Time: 下午10:45
 */
/**
 * 流程 先把要写日志数据保存成一个静态变量,当脚本停止时调用__destruct方法写入数据并关闭日志
 */
namespace library\log;

class file
{
    private static $path;
    private static $size;
    private static $fix;
    private static $filename;
    private static $handel;
    private static $content = [];


    /**
     * file constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        self::$path = $config['_dir'];
        self::$size = $config['_log_size'];
        self::$fix = $config['_log_fix'];
        if (!is_dir(self::$path)) {
                mkdir(self::$path,0777,true);
                throw  new \Exception("日志目录创建失败!,请检测权限");
        } else {
            if (!is_writable(self::$path)) {
                throw  new \Exception("日志不可写!,请检测权限,路径: ".self::$path);
            }
        }
        self::check_file();
    }

    /**
     *检测并打开文件
     * @return bool
     */
    public static function check_file()
    {
        clearstatcache();
        $tem = date("Y-m-d");
        self::$filename = $tem . self::$fix;
        $file = self::$path . self::$filename;
        if (file_exists($file) && filesize($file) > self::$size) {
            rename($file, self::$path . $tem . '-' . rand(100, 999) .self::$fix) && touch($file);
            self::$handel = fopen($file, 'a+') or new \Exception("日志不可写!,请检测权限");
        }
        if (is_resource(self::$handel)) {
            return true;
        } else {
            self::$handel = fopen($file, 'a+');
            return true;
        }
    }

    public static function write()
    {
        $t = '日期:' . date('Y-m-d H:i:s') . "\n";
        foreach (self::$content as $k => $v) {
            $t .= '日志类型:' . $v['type'] . ' ; 详细: ' . $v['msg'] . "\n";
        }
        $t .= "\n\n";
        fwrite(self::$handel, $t);
    }

    public static function save($msg, $type)
    {
        self::$content[] = ['type' => $type, 'msg' => $msg];
        return true;
    }

    public function __destruct()
    {
        if (self::check_file() === true) {
            self::write();
            fclose(self::$handel);
        }
    }
}
