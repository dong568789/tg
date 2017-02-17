<?php
 function Pinyin($_String, $_Code='utf-8'){
         
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
                    "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
                    "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
                    "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
                    "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
                    "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
                    "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
                    "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
                    "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
                    "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
                    "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
                    "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
                    "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
                    "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
                    "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
                    "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
                     
        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
                    "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
                    "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
                    "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
                    "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
                    "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
                    "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
                    "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
                    "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
                    "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
                    "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
                    "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
                    "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
                    "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
                    "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
                    "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
                    "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
                    "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
                    "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
                    "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
                    "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
                    "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
                    "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
                    "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
                    "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
                    "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
                    "|-10270|-10262|-10260|-10256|-10254";
                     
        $_TDataKey = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : Arr_Combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);
        if($_Code != 'gb2312') $_String = U2_Utf8_Gb($_String);
        $_Res = '';
        for($i=0; $i<strlen($_String); $i++){
            $_P = ord(substr($_String, $i, 1));
            if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
            $_Res .= Pinyins($_P, $_Data);
        }
        return $_Res;
        //return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }
         
    function Pinyins($_Num, $_Data){
        if ($_Num>0 && $_Num<160 ) return chr($_Num);
            elseif($_Num<-20319 || $_Num>-10247) return '';
        else {
            foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
            return $k;
        }
    }
    function U2_Utf8_Gb($_C){
        $_String = '';
        if($_C < 0x80){ 
            $_String .= $_C;
        }elseif($_C < 0x800){
            $_String .= chr(0xC0 | $_C>>6);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x10000){
            $_String .= chr(0xE0 | $_C>>12);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C>>18);
            $_String .= chr(0x80 | $_C>>12 & 0x3F);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
            return iconv('UTF-8', 'GB2312', $_String);
    }
    function Arr_Combine($_Arr1, $_Arr2){
        for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
        return $_Res;
    }



//获取汉字首字母
function getfirstchar($s0){ 
		$fchar = ord($s0{0});
		if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
		$s1 = iconv("UTF-8","gb2312", $s0);
		$s2 = iconv("gb2312","UTF-8", $s1);
		if($s2 == $s0){$s = $s1;}else{$s = $s0;}
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if($asc >= -20319 and $asc <= -20284) return "A";
		if($asc >= -20283 and $asc <= -19776) return "B";
		if($asc >= -19775 and $asc <= -19219) return "C";
		if($asc >= -19218 and $asc <= -18711) return "D";
		if($asc >= -18710 and $asc <= -18527) return "E";
		if($asc >= -18526 and $asc <= -18240) return "F";
		if($asc >= -18239 and $asc <= -17923) return "G"; 
		if($asc >= -17922 and $asc <= -17418) return "H";
		if($asc >= -17417 and $asc <= -16475) return "J";
		if($asc >= -16474 and $asc <= -16213) return "K";
		if($asc >= -16212 and $asc <= -15641) return "L";
		if($asc >= -15640 and $asc <= -15166) return "M";
		if($asc >= -15165 and $asc <= -14923) return "N";
		if($asc >= -14922 and $asc <= -14915) return "O";
		if($asc >= -14914 and $asc <= -14631) return "P";
		if($asc >= -14630 and $asc <= -14150) return "Q";
		if($asc >= -14149 and $asc <= -14091) return "R";
		if($asc >= -14090 and $asc <= -13319) return "S";
		if($asc >= -13318 and $asc <= -12839) return "T";
		if($asc >= -12838 and $asc <= -12557) return "W";
		if($asc >= -12556 and $asc <= -11848) return "X";
		if($asc >= -11847 and $asc <= -11056) return "Y";
		if($asc >= -11055 and $asc <= -10247) return "Z";
		return null;
	}
//截取中文字符串
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {  
  if(function_exists("mb_substr")){  
              if($suffix)  
              return mb_substr($str, $start, $length, $charset)."...";  
              else
                   return mb_substr($str, $start, $length, $charset);  
         }  
         elseif(function_exists('iconv_substr')) {  
             if($suffix)  
                  return iconv_substr($str,$start,$length,$charset)."...";  
             else
                  return iconv_substr($str,$start,$length,$charset);  
         }  
         $re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef]
                  [x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";  
         $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";  
         $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";  
         $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";  
         preg_match_all($re[$charset], $str, $match);  
         $slice = join("",array_slice($match[0], $start, $length));  
         if($suffix) return $slice."…";  
         return $slice;
}

	function submit(){
		if(!(isset($_POST)&&$_POST['__hash__']==$_SESSION['__hash__'])){
			exit;
		}else{
			if(!is_array($_POST)){
				exit;
			}else{
				foreach($_POST as $k => $v) {
					$v = trim($v);
					$v  = str_replace(" ","",$v);					
					$_POST[$k] = strip_tags($v);
				}
				return $_POST;
			}
		}	
	}
