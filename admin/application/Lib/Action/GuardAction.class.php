<?php

class GuardAction extends CommonAction
{
    /**
     * 渠道类型
     */
    protected $sourceType = array(
        0 => '其它',
        1 => '公会',
        2 => '买量',
        3 => '平台YXGAMES',
        4 => 'CPS',
        5 => '应用商店'
    );

    private $_guard;

    public function __construct()
    {
        parent::__construct();

        $this->_guard = M('guard');
    }


    public function index()
    {
        $formData['from_table'] = isset($_GET['from_table']) ? $_GET['from_table'] : 0;

        $formData['from_id'] = isset($_GET['from_id']) ? $_GET['from_id'] : 0;

        $this->assign($formData);

        $this->display();
    }

    public function data(){
        $treeData = $this->getUserType();
        $gameAll = M('all_game')->field('id,name as text')->select();

        $this->assign('treeData', $treeData);
        $this->assign('allGame', json_encode($gameAll));
        $this->display();
    }



    public function getGame()
    {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $gameModel = M('all_game');
        $where = array(
            'name' => array('like', "%{$q}%")
        );
        $data = $gameModel->where($where)->select();

        if($data){
            $res = array(
                'incomplete_results' => true,
                'items' => $data,
                'total_count' => count($data)
            );
        }else{
            $res = array(
                'incomplete_results' => false,
                'items' => array(),
                'total_count' => 0
            );
        }

        echo json_encode($res);
    }

    public function getChannel()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $strCount = substr_count($id, '*');
        if($strCount == 2){
            $data = $this->getUrserByType($id);
        }else if($strCount == 1){
            $data = $this->getChannelByUid($id);
        }
        echo json_encode($data);exit;

    }


    public function getUserType(){
        $sourceType = array(
            array('id'=>'0-*-*', 'name' => '其它','isParent' => true, 'pId' => -1),
            array('id'=>'1-*-*', 'name' => '公会','isParent' => true, 'pId' => -1),
            array('id'=>'2-*-*', 'name' => '买量','isParent' => true, 'pId' => -1),
            array('id'=>'3-*-*', 'name' => '平台YXGAMES','isParent' => true, 'pId' => -1),
            array('id'=>'4-*-*', 'name' => 'CPS','isParent' => true, 'pId' => -1),
            array('id'=>'5-*-*', 'name' => '应用商店','isParent' => true, 'pId' => -1),
        );

        return $sourceType;
    }

    protected function getUrserByType($id)
    {
        list($sid) = explode('-', $id);
        $where['activeflag'] = 1;
        $where['isverified'] = 1;
        $where['sourcetype'] = $sid;
        $user = M('tg_user')
            ->where($where)
            ->field('sourcetype,account,realname,userid')
            ->order('userid asc')
            ->select();

        $item = array();
        foreach($user as $value){
            $strId = implode('-', array($sid, $value['userid'], '*'));
            $item[] = array(
                'id' => $strId,
                'name' => !empty($value['realname']) ? $value['realname'] : $value['account'],
                'isParent' => true
            );
        }

        unset($user);
        return $item;
    }

    protected function getChannelByUid($id)
    {
        list($sid,$uid,$cid) = explode('-', $id);

        $where['userid'] = $uid;

        $channel = M('tg_channel')
            ->where($where)
            ->select();

        $item = array();
        foreach($channel as $value){
            $strId = implode('-', array($sid, $uid, $value['channelid']));
            $item[] = array(
                'id' => $strId,
                'name' => $value['channelname'],
                'isParent' => false
            );
        }

        unset($user);
        return $item;
    }

    /**
     * @param $data
     * @param $from_table
     * @param $from_id
     * @param $type
     */
    public function addGuard(&$guard_data,$from_table,$from_id)
    {
        if(empty($from_table) || empty($from_id)){
            return false;
        }

        $where['from_table'] = $from_table;
        $where['from_id'] = $from_id;
        $guardModel = M('guard');
        $guardModel->where($where)->delete();

        if(!isset($guard_data['game_id']) || empty($guard_data['game_id'])){
            $guard_data['game_id'] = array();
        }

        if(!isset($guard_data['channel_id']) || empty($guard_data['channel_id'])){
            $guard_data['channel_id'] = array();
        }

        if(!isset($guard_data['user_id']) || empty($guard_data['user_id'])){
            $guard_data['user_id'] = array();
        }
        $time = time();
        $item = array();
        foreach($guard_data as $key=>$value){
            if($key == 'user_id')  {
                $value = $this->parseUser($value);
            }elseif($key == 'channel_id'){
                $value = $this->parseChannel($value);
            }elseif($key == 'game_id'){
                $value = array();//$this->parseGame($value);
            }
            foreach($value as $v){
                $item[] = array(
                    'from_table' => $from_table,
                    'from_id' => $from_id,
                    'value' => $v,
                    'type' => $key,
                    'created_at' => $time
                );
            }
        }
       // print_r($item);exit;

        unset($where);
        $ret = $guardModel->addAll($item);

        if($ret){
           return true;
        }
        return false;
    }

    public function removeGuard($from_table, $from_id)
    {
        $where['from_table'] = $from_table;
        $where['from_id'] = $from_id;
        $guardModel = M('guard');
        return $guardModel->where($where)->delete();
    }

    protected function parseUser($userid)
    {
        if(empty($userid)){
            $item[] = '*-*-*';
            return $item;
        }

        $userid = implode(',',$userid);
        $where['username'] = array('in', $userid);
        $where['mobile'] = array('in', $userid);
        $where['email'] = array('in', $userid);
        $where['_logic'] = 'OR';
        $user = M('all_user')
            ->where($where)
            ->field('id,username')
            ->select();
        $item = array();
        foreach($user as $v2){
            $item[] = $v2['id'];
        }

        return $item;
    }

    protected function parseChannel($channel_id)
    {
        $channel_id = array_unique($channel_id);
        sort($channel_id);

        $lastWildcard = '';
        $item = array();
        foreach($channel_id as $id)
        {
            if ($this->str_is($lastWildcard, $id))
                continue;
            else
                $lastWildcard = $id;

            $item[] = $id;
        }

        if(empty($item)){
            $item[] = '*-*-*';
        }
        return $item;
    }

    protected function parseGame($gameid){
        if(empty($gameid)){
            $item[] = '*-*-*';
            return $item;
        }
        return $gameid;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    public function str_is($pattern, $value)
    {
        if ($pattern == $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool) preg_match('#^'.$pattern.'\z#u', $value);
    }

    /**
     * get edit data
     */
    public function getGuardData()
    {
        $from_table = isset($_POST['from_table']) ? $_POST['from_table'] : 0;
        $from_id = isset($_POST['from_id']) ? $_POST['from_id'] : 0;
        if(!empty($from_table) && !empty($from_id)){
            $where['from_table'] = $from_table;
            $where['from_id'] = $from_id;
            $guard = $this->_guard->where($where)->select();
            $item = $user_id = array();
            foreach($guard as $value){
                if($value['value'] == '*-*-*')
                    continue;
                if($value['type'] == '')
                    continue;

                if($value['type'] == 'user_id'){
                    $user_id[] = $value['value'];
                }else{
                    $item[$value['type']][] = $value['value'];
                }
            }

            unset($guard);
            if(!empty($user_id)){
                $tguser = M('all_user')->where(array('id' => array('in', $user_id)))->select();
                foreach($tguser as $v){
                    $item['user_id'][] = $v['username'];
                }
            }

            $data['status'] = 1;
            $data['data'] = json_encode($item);
            echo json_encode($data);
            exit;
        }
        $data['status'] = 0;
        $data['data'] = array();
        echo json_encode($data);
        exit;
    }

    public function getUser()
    {
        $data = array('total_count' => 0, 'incomplete_results'=> true, 'items' => array());

        $username = isset($_GET['q']) ? $_GET['q'] : '';
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $pageSize = 30;
        if(empty($username)){
            return $data;
        }
        $limit = ($page-1) * $pageSize.','.$pageSize;
        $where['username'] = array('like', "%$username%");
        $where['mobile'] = array('like', "%$username");
        $where['email'] = array('like', "%$username");
        $where['_logic'] = 'OR';
        $userModel = M('all_user');
        $count = $userModel
            ->where($where)
            ->count();

        $user = $userModel
            ->where($where)
            ->field('id,username as name,mobile,email')
            ->limit($limit)
            ->select();
        $data['total_count'] = $count;
        $data['items'] = $user;

        echo json_encode($data);exit;
    }



    public function guartGetindex()
    {
        $this->display();
    }

    public function guartGetdata(){
        $this->display();
    }

    /**
     * @param $data
     * @param $from_table
     * @param $from_id
     * @param $type
     */
    public function addGuardget(&$guard_data,$from_table,$from_id)
    {
        if(empty($from_table) || empty($from_id) || empty($guard_data)){
            return false;
        }
        $guardModel = M('guard_get');
        $guardModel->where(array(
            'from_table' => $from_table,
            'from_id' => $from_id
            ))->delete();

        $ret = $guardModel->add(array(
            'from_table' => $from_table,
            'from_id' => $from_id,
            'value' => $guard_data,
            'created_at' => time()
            ));

        if($ret){
           return true;
        }
        return false;
    }

    /**
     * [getGuardgetData 获取限制json]
     * @return [type] [description]
     */
    public function getGuardgetData()
    {
        $value = M('guard_get')->where(array(
            'from_table' => $_POST['from_table'],
            'from_id' => $_POST['from_id']
            ))->getField('value');
        echo $value;
    }

}