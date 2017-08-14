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



            $where['a.type'] = "申请资源";
            $where["a.createtime"]  = array(array('egt',$startdate." 00:00:00"),array('elt',$enddate." 23:59:59"));

            $count = M('')->table('yx_tg_log as a')->join("left join yx_tg_source as b on a.source_id=b.id")->count();
            $operate = M('')->table('yx_tg_log as a')->join("left join yx_tg_source as b on a.source_id=b.id")
                ->order("a.createtime desc")
                ->where($where)
                ->field('a.id,a.username,a.type,a.class,a.function,a.content,a.source_id,b.sourcesharerate,b.sourcechannelrate,a.createtime')
                ->page($current, $rowCount)
                ->select();
            empty($operate) && $operate = array();
            foreach($operate as &$value){
                $value['operation'] = '<a href="javascript:void(0);" onclick="editRate('.$value['source_id'].','.$value['sourcesharerate'].','.$value['sourcechannelrate'].')">修改</a>';
            }
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


    public function editRate()
    {
        $sourceid = isset($_POST["sourceid"]) ? (int)$_POST["sourceid"] : '';
        $sourcesharerate = isset($_POST["sourcesharerate"]) ? $_POST["sourcesharerate"] : 0;
        $sourcechannelrate = isset($_POST["sourcechannelrate"]) ? $_POST["sourcechannelrate"] : 0;

        if (empty($sourceid)) {
            $this->ajaxReturn('参数错误', 'error', 0);
        }

        $modal = M('tg_source');

        $condition["id"] = $sourceid;

        $source = $modal->where($condition)->find();
        $data["sourcesharerate"] = $sourcesharerate;
        $data["sourcechannelrate"] = $sourcechannelrate;
        $result = $modal->where($condition)->save($data);

        $this->insertLog($_SESSION['adminname'], '修改分成比例', 'LogAction.class.php', 'editRate',
            date('Y-m-d H:i:s'),$_SESSION['adminname'] . "重置【" . $source['sourcesn'] . "】分成比例,
            原始通道费：" . $source["sourcechannelrate"] . ",修改通道费:" . $sourcesharerate.",原始分成比例：" .
            $source["sourcesharerate"] . ",修改分成比例:" . $sourcesharerate);
        if ($result) {
            $this->ajaxReturn($result, 'success', 1);
            exit();
        } else {
            $this->ajaxReturn('未能更新成功', 'fail', 0);
            exit();
        }
    }
}