//字复
function daddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}	
//短信 
function smscode($phone){
	
	header("content-type:text/html; charset=utf-8;");

		session_start();//开启缓存
		//if (isset($_SESSION['time']))//判断缓存时间
		//{
		session_id();
		//	$_SESSION['time'];
		//}
		//else
		//{
		$_SESSION['time'] = date("Y-m-d H:i:s");
		//}
		$smscode = rand(1000,9999);
		$_SESSION['smscode']= $smscode;//将content的值保存在session
		
		$username = '70208457';		//用户账号
		$password = '15927611975';		//密码
		$content = "此次申请绑定手机的验证码为".$smscode.",有效时间2分钟.";		//内容
		
		$http = 'http://api.duanxin.cm/';
		$data = array
			(
			'action'=>'send',
			'username'=>$username,					//用户账号
			'password'=>strtolower(md5($password)),	//MD5位32密码
			'phone'=>$phone,				//号码
			'content'=>$content,			//内容
			'time'=>$time,		//定时发送
			'encode'=>'utf8'
			);
		
		$re= postSMS($http,$data);			//POST方式提交
		if(trim($re) == '100' )
		{
			return 'ok';
			exit;
		}
		else 
		{
			return 'exist';
			exit;
		}
}	
function postSMS($url,$data=''){
		$row = parse_url($url);
		$host = $row['host'];
		$port = $row['port'] ? $row['port']:80;
		$file = $row['path'];
		while (list($k,$v) = each($data)) 
		{
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
		}
		$post = substr( $post , 0 , -1 );
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.0\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;		
			fwrite($fp, $out);
			while (!feof($fp)) {
				$receive .= fgets($fp, 128);
			}
			fclose($fp);
			$receive = explode("\r\n\r\n",$receive);
			unset($receive[0]);
			return implode("",$receive);
		}
	}
//加密
function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 0;
	$key = md5($key ? $key : '9e13yK8RN2M0lKP8CLRLhGs468d1WMaSlbDeCcI');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	//$box = range(0, 255);
	$box = 100;

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}


/*
    将7893 转换成 柒仟捌佰玖拾叁肆
    最大是亿
    零壹贰叁肆伍陆柒捌玖
    个拾佰仟万亿分厘
*/
// 将单个数字0-9转换成大写
function one_number_transform_capital($number){
    $str='';
    switch ($number) {
        case '0':
            $str = '零';
            break;

        case '1':
            $str = '壹';
            break;

        case '2':
            $str = '贰';
            break;

        case '3':
            $str = '叁';
            break;

        case '4':
            $str = '肆';
            break;

        case '5':
            $str = '伍';
            break;

        case '6':
            $str = '陆';
            break;

        case '7':
            $str = '柒';
            break;

        case '8':
            $str = '捌';
            break;

        case '9':
            $str = '玖';
            break;

        case '9':
            $str = '拾';
            break;
        
        default:
            # code...
            break;
    }

    return $str;
}
// 整体转换
function numbers_transform_capital($number){
    // 1 0000 0000 

    // 将小数点后面的取出来
    $decimal = explode(".",$number);
    $decimal_arr = str_split($decimal[1],'1');

    $str='';

    $yiDivisor = 100000000;
    $yi=intval($number/$yiDivisor);
    if($yi>=1 && $yi<=9){
        $str.=one_number_transform_capital($yi).'亿';
        $number-=$yi*$yiDivisor;
    }
    $qianWanDivisor = 10000000;
    $qianWan=intval($number/$qianWanDivisor);
    if($qianWan>=1 && $qianWan<=9){
        $str.=one_number_transform_capital($qianWan).'仟';
        $number-=$qianWan*$qianWanDivisor;
    }
    $banWanDivisor = 1000000;
    $banWan=intval($number/$banWanDivisor);
    if($banWan>=1 && $banWan<=9){
        $str.=one_number_transform_capital($banWan).'佰';
        $number-=$banWan*$banWanDivisor;
    }
    $shiWanDivisor = 100000;
    $shiWan=intval($number/$shiWanDivisor);
    if($shiWan>=1 && $shiWan<=9){
        $str.=one_number_transform_capital($shiWan).'拾';
        $number-=$shiWan*$shiWanDivisor;
    }
    $WanDivisor = 10000;
    $Wan=intval($number/$WanDivisor);
    if($Wan>=1 && $Wan<=9){
        $str.=one_number_transform_capital($Wan).'万';
        $number-=$Wan*$WanDivisor;
    }
    $qianDivisor = 1000;
    $qian=intval($number/$qianDivisor);
    if($qian>=1 && $qian<=9){
        $str.=one_number_transform_capital($qian).'仟';
        $number-=$qian*$qianDivisor;
    }
    $banDivisor = 100;
    $ban=intval($number/$banDivisor);
    if($ban>=1 && $ban<=9){
        $str.=one_number_transform_capital($ban).'佰';
        $number-=$ban*$banDivisor;
    }
    $shiDivisor = 10;
    $shi=intval($number/$shiDivisor);
    if($shi>=1 && $shi<=9){
        $str.=one_number_transform_capital($shi).'拾';
        $number-=$shi*$shiDivisor;
    }
    $ge=intval($number/1);
    if($ge>=1 && $ge<=9){
        $str.=one_number_transform_capital($ge).'元';
    }

    if($decimal_arr['0']){
        $str.=one_number_transform_capital($decimal_arr['0']).'角';
    }
    
    if($decimal_arr['1']){
        $str.=one_number_transform_capital($decimal_arr['1']).'分';
    }

    return $str;
}

/**
 * HTTP请求
 * @param string $Url       地址
 * @param string $Params    请求参数
 * @param string $Method    请求方法
 * @return array $callback  返回数组
 */
function httpreqCommon($Url, $Params, $Method='post'){
        $Curl = curl_init();//初始化curl
        if ('get' == $Method){//以GET方式发送请求
            curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
        }else{//以POST方式发送请求
            curl_setopt($Curl, CURLOPT_URL, $Url);
            curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
        }

        curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        //curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

        $Res = curl_exec($Curl);//运行curl
        curl_close($Curl);//关闭curl
        return $Res;
}

// 生成一个不重复的字符串
// 为了和其他控制器中的makeStr名字区分,是一个函数
function createstr($length) { 
    $possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"; 
    $str = ""; 
    while(strlen($str) < $length) {
        $str .= substr($possible, (rand() % strlen($possible)), 1);
    }
    return($str); 
}

// 方便输出调试
function vde($input){
    var_dump($input);
    exit;
}
// 方便输出调试
function vd($input){
    var_dump($input);
}

?>