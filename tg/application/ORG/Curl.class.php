<?php
/**
 * ����curl�ϴ�����ط���
 *
 * @author     lixiaohong <2644259148@qq.com>
 */

/**
 * ����curl�ϴ�����صķ���
 *
 * @author     lixiaohong <2644259148@qq.com>
 */
class Curl
{
    public $error; 
    public $typearr;
    //ָ���ļ���ʱ��Ĭ�ϵĺϷ�����
    public $existtype;
    public $filemaxsize;
    public $imghost;
    public $imgport;
    //����
    public $data;    
    public $postData;        
    public $defaultdir;
    //�����ύ����nameֵ 
    public $filterformarr;
    
    
    /**
    * Short description�����캯��������curl������Ϣ
    *
    */
    public function __construct()
    {
        include dirname(__FILE__).'/../../include/mbcache/config.php';
        $CURL_HOST=$curlconfig[0][0];
        $CURL_PORT=$curlconfig[0][1];
        
        $this->imghost=trim($CURL_HOST);
        $this->imgport=trim($CURL_PORT);
        $this->defaultdir='publicimg';
        $this->typearr=array(
                                'image/jpeg',
                                'image/pjpeg', 
                                'image/png', 
                                'image/x-png', 
                                'image/gif', 
                                'image/bmp', 
                                'application/x-shockwave-flash'
                            );
        $this->existtype=array("jpg", "jpeg", "bmp", "gif", "swf", "png");
        $this->filemaxsize=3*1024*1024; 
    }
    
