<?php
/**
 * ȫվ���ù�����
 *
 * @author     wusl <525083980@qq.com>
 */

/**
 * ȫվ���ù�����
 *
 * @author     wusl <525083980@qq.com>
 */
class tool
{
    /**
     * tool�๹�캯��
     */
    public function tool()
    {
        //
    }

    /**
     * ��ȡָ���������
     *
     * @param array  $array Ҫ���������
     * @param string $num   ��������
     *
     * @return array ����������
     */
    public static function getrandarray($array, $num)
    {
        $arrcount = count($array);
        if (!$arrcount) {
            return '';
        }
        if ($arrcount < $num) {
            $num = $arrcount;
        }

        $keyarray = array_keys($array);
        shuffle($keyarray);

        for ($i = 0; $i < $num; $i ++) {
            if ($num == 1) {
                $newarray [$i] = $array [$keyarray[0]];
            } else {
                $newarray [$i] = $array [$keyarray [$i]];
            }

        }

        return $newarray;
    }

    /**
     * ��ȡsql������������
     *
     * @param string $field ��ѯ���ֶ���
     * @param int    $area  ����id
     * @param boolen $type  AND��OR��ѡ��
     *
     * @return string ��ѯ������������
     */
    public static function areaWhere($field, $area, $type = true)
    {
        if (empty( $field) || empty( $area)) {
            return 1;
        }

        $len = strlen($area);
        //ʡ����
        if ($len == 2) {
            $startArea = $area.'0000';
            $endArea = $area.'9999';
        } elseif ( $len == 4) {//������
            $startArea = $area.'00';
            $endArea = $area.'99';
        } elseif ( $len == 6) {
            return " $field = $area ";
        } else {
            return 1;
        }
        if ($type === true) {
            return " ($field >= $startArea AND $field <= $endArea) ";
        } else {
            return " ($field < $startArea OR $field > $endArea) ";
        }
    }

    /**
     * ��ȡָ��KEYֵ�Ļ�������
     *
     * @param object $cache �������
     * @param string $key   ����keyֵ
     *
     * @return array ��������
     */
    public static function getCache($cache, $key)
    {
        $data = $cache->get($key);
        return $data;
    }

