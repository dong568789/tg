<?php

class LogAction extends CommonAction
{

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 登录日志页面
     *
     */

    public function index()
    {
        if (IS_AJAX) {
            $current = isset($_POST['current']) ? (int)$_POST['current'] : 1;
            $rowCount = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;

            $userlogmodel = M('tg_userlog');
            $count = $userlogmodel->count();
            $log = $userlogmodel->order("createtime desc")->page($current, $rowCount)->select();
            echo json_encode(array(
                'current' => $current,
                'rowCount' => $rowCount,
                'rows' => $log,
                'total' => $count
            ));
            exit;
        }
        $this->authoritycheck(10125);
        $this->menucheck();
        $this->display();

    }


    /**
     * 操作日志页面
     *
     */
    public function operate()
    {
        if (IS_AJAX) {
            $current = isset($_POST['current']) ? (int)$_POST['current'] : 1;
            $rowCount = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;
            $keyword = !empty($_POST['searchPhrase']) ? trim($_POST['searchPhrase']) : '';
            $model = M('tg_log');
            if (!empty($keyword)) {
                $where['type'] = array('like', "%" . $keyword . "%");
                $where['content'] = array('like', "%" . $keyword . "%");
                $where['_logic'] = 'OR';
            }
            $count = $model->where($where)->count();
            $operate = $model->order("createtime desc")->where($where)->page($current, $rowCount)->select();
            echo json_encode(array(
                'current' => $current,
                'rowCount' => $rowCount,
                'rows' => $operate,
                'total' => $count
            ));
            exit;
        }
        $this->logincheck();
        $this->authoritycheck(10126);

        $this->menucheck();
        $this->display();

    }


    public function source()
    {
        if (IS_AJAX) {
            $current = isset($_POST['current']) ? (int)$_POST['current'] : 1;
            $rowCount = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;
            $startdate = isset($_POST['startdate']) ? $_POST['startdate'] : '';
            $enddate = isset($_POST['enddate']) ? $_POST['enddate'] : '';

            if(empty($startdate) || empty($enddate)){
                $startdate = date('Y-m-d');
                $enddate = date('Y-m-d');
            }


            $model = M('tg_log');

            $where['type'] = "申请资源";
            $where["createtime"]  = array(array('egt',$startdate." 00:00:00"),array('elt',$enddate." 23:59:59"));

            $count = $model->where($where)->count();
            $operate = $model->order("createtime desc")->where($where)->page($current, $rowCount)->select();
            echo json_encode(array(
                'current' => $current,
                'rowCount' => $rowCount,
                'rows' => (array)$operate,
                'total' => $count
            ));
            exit;
        }
        $this->logincheck();
        $this->authoritycheck(10126);

        $this->menucheck();
        $this->display();
    }

}