    /**
    * Short description���ļ��ϴ�
    *
    * @param array $arr #�ϴ�������
    *
    * @return array #�������ɹ�������������Ϣ ���򷵻�false
    */
    public function add($arr=array())
    {
        if (!is_array($arr) || empty($arr)) {
            $this->error=array('1','��ʼ����������');
            return false;
        }
        
        if ($this->passFile($arr)!=true) {
            return false;
        }

        if (!is_array($this->postData) || empty($this->postData)) {
            $this->error=array('4','���ݵĲ������ǿ�ֵ������');
            return false;
        }
        
        $postData=$this->postData;

        $ch=curl_init();
        $tmp_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:'';
        curl_setopt($ch, CURLOPT_URL, 'http://'.$this->imghost.':'.$this->imgport.'/index.php?m=index&a=curl');
        //curl_setopt($ch, CURLOPT_URL,'http://www.lawtime.cn/imageserver/index.php?m=index&a=curl');
        curl_setopt($ch, CURLOPT_USERAGENT, $tmp_user_agent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $data=curl_exec($ch);
        
        curl_close($ch);
        
        //$this->data=$data;
        
        return $data;
        
    }

    /**
     * Short description����ȡ�������Ϣ
     *
     * @return array #����д���ͷ��ش�����Ϣ ���򷵻ؿ�
     */
    public function getError()
    {
        if (empty($this->error)) {
            return '';
        }
        return $this->error;
    }


    /**
    * Short description����ȡ������������� ����
    *
    * @return array #����оͷ��� ���򷵻ؿ�
    */
    public function getData()
    {
        if (empty($this->data)) {
            return '';
        }
        return $this->data;
    }

    /**
    * Short description����ȡ�������Ϣ
    *
    * @param array $arr #��֤����
    *
    * @return array #����д���ͷ��ش�����Ϣ ���򷵻ؿ�
    */
    public function passFile($arr=array())
    {
        
        if (!is_array($arr) || empty($arr)) {
            $this->error=array('1', '��ʼ����������');
            return false;
        }
    
        $flag=true;
        
        foreach ($arr as $k=>$v) {
            if (empty($v)) {
                continue;
            }
            if (is_array($v) && !empty($v) && ($this->oneTwoArr($v)=='2') ) {
                $filetotalsize='';
                foreach ($v as $key=>$val) {
                
                    //����form���е�nameֵ ��ֵ�Ż�ȥ��֤
                    if (!empty($this->filterformarr)) {
                        if (!in_array($key, $this->filterformarr)) {
                            continue;
                        }
                    }

                    if (is_array($val) 
                        && !empty($val) 
                        && isset($val['name']) 
                        && isset($val['tmp_name']) 
                        && !empty($val['name']) 
                        && !empty($val['tmp_name'])
                    ) {
                    
                        $tmp_type=$val['type'];
                        $tmp_type=strtolower($tmp_type);
                        if (!in_array($tmp_type, $this->typearr)) {
                            $this->error=array('2', '�����ϴ��ļ����Ͳ��Ϸ�������');
                            return false;
                        }
                        $tmp_size=$val['size'];
                        $filetotalsize+=$tmp_size;
                        $tmp_maxsize=$this->filemaxsize/1024/1024;
                        
                        if ($filetotalsize>$this->filemaxsize) {
                            $this->error=array('3', '�����ϴ��ļ��ܴ�С����'.$tmp_maxsize.'M������');
                            return false;
                        }
                        $this->postData[$key]='@'.$val['tmp_name']; 
                        $imgtype=pathinfo($val['name'], PATHINFO_EXTENSION);
                        $this->postData["imgtype_".$key]=$imgtype;
                    }
                }
            } else if ( $this->oneTwoArr($v)=='1' ) {
                //�������Ƭ
                if ($k=='oldphoto') {
                    $this->oldPhotoDone($v);
                } else if ($k=='extraparam') {
                    //�������Ĳ���
                    if ($this->extraDone($v, $k)!=true) {
                        return false;
                    }
                } else {
                    if ($this->passDir($v)!=true) {
                        return false;
                    }
                }
            } else {
                $this->postData[$k]=$v;
            }
            
            $flag=false;
        }
 
        if ($flag==true) {
            $this->error=array('4', '���ݵĲ������ǿ�ֵ������');
        }
        
        return true;
        
    }
    
     /**
     * Short description������ϴ��ļ��е�ԭͼ·�� ����ͼ·�� ����ͼ��С�Ƿ�ϸ� ����ͼ�ĳߴ��Ƿ�ϸ�
     *
     * @param array  $v #�������
     * @param string $k #��Ҫ����imgdir��php��html�д�ֵ�ĳ�ͻ
     *
     * @return array  #�ɹ�����true ���򷵻�false
     */
     
    public function passDir($v=array(),$k='')
    {
        if (!is_array($v) || empty($v) || !($this->oneTwoArr($v)=='1') ) {
            $this->error=array('5', 'ϵͳ���·������ʱ�����˴���');
            return false;
        }
        
        $k=trim($k);
        
        //��extram�в�����imgdir������ȥ���е�post��֤
        if (!$this->postData['imgdir']) {
            $imgdir=$v['imgdir'];
    
            $imgdir=trim($imgdir);
            
            if (is_dir($imgdir)) {
                $this->error=array('6', 'ͼƬ�Ĵ���·��ֻ����д�ļ�������');
                return false;
            } else if (!empty($imgdir)) {
                $this->postData['imgdir']=$imgdir; 
            } else if (!empty($k)) {
                $this->postData['imgdir']=$this->defaultdir;
            }
        }
        
        //��extram�в�����isthumb������ȥ���е�post��֤
        if (!$this->postData['isthumb']) {
            $isthumb=$v['isthumb'];
            $isthumb=trim($isthumb);
            if ($isthumb=='1') {
                
                $thumbw=$v['thumbw'];
                $thumbwarr=explode(",", $thumbw);
                
                $thumbh=$v['thumbh'];
                $thumbharr=explode(",", $thumbh);
                
                if ($this->passThumb($thumbwarr, $thumbharr)!=true) {
                    return false;
                }
                
                
                $isfix=$v['isfix'];
                $isfix=trim($isfix);
                if ($isfix=='1') {
                    $this->postData['isfix']='1';
                }   
                
                $this->postData['isthumb']='1';
                $this->postData['thumbw']=$thumbw;
                $this->postData['thumbh']=$thumbh;
            }
        }
        
        //��extram�в�����isthumb������ȥ���е�post��֤
        if (!$this->postData['isauto']) {
            $isauto=$v['isauto'];
            $isauto=trim($isauto);
            if ($isauto=='1') {
                $this->postData['isauto']='1';
            }
        }
        
        //��extram�в�����isuniqid������ȥ���е�post��֤
        if (!$this->postData['isuniqid']) {
            $isuniqid=$v['isuniqid'];
            $isuniqid=trim($isuniqid);
            if ($isuniqid=='1') {
                $this->postData['isuniqid']='1';
            }
        }
        

        return true;
        
        
    }

    
    /**
    * Short description���������Ƭ
    *
    * @param array $v #����Ƭ
    *
    * @return array #�����һά���� �����ַ���1 ����Ƕ�ά���鷵���ַ���2
    */
    public function oldPhotoDone($v=array())
    {
        if (empty($v)) {
            return true;
        }
        
        if (is_array($v)) {
            foreach ($v as $key=>$val) {
                if (!empty($val)) {
                    $key=trim($key);
                    //�ж��Ƿ����������ļ�
                    if (is_file($val)) {
                        //����±�Ϊ���� �ṩĬ�ϼ�
                        if (is_numeric($key)) {
                            $this->postData["secondfile".($key+1)]='@'.$val; 
                        } else {
                            $this->postData[$key]='@'.$val; 
                        }
                    } else {
                        $this->postData['photo'.time()."_".$key]=$val; 
                    } 
                }
            }
           
        } else {
            //�ж��Ƿ����������ļ�
            if (is_file($v)) {
                $this->postData["secondfile"]='@'.$v; 
            } else {
                $this->postData['photo'.time()."_".$key]=$v; 
            }
           
        
        }
   
    }
    
    /**
    * Short description���������Ĳ���
    *
    * @param array  $v #�������Ϣ
    * @param string $k #��Ҫ����imgdir���php��html�еĳ�ͻ
    *
    * @return array #
    */
    public function extraDone($v,$k='')
    {
        if (empty($v)) {
            return true;
        }
        
        
        //ָ�����ļ��� ���֧��10��ָ�����ļ���
        for ($i=0; $i<=10; $i++) {
            //���ڵ��Ž��д���
            if ($i==0) {
                $existname=$v['existname'];
            } else {
                $existname=$v['existname'.$i];
            }
            $existname=trim($existname);
            if (isset($existname) && !empty($existname) ) {
                $len=strpos($existname, '?v');
                if ($len>1) {
                    $existname=mb_substr($existname, 0, $len);
                }
                $existname=trim($existname);                

                $tmp_existname_arr=explode(".", $existname);
                $tmp_ext=array_pop($tmp_existname_arr);
                $tmp_ext=strtolower($tmp_ext);
                if (in_array($tmp_ext, $this->existtype)) {
                    //���ڵ��Ž��д���
                    if ($i==0) {
                        $this->postData['existname']=$existname; 
                    } else {
                        $this->postData['existname'.$i]=$existname; 
                    }
                } else {
                    $this->error=array("7", 'existname'.$i.'ָ�����ļ����������Ϸ�');
                    return false;
                }
            }
        }
        
        //���˱��е�name 
        $filterformname=$v['filterformname'];
        $filterformname=trim($filterformname);
        if (isset($filterformname) && !empty($filterformname) ) {
            $filterformarr=explode(",", $filterformname);
            foreach ($filterformarr as $key=>$value) {
                $value=trim($value);
                if (empty($value)) {
                    continue;
                }
                $this->filterformarr[]=$value;
            }
        }
        
        $k=trim($k);
        if ($this->passDir($v, $k)!=true) {
            return false;
        }
        
        return true;
        

    }
    
    /**
    * Short description�����һ��������һά���Ƕ�ά
    *
    * @param array $arr #����
    *
    * @return array #�����һά���� �����ַ���1 ����Ƕ�ά���鷵���ַ���2
    */
    public function oneTwoArr($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $k=>$v) {
                if (is_array($v)) {
                    return '2';
                }
                return '1';
            }
        }
        return '3';
    }

    /**
    * Short description����֤����ͼ����ߵĺϷ���
    *
    * @param array $thumbwarr #�����
    * @param array $thumbharr #�����
    *
    * @return array #��֤����ͼ�����
    */
    public function passThumb($thumbwarr=array(),$thumbharr=array())
    {
        if (empty($thumbwarr) || empty($thumbharr)) {
            $this->error=array('7', 'ͼƬ����ͼ�Ĳ����������');
            return false;
        }
        
        $count1=count($thumbwarr);
        $count2=count($thumbharr);
        
        if ($count1==0 || $count2==0 || $count1!=$count2) {
            $this->error=array('7', '��������ͼ֮�� ����������ó������������������');
            return false;
        }
        
        foreach ($thumbwarr as $k=>$v) {
            $v=trim($v);
            if (empty($v) || !is_numeric($v)) {
                $this->error=array('8', '����ͼ�еĿ��к������ֵĲ���');
                return false;
            }
        }
        
        foreach ($thumbharr as $k=>$v) {
            $v=trim($v);
            if (empty($v) || !is_numeric($v)) {
                $this->error=array('9', '����ͼ�еĸ��к������ֵĲ���');
                return false;
            }
        }
        return true;
    
    }
    
    /**
    * Short description��ɾ���ļ�
    *
    * @param array $arr #����
    *
    * @return array #ɾ���ļ�
    */
    public function delete($arr=array())
    {
        if (!is_array($arr) || empty($arr)) {
            $this->error=array('1', '��ʼ����������');
            return false;
        }
        
        $postData2=$this->postData2($arr);
        if (empty($postData2)) {
            return false;
        }
        
        $ch=curl_init();
        $tmp_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:'';
        curl_setopt($ch, CURLOPT_URL, 'http://'.$this->imghost.':'.$this->imgport.'/index.php?m=index&a=curlDel');
        //curl_setopt($ch,CURLOPT_URL,'http://www.lawtime.cn/imageserver/index.php?m=index&a=curlDel');
        curl_setopt($ch, CURLOPT_USERAGENT, $tmp_user_agent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData2);
        $data=curl_exec($ch);
        curl_close($ch);
        
        //$this->data=$data;
        
        return $data;
        
    }
    
    /**
    * Short description������ɾ��������
    *
    * @param array $arr #����
    *
    * @return array #����ɾ��������
    */
    public function postData2($arr)
    {
        if (!is_array($arr) || empty($arr)) {
            $this->error=array('1', '��ʼ����������');
            return false;
        }
        
        $newarr=array();
        foreach ($arr as $k=>$v) {
            if (empty($v) || is_array($v)) {
                continue; 
            }
            //�����������Ϊ��������
            $newarr["curl_delete".($k+1)]=$v;
        }
        
        if (!is_array($newarr) || empty($newarr)) {
            $this->error=array('2', '��ʼ������ֻ����һά������߶��ǿ�ֵ');
            return false;
        }
        
        return $newarr;
    }
    
}

?>