    /**
     * �첽��ȡ����
     *
     * @param string $url ����Դ����
     *
     * @return string html����
     */
    public static function ajaxProcess($url)
    {
        echo '<script type="text/javascript" src="http://js.lawtimeimg.com/min/?f=js/jquery.js"></script>';
        echo '<script type="text/javascript">';
        echo '$.ajax({
                url:"'.$url.'",
                success : function(data){/*alert(data)*/}
            });
        ';
        echo '</script>';
    }

    /**
     * ����json����
     *
     * @param array $arr Ҫ���������
     *
     * @return json ����������
     */
    public static function jsonencode($arr)
    {
        $parts = array ();
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if ($is_list) {
                    $parts [] = $this->jsonencode($value);
                } else {
                    $parts [] = '"' . $key . '":' . $this->jsonencode($value);
                }
            } else {
                $str = '';
                if (!$is_list) {
                    $str = '"' . $key . '":';
                }
                if (is_numeric($value)) {
                    $str .= $value;
                } elseif ($value === false) {
                    $str .= 'false';
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addslashes($value) . '"';
                }
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        return '{' . $json . '}';
    }

    /**
     * ����json�ַ���
     *
     * @param json $json Ҫ�����json����
     *
     * @return array ����������
     */
    public static function jsondecode($json)
    {
        $comment = false;
        $out = '$x=';
        for ($i = 0; $i < strlen($json); $i ++) {
            if (!$comment) {
                if ($json [$i] == '{') {
                    $out .= ' array(';
                } elseif ($json [$i] == '}') {
                    $out .= ')';
                } elseif ($json [$i] == ':') {
                    $out .= '=>';
                } else {
                    $out .= $json [$i];
                }
            } else {
                $out .= $json [$i];
            }
            if ($json [$i] == '"') {
                $comment = ! $comment;
            }
        }
        eval($out . ';');
        return $x;
    }

    /**
     * ���Թ��߷���
     *
     * @return string �����������
     */
    public static function debug()
    {
        static $start_time = null;
        static $start_code_line = 0;

        $call_info = array_shift(debug_backtrace());
        $code_line = $call_info['line'];
        $file = array_pop(explode('/', $call_info['file']));

        if ($start_time === null) {
            print "debug ".$file."> initialize<br/>\n";
            $start_time = time() + microtime();
            $start_code_line = $code_line;
            return 0;
        }

        printf(
            "debug %s> code-lines: %d-%d time: %.4f mem: %d KB<br/>\n", 
            $file, 
            $start_code_line, 
            $code_line, 
            (time() + microtime() - $start_time), 
            ceil(memory_get_usage()/1024)
        );
        $start_time = time() + microtime();
        $start_code_line = $code_line;
    }

    /**
     * ȥ��html��ǩ
     *
     * @param string $str Ҫ��������
     *
     * @return string ���������
     */
    public static function html2text($str)
    {
        $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU", "", $str);
        $alltext = "";
        $start = 1;
        for ($i=0; $i<strlen($str); $i++) {
            if ($start==0 && $str[$i]==">") {
                $start = 1;
            } elseif ($start==1) {
                if ($str[$i]=="<") {
                    $start = 0;
                    $alltext .= " ";
                } elseif (ord($str[$i])>31) {
                    $alltext .= $str[$i];
                }
            }
        }
        $alltext = str_replace(" ", "", $alltext);
        $alltext = preg_replace("/&([^;&]*)(;|&)/", "", $alltext);
        $alltext = preg_replace("/[ ]+/s", " ", $alltext);
        return $alltext;
    }

    /**
     * ��̬��̬ͼƬ����ƥ��
     *
     * @param string $url ͼƬ·����ʽ�淶�����ĵ�
     *
     * @return string �������淶��ͼƬ·��
     */
    public static function imagesReplace($url)
    {
        $host_url = '';
        $static_host_url = array(
                                0 => 'http://img1.lawtimeimg.com',
                                1 => 'http://img2.lawtimeimg.com', 
                                2 => 'http://img3.lawtimeimg.com'
                            );
        $dyn_host_url = array(
                                0 => 'http://d01.lawtimeimg.com', 
                                1 => 'http://d02.lawtimeimg.com', 
                                2 => 'http://d03.lawtimeimg.com'
                            );

        $begin = explode('/', $url);
        switch ($begin[1]) {
        case 'images':
            $host_url = $static_host_url;
            break;
        case 'photo':
        case 'micphoto':
            $host_url = $dyn_host_url;
            break;
        }

        if ($host_url) {
            $name = explode('?', $url);
            $pos = (crc32($name[0])%3);
            $host_url = $host_url[$pos].$url;
            return $host_url;
        } else {
            echo $url;
        }

    }
    
    /**
    * ��һ����װͼƬ�ϴ�����
    * 
    * @param array   $extra    ������������� ��ָ���ļ���
    * @param array   $oldphoto �������ַ�����Ҳ������һά����
    * @param boolean $iserror  �Ƿ񷵻ش���
    * @param boolean $debug    �Ƿ���������
    *
    * @return #
    */
    public static function add($extra='',$oldphoto='',$iserror=false,$debug=false)
    {
        $data = array("extraparam"=>$extra,"oldphoto"=>$oldphoto,$_FILES, 'post'=>$_POST);
        include_once dirname(__FILE__)."/Curl.class.php";
        $curl=new Curl();
        $add=$curl->add($data);
        if ( (!$add) || is_numeric($add) ) {
            
            if ($debug) {
                if (!$add) {
                    //$add��һά����
                    $debugError=$curl->geterror();
                    print_r($debugError);
                } else if ( is_numeric($add) ) {
                    if ($add=='100') {
                        echo 'no file is up';
                    } else if ($add=='99') {
                        echo 'file fails';
                    }
                }
            }
            
            //�����û���ɵĴ���
            if ($iserror && (!$add) ) {
                $add=$curl->geterror();
                if (is_array($add) && !empty($add)) {
                    $add_tmp1=trim($add[0]);
                    if (in_array($add_tmp1, array('2', '3'))) {
                        return array('error'=>1, 'info'=>$add[1]);
                    } else {
                        return array('error'=>1, 'info'=>'ϵͳ�ڲ������˴���������');
                    }
                }
            }
            
            return false;
            
        } else {
            $add=self::jsondecode($add);
            $info=$add;
            
            if ($iserror) {
                return array('error'=>0, 'info'=>$info);
            } else {
                return $info;
            }
            
            
        }
        

        
        
    }
    
    
    /**
    * ��һ����װͼƬɾ������
    * 
    * @param array   $arr   ����
    * @param boolean $debug �Ƿ���������
    *
    * @return ���ɾ���ɹ�����1 ����0
    */
    public static function delete($arr=array(),$debug=false)
    {
        include_once dirname(__FILE__)."/Curl.class.php";
        $curl=new Curl();
        //ɾ������
        $delete=$curl->delete($arr);
        if (!$delete) {
            $info=false;
            if ($debug) {
                $error=$curl->geterror();
                print_r($error);
            }  
        } else {
            $info=true;
        }
        
        return $info;
    }
    
    /**
    * ����file_get_contents
    * 
    * @param string $strUrl ��ȡ��url����
    *
    * @return string ��ָ�����ӵ�����
    */
    public static function url_get_contents($strUrl)
    {
        $ch = curl_init($strUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            return false;
        }
        curl_close($ch);
        return $response;
    }
    
    /**
    * ���������������ַ���micphoto,common,images�滻Ϊ/micphoto/common/images/
    * 
    * @param string $str       #�������������ַ���micphoto,common,images
    * @param string $separator #separator
    * @param string $replace   #replace
    *
    * @return string #�滻֮���ֵ /micphoto/common/images/
    */
    public static function separatorReplace($str, $separator=",", $replace="/")
    {
        $str=trim($str);
        if (empty($str)) {
            return '';
        }
        
        $separator=trim($separator);
        $replace=trim($replace);
        
        $str=ereg_replace($separator, $replace, $str);
        $str=$replace.$str.$replace;
        $str=ereg_replace($replace.$replace, $replace, $str);
        
        return $str;
        
        
    }
    
    
}
?>