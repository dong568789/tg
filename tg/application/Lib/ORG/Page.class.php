<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------

class Page {
    
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    //protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
	protected $config  =    array('header'=>"<div class='page-turning-box'><div id='page'>",'end'=>'</div></div>','pageleft'=>"<div class='number-page'>",'pageright'=>'</div>','first'=>'首页','prev'=>'上一页','next'=>'下一页','last'=>'末页','theme'=>' %header% %upPage% %pageleft% %linkPage% %pageright% %downPage% %endPage% ');//%nextPage% %end%
    // 默认分页变量名
    protected $varPage;
	//个人中心的AJAX分页
	protected $member;
    protected $stor;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='',$giftnumber='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->giftnumber    =   $giftnumber;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
		if($url =='member'){
			$this->member = 1;

		}
        //dump($_GET);
        if($_GET['_URL_'][0] == 'number'){
            $this->giftnumber=1;
        }

        if( $this->giftnumber==1){
            $array['type'] = $_GET['type'];
            $array['gift_type'] =  $_GET['gift_type'];
            $array['state'] =  $_GET['state'];
           $this->stor = '_t_'.$_GET['type'].'_g_'.$_GET['gift_type'].'_s_'.$_GET['state'];
            //dump($this->stor);
        }

        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url; 
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            //dump($this->url);
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';

        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
            //dump($url);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
			if($this->member==1){

            }else{
                if($this->giftnumber==1){
                 // $url.=$this->stor;
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $upPage  =   "<div class='previous-page'><a href='".str_replace('__PAGE__','p_1',$url)."'>".$this->config['first']."</a></div>
                        <div class='previous-page'><a href='".str_replace('__PAGE__','p_'.$upRow,$url)."'>".$this->config['prev']."</a></div>";
                    //print_R($upPage);
                }else{
                    $upPage  =   "<div class='previous-page'><a href='".str_replace('__PAGE__','index_1',$url)."'>".$this->config['first']."</a></div>
                        <div class='previous-page'><a href='".str_replace('__PAGE__','index_'.$upRow,$url)."'>".$this->config['prev']."</a></div>";
                }

			}
        }else{
            $upPage     =   " ";
        }

        if ($downRow <= $this->totalPages){
			if($this->member==1){
			}else{
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $downPage   =   "<div class='next-page'><a href='".str_replace('__PAGE__','p_'.$downRow,$url)."'>".$this->config['next']."</a></div>
				                <div class='next-page'><a href='".str_replace('__PAGE__','p_'.$this->totalPages .'',$url)."'>".$this->config['last']."</a></div>";
                    //print_R($downPage);
                } else{
                    $downPage   =   "<div class='next-page'><a href='".str_replace('__PAGE__','index_'.$downRow,$url)."'>".$this->config['next']."</a></div>
				                <div class='next-page'><a href='".str_replace('__PAGE__','index_'.$this->totalPages .'',$url)."'>".$this->config['last']."</a></div>";
                }
			}
        }else{
            $downPage   =   " ";
        }
        // << < > >>
