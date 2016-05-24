<?php
namespace library;
class cache
{
    private static $handle = false;
    private static $drive;

    static public function init()
    {
        if (is_resource(self::$handle)) {
            return true;
        }
        $config = config::get("cache");
        if (empty($config) || !is_array($config)) {
            throw new \Exception("没有缓存配置信息");
        }
        switch ($config['cache_type']) {
            case false;
                return;
            case  "file";
                self::$drive = "file";
                break;
            case  "redis";
                self::$drive = "redis";
                break;
            default;
                throw new \Exception("不支持的缓存记录类型");
        }
        $config = $config['cache_' . self::$drive];
        if (!is_array($config) || empty($config)) {
            throw new \Exception("缓存类型" . self::$drive . "配置信息有误");
        }
        if (self::$handle == false) {
            $class = ('library\\cache\\') . self::$drive;
            self::$handle = new $class($config);
            return true;
        } else {
            return true;
        }
    }

    static public function set($name, $value, $expire = null)
    {
        self::init();
        return self::$handle->set($name, $value, $expire);
    }

    static public function get($name)
    {
        self::init();
        return self::$handle->get($name);
    }

    static public function rm($name)
    {
        self::init();
        return self::$handle->rm($name);

    }

    static public function clear()
    {
        self::init();
        return self::$handle->clear();
    }
}