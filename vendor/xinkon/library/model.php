<?php
/*
//
 * Created by PhpStorm.
 * User: 新&空
 * Date: 2016/4/8
 * Time: 17:17
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
 *                #                                                   #
 *                *****************************************************
 *
//
*/
namespace library;

class  model
{
    protected static $_dbh = null; //静态属性,所有数据库实例共用,避免重复连接数据库

    protected $_where = '';
    protected $_order = '';
    protected $_limit = '';
    protected $_field = '*';
    protected $_table = NULL;
    protected $_table_true = '';
    protected $_clear = 0; //状态，0表示查询条件干净，1表示查询条件污染
    protected $_trans = 0; //事务指令数
    protected $config = [
    ];

     static $sql_data = [];

    /**
     * 初始化类
     */
    public function __construct($table = "")
    {
        if (!class_exists('PDO')) {
            throw new \Exception("环境不支持pdo连接");
        }
        if (empty($this->config = config::get("db"))) {
            throw new \Exception("数据库配置为空");
        }
        //连接数据库
        if (is_null(self::$_dbh)) {
            $this->_connect();
        }
        //获取表名
        $this->get_table($table);
    }

    /**
     * 连接数据库的方法
     */
    private function _connect()
    {
        $dsn = $this->config['type'] . ':host=' . $this->config['hostname'] . ';port=' . $this->config['hostport'] . ';dbname=' . $this->config['dbname'];
        $options = $this->config['connect'] ? array(\PDO::ATTR_PERSISTENT => true) : array();
        try {
            $dbh = new \PDO($dsn, $this->config['username'], $this->config['password'], $options);
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);  //设置如果sql语句执行错误则抛出异常，事务会自动回滚
            $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //禁用prepared statements的仿真效果(防SQL注入)
        } catch (\PDOException $e) {
            throw new \Exception('数据库连接失败: ' . $e->getMessage());
        }
        $utf8 = 'SET NAMES utf8';
        $dbh->exec($utf8);
        $this->set_sql_data("连接数据库..");
        $this->set_sql_data($utf8);
        self::$_dbh = $dbh;
    }

    private function get_table($table = "")
    {
        $str = '';
        if (is_string($table) && !empty($table)) {
            $str = $table;
        } else {
            if ($this->_table !== NULL) {
                $str = $this->_table;
            } elseif ($this->_table == NUll) {
                $str = basename(str_replace('\\', '/', get_class($this)));
            }
        }
        if ($str == "model") {
            throw new \Exception("请选择表..");
        }
        $this->_table_true = $this->config['prefix'] . $str;
    }


    /**
     * 字段和表名添加 `符号
     * 保证指令中使用关键字不出错 针对mysql
     * @param string $value
     * @return string
     */
    protected function _addChar($value)
    {
        if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`')) {
            //如果包含* 或者 使用了sql方法 则不作处理
        } elseif (false === strpos($value, '`')) {
            $value = '`' . trim($value) . '`';
        }
        return $value;
    }

    /**
     * 取得数据表的字段信息
     * @param string $tbName 表名
     * @return array
     */
    protected function _tbFields($tbName)
    {
        $sql = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="' . $tbName . '" AND TABLE_SCHEMA="' . $this->config['dbname'] . '"';
        $stmt = self::$_dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $ret = array();
        foreach ($result as $key => $value) {
            $ret[$value['COLUMN_NAME']] = 1;
        }
        return $ret;
    }

    /**
     * 过滤并格式化数据表字段
     * @param string $tbName 数据表名
     * @param array $data POST提交数据
     * @return array $newdata
     */
    protected function _dataFormat($tbName, $data)
    {
        if (!is_array($data)) return array();
        $table_column = $this->_tbFields($tbName);
        $ret = array();
        foreach ($data as $key => $val) {
            if (!is_scalar($val)) continue; //值不是标量则跳过
            if (array_key_exists($key, $table_column)) {
                $key = $this->_addChar($key);
                if (is_int($val)) {
                    $val = intval($val);
                } elseif (is_float($val)) {
                    $val = floatval($val);
                } elseif (preg_match('/^\(\w*(\+|\-|\*|\/)?\w*\)$/i', $val)) {
                    // 支持在字段的值里面直接使用其它字段 ,例如 (score+1) (name) 必须包含括号
                    $val = $val;
                } elseif (is_string($val)) {
                    $val = '"' . addslashes($val) . '"';
                }
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function set_sql_data($sql)
    {
        self::$sql_data[] = " ' $sql ' ";
        return;
    }

    /**
     * 执行查询 主要针对 SELECT, SHOW 等指令
     * @param string $sql sql指令
     * @return mixed
     */
    private function _doQuery($sql = '')
    {
        $this->set_sql_data($sql);
        $pdostmt = self::$_dbh->prepare($sql); //prepare或者query 返回一个PDOStatement
        $pdostmt->execute();
        $result = $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 执行语句 针对 INSERT, UPDATE 以及DELETE,exec结果返回受影响的行数
     * @param string $sql sql指令
     * @param bool $type sql指令
     * @return integer
     */
    private function _doExec($sql = '', $type = true)
    {
        $this->set_sql_data($sql);
        $result = self::$_dbh->exec($sql);
        if ($type === true) {
            return $result;
        } else {
            return self::$_dbh->lastInsertId();
        }
    }

    /**
     * 执行sql语句，自动判断进行查询或者执行操作
     * @param string $sql SQL指令
     * @return mixed
     */
    public function doSql($sql = '')
    {
        $queryIps = 'INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|SELECT .* INTO|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK';
        if (preg_match('/^\s*"?(' . $queryIps . ')\s+/i', $sql)) {
            return $this->_doExec($sql);
        } else {
            //查询操作
            return $this->_doQuery($sql);
        }
    }


    /**
     * 插入方法
     * @param string $tbName 操作的数据表名
     * @param array $data 字段-值的一维数组
     * @return int 受影响的行数
     */
    public function insert(array $data)
    {
        $data = $this->_dataFormat($this->_table_true, $data);
        if (!$data) return;
        $sql = "insert into " . $this->_table_true . "(" . implode(',', array_keys($data)) . ") values(" . implode(',', array_values($data)) . ")";
        return $this->_doExec($sql, false);
    }

    /**
     * 删除方法
     * @param string $tbName 操作的数据表名
     * @return int 受影响的行数
     */
    public function delete()
    {
        //安全考虑,阻止全表删除
        if (!trim($this->_where)) return false;
        $sql = "delete from " . $this->_table_true . " " . $this->_where;
        $this->_clear = 1;
        $this->_clear();
        return $this->_doExec($sql);
    }

    /**
     * 更新函数
     * @param array $data 参数数组
     * @return int 受影响的行数
     */
    public function update(array $data)
    {
        //安全考虑,阻止全表更新
        if (!trim($this->_where)) return false;
        $data = $this->_dataFormat($this->_table_true, $data);
        if (!$data) return;
        $valArr = '';
        foreach ($data as $k => $v) {
            $valArr[] = $k . '=' . $v;
        }
        $valStr = implode(',', $valArr);
        $sql = "update " . trim($this->_table_true) . " set " . trim($valStr) . " " . trim($this->_where);
        return $this->_doExec($sql);
    }

    /**
     * 查询函数
     * @return array 结果集
     */
    public function select()
    {
        $sql = "select " . trim($this->_field) . " from " . $this->_table_true . " " . trim($this->_where) . " " . trim($this->_order) . " " . trim($this->_limit);
        $this->_clear = 1;
        $this->_clear();
        return $this->_doQuery(trim($sql));
    }

    /**
     * @param mixed $option 组合条件的二维数组，例：$option['field1'] = array(1,'=>','or')
     * @return $this
     */
    public function where($option)
    {
        if ($this->_clear > 0) $this->_clear();
        $this->_where = ' where ';
        $logic = 'and';
        if (is_string($option)) {
            $this->_where .= $option;
        } elseif (is_array($option)) {
            foreach ($option as $k => $v) {
                if (is_array($v)) {
                    $relative = isset($v[1]) ? $v[1] : '=';
                    $logic = isset($v[2]) ? $v[2] : 'and';
                    $condition = ' (' . $this->_addChar($k) . ' ' . $relative . ' ' . $v[0] . ') ';
                } else {
                    $logic = 'and';
                    $condition = ' (' . $this->_addChar($k) . '=' . $v . ') ';
                }
                $this->_where .= isset($mark) ? $logic . $condition : $condition;
                $mark = 1;
            }
        }
        return $this;
    }

    /**
     * 设置排序
     * @param mixed $option 排序条件数组 例:array('sort'=>'desc')
     * @return $this
     */
    public function order($option)
    {
        if ($this->_clear > 0) $this->_clear();
        $this->_order = ' order by ';
        if (is_string($option)) {
            $this->_order .= $option;
        } elseif (is_array($option)) {
            foreach ($option as $k => $v) {
                $order = $this->_addChar($k) . ' ' . $v;
                $this->_order .= isset($mark) ? ',' . $order : $order;
                $mark = 1;
            }
        }
        return $this;
    }

    /**
     * 设置查询行数及页数
     * @param int $page pageSize不为空时为页数，否则为行数
     * @param int $pageSize 为空则函数设定取出行数，不为空则设定取出行数及页数
     * @return $this
     */
    public function limit($page, $pageSize = null)
    {
        if ($this->_clear > 0) $this->_clear();
        if ($pageSize === null) {
            $this->_limit = "limit " . $page;
        } else {
            $pageval = intval(($page - 1) * $pageSize);
            $this->_limit = "limit " . $pageval . "," . $pageSize;
        }
        return $this;
    }

    /**
     * 设置查询字段
     * @param mixed $field 字段数组
     * @return $this
     */
    public function field($field)
    {
        if ($this->_clear > 0) $this->_clear();
        if (is_string($field)) {
            $field = explode(',', $field);
        }
        $nField = array_map(array($this, '_addChar'), $field);
        $this->_field = implode(',', $nField);
        return $this;
    }

    /**
     * 清理标记函数
     */
    protected function _clear()
    {
        $this->_where = '';
        $this->_order = '';
        $this->_limit = '';
        $this->_field = '*';
        $this->_clear = 0;
    }

    /**
     * 手动清理标记
     * @return $this
     */
    public function clearKey()
    {
        $this->_clear();
        return $this;
    }

    /**
     * 启动事务
     * @return void
     */
    public function startTrans()
    {
        //数据rollback 支持
        if ($this->_trans == 0) self::$_dbh->beginTransaction();
        $this->_trans++;
        return;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @return boolen
     */
    public function commit()
    {
        $result = true;
        if ($this->_trans > 0) {
            $result = self::$_dbh->commit();
            $this->_trans = 0;
        }
        return $result;
    }

    /**
     * 事务回滚
     * @return boolen
     */
    public function rollback()
    {
        $result = true;
        if ($this->_trans > 0) {
            $result = self::$_dbh->rollback();
            $this->_trans = 0;
        }
        return $result;
    }

    /**
     * 关闭连接
     * PHP 在脚本结束时会自动关闭连接。
     */
    public function close()
    {
        if (!is_null(self::$_dbh)) self::$_dbh = null;
    }

}