//        if($nowCoolPage == 1){
//            $theFirst   =   '';
//            $prePage    =   '';
//        }else{
//            $preRow     =   $this->nowPage-$this->rollPage;
//			if($this->member==1){
//				$prePage    =   "<a href='javascript:' >上".$this->rollPage."页</a>";
//				$theFirst   =   "<a href='javascript:' >".$this->config['first']."</a>";
//			}else{
//				$prePage    =   "<a href='".str_replace('__PAGE__','index_'.$preRow,$url)."' >上".$this->rollPage."页</a>";
//				$theFirst   =   "<a href='".str_replace('__PAGE__','index_1',$url)."' >".$this->config['first']."</a>";
//			}
//
//        }
//        if($nowCoolPage == $this->coolPages){
//            $nextRow    =   $this->nowPage+$this->rollPage;
//			$theEndRow  =   $this->totalPages;
//			if($this->member==1){
//				$nextPage   =   "<div class='number-page'><a href='javascript:' title='后 ".$this->rollPage." 页'>...</a></div>";
//				$theEnd     =   "<div class='number-page'><a href='javascript:' >".$this->config['last']."</a></div>";
//			}else{
//				$nextPage   =   "<div class='number-page'><a href='".str_replace('__PAGE__','index_'.$nextRow,$url)."' title='后 ".$this->rollPage." 页'>...</a></div>";
//				$theEnd     =   "<div class='number-page'><a href='".str_replace('__PAGE__','index_'.$theEndRow,$url)."' >".$this->config['last']."</a></div>";
//			}
//        }else{
//            $nextRow    =   $this->nowPage+$this->rollPage;
//            $theEndRow  =   $this->totalPages;
//			if($this->member==1){
//				$nextPage   =   "<div class='number-page'><a href='javascript:' title='后 ".$this->rollPage." 页'>...</a></div>";
//				$theEnd     =   "<div class='number-page'><a href='javascript:' >".$this->config['last']."</a></div>";
//			}else{
//				$nextPage   =   "<div class='number-page'><a href='".str_replace('__PAGE__','index_'.$nextRow,$url)."' title='后 ".$this->rollPage." 页'>...</a></div>";
//				$theEnd     =   "<div class='number-page'><a href='".str_replace('__PAGE__','index_'.$theEndRow,$url)."' >".$this->config['last']."</a></div>";
//			}
//        }
		$linkPage = "";

        if(is_numeric($_GET['nowPage'])&&isset($_GET['nowPage'])){
            $page=$_GET['nowPage'];
        }


        //当前页面前面的部分，
        if ($this->nowPage > $this->totalPages) {
            if($this->nowPage <= 4 ){
                for ($i = $this->nowPage ; $i < $this->nowPage; $i++ ) {
                    if($this->giftnumber==1){
                        $url = '/number/search/'.'__PAGE__'.$this->stor;
                        $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                    }else{
                        $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                    }

                }
            }else{$x = $this->totalPages - $this->nowPage >= 0 ? $this->totalPages - $this->nowPage : 1;
                for ($i = $this->nowPage + $x - 4; $i < $this->nowPage; $i++ ) {
                    if($this->giftnumber==1){
                        $url = '/number/search/'.'__PAGE__'.$this->stor;
                        $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                    }else{
                        $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                    }

                }

            }
        } elseif ($this->nowPage >= 4) {
            for ($i = $this->nowPage - 2; $i < $this->nowPage; $i++) {
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                }else{
                    $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                }
            }
        }  elseif ($this->totalPages == 1) {

                $page .= '';

        }  else {
            for ($i = 1; $i < $this->nowPage; $i++) {
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                }else{
                    $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                }
            }
        }


        //当前页面
        if($this->giftnumber==1){
            $url = '/number/search/'.'__PAGE__'.$this->stor;
            $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).' style="background:#b40178;color:#fff">'.$i.'</a>';

        }else{
            $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).' style="background:#b40178;color:#fff">'.$i.'</a>';
        }

        //当前页面后面的部分
        if ($this->nowPage < 4) {
            $x = $this->totalPages < 5 ? $this->totalPages : 5;
            for ($i = $this->nowPage + 1; $i <= $x; $i++) {
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                }else{
                    $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                }
            }
        } elseif ($this->nowPage + 2 < $this->totalPages) {
            $x = $this->nowPage + 3 < $this->totalPages ? $this->nowPage + 3 : $this->totalPages;
            for ($i = $this->nowPage + 1; $i < $x; $i++) {
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                }else{
                    $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                }
            }
        } else {
            for ($i = $this->nowPage + 1; $i < $this->totalPages + 1; $i++) {
                if($this->giftnumber==1){
                    $url = '/number/search/'.'__PAGE__'.$this->stor;
                    $page.='<a href='.str_replace('__PAGE__','p_'.$i,$url).'>'.$i.'</a>';

                }else{
                    $page.='<a href='.str_replace('__PAGE__','index_'.$i,$url).'>'.$i.'</a>';

                }
            }
        }

        $linkPage .= "$page";


//        for($i=1;$i<=$this->rollPage;$i++){
//
//            $page       =  ($nowCoolPage-1)*$this->rollPage+$i;
//
//            if($page!=$this->nowPage){
//                if($page<=$this->totalPages){
//					if($this->member==1){
//						$linkPage .= "<a href='javascript:'>".$page."</a>";
//					}else{
//						$linkPage .= "<a href='".str_replace('__PAGE__','index_'.$page,$url)."'>".$page."</a>";
//					}
//
//                }else{
//                    break;
//                }
//            }else{
//                if($this->totalPages != 1){
//					if($this->member==1){
//						  $linkPage .= "<a href='javascript:void(0);' class='page-on' style='background-color: #b40178;color: #fff;'>".$page."</a>";
//					}else{
//						   $linkPage .= "<a href='javascript:void(0);' class='page-on' style='background-color: #b40178;color: #fff;'>".$page."</a>";
//					}
//
//                }
//            }
//        }
		if(empty($linkPage)){
			$linkPage = " ";
		}
		$pageStr     =   str_replace(
		array('%header%','%upPage%','%pageleft%','%linkPage%','%pageright%','%downPage%','%endPage%'),//,'%nextPage%','%end%'
		array($this->config['header'],$upPage,$this->config['pageleft'],$linkPage,$this->config['pageright'],$downPage,$this->config['end']),$this->config['theme']);//,$theEnd,$nextPage
		return $pageStr;
    }
}