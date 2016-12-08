<?php
/**
 * Created by PhpStorm.
 * User: xinkon
 * Date: 16-5-22
 * Time: 下午3:15
 */
namespace app\web\controllers;

use library\view;

class index extends view
{
    public function index()
    {
        $this->assign('dd', 'bb');
        $this->display("index");
    }

    public function handle($url, $type, $parameter='')
    {
        $return_data = '';
        if ($type == 'get') {
            if (strpos($url, '?') === false) {
                $url = $url . '?' . $parameter;
            } else {
                $url = $url . $parameter;
            }
            $return_data = $this->get_url($url);
        } else if ($type == 'post') {
            $data = urlencode(str_replace(' ','',$parameter));
            $data = array_filter(explode('%26amp%3B', $data));
            $array = [];
            foreach ($data as $k => $v) {
                $tem = explode('%3D', $v);
                $array[$tem[0]] = $tem[1];
            }
            $return_data = $this->post_url($url, $array);
        }else if ($type =='empty'){
            $return_data = $this->get_url($url);
        }
        $data = $this->exit_data($return_data);

        header("Content-type: text/json; charset=utf8");
        exit(json_encode($data));
    }

    private function post_url($url, $post_data)
    {
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,10);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        //执行命令
        $data = curl_exec($curl);

        $array['status'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $array['header'] = substr($data, 0, $headerSize);
        $array['body'] = substr($data, $headerSize);
        //关闭URL请求
        curl_close($curl);
        return $array;
    }

    private function get_url($url)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        //执行命令
        $data = curl_exec($curl);
        $array['status'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $array['header'] = substr($data, 0, $headerSize);
        $array['body'] = substr($data, $headerSize);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $array;
    }

    private function exit_data($data)
    {
        if ($data['status'] == 200) {
            if (json_decode($data['body'], true) == null) {
                $data['status'] = 400;
                return $data;
            }
            $data['body'] = $this->jsonFormat($data['body']);
        }

        $preg = "/<script[\s\S]*?<\/script>/i";
        $data['body'] = preg_replace($preg,"",$data['body']);
        $preg = "/<style[\s\S]*?<\/style>/i";
        $data['body'] = preg_replace($preg,"",$data['body']);

        return $data;
    }


    /** Json数据格式化
     * @param  Mixed $data 数据
     * @param  String $indent 缩进字符，默认4个空格
     * @return JSON
     */
    private function jsonFormat($data, $indent = "&nbsp;&nbsp;&nbsp;&nbsp;")
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }


        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent) ? $indent : '    ';
        $newline = "\n";
        $prevchar = '';
        $outofquotes = true;

        for ($i = 0; $i <= $length; $i++) {

            $char = substr($data, $i, 1);

            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $ret .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $prevchar = $char;
        }

        return $ret;
    }

    /** 将数组元素进行urlencode
     * @param String $val
     */
    private function jsonFormatProtect(&$val)
    {
        if ($val !== true && $val !== false && $val !== null) {
            $val = urlencode($val);
        }
    }


}