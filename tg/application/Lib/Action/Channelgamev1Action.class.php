<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/8
 * Time: 18:22
 */
class Channelgamev1Action extends CommonAction
{

    public function channel_games()
    {
        $userid = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pageNum = isset($_GET['pageNum']) ? (int)$_GET['pageNum'] : 100;
        $sign = isset($_GET['sign']) ? $_GET['sign'] : '';
        $appKey = 'f4d0e057809264c01a6a279519b37df9';
        // $sign = $this->getSign(array('user_id' => $userid, 'page' => $page),$appKey);
        //if($this->getSign(array('user_id' => $userid, 'page' => $page), $appKey) <> $sign){
            //$this->error('签名失败');
        //}

        $pageSize = $pageNum < 1 ? 100 : $pageNum;
        $offset = ($page -1)*$pageSize;

        if($userid <= 0){
            $this->error('用户userid不能为空');
        }

        $user = M('tg_user')->where(array('userid' => $userid))->find();

        if(empty($user['channelid']))
            $this->error('用户渠道不存在');

        $sql = "SELECT count(*) as count FROM yx_tg_source as a INNER JOIN yx_tg_game as b on a.gameid=b.gameid WHERE a.channelid='{$user['channelid']}'";
        $count = M('')->query($sql);

        $sql = "SELECT a.apkurl,a.is_cdn_submit,a.isupload,a.sourcesn,b.gamename,b.gameid,b.gameversion,b.packageversion,b.description,b.gamesize,b.gameicon,b.texturename,b.gametype FROM yx_tg_source as a INNER JOIN yx_tg_game as b on a.gameid=b.gameid WHERE a.channelid='{$user['channelid']}' LIMIT {$offset},{$pageSize}";
        $rs = M('')->query($sql);
        $data = array();

       // $sourceAction = new SourceAction();
        foreach ($rs as $row) {

            //$download_url = $sourceAction->apidownload($row['sourcesn']);
            $data[] = array(
                'gameid' => $row['gameid'],
                'gamename' => $row['gamename'],
                'version' => $row['gameversion'],
                'download_url' => $this->tgdomain.'/Source/apidownload/'.$row['sourcesn'],
                'gamesize' => $row['gamesize'].'MB',
                'gameicon' => $this->iconurl.$row['gameicon'],
                'texturename' => $this->texturedownloadurl.$row['texturename'],
                'packageversion' => $row['packageversion'],
                'gametype' => $row['gametype'],
                'description' => $row['description']
            );
        }

        $this->success($data, $page, $pageSize, (int)$count[0]['count'], $userid);
    }


    /**
     * 功    能:	获取签名
     * 参    数:	$arrParam 数组 POST参数数组
     *			$strKey 字符型 加密秘钥
     * 返    回:	签名
     */
    protected function getSign($arrParam, $strKey)
    {
        $strSign = '';
        ksort($arrParam);
        foreach($arrParam as $strK => $strV)
        {
            if($strK != 'sign')
            {
                $strSign .= "$strK=$strV";
            }
        }
        $strSign .= $strKey;
        return md5($strSign);
    }

    /**
     * 错误提示
     * @param $msg
     */
    protected function error($msg)
    {
        echo json_encode(array(
                'result' => 'failure',
                'message' => $msg
            ));
        exit;
    }

    /**
     * 成功提示
     * @param $coin
     */
    protected function success($data, $page, $page_size, $total, $uid, $msg = '')
    {
          print_r(array(
                'result' => 'success',
                'message' => $msg,
                'data' => array(
                    'page' => $page,
                    'prePage' => $page_size,
                    'total' => $total,
                    'uid' => $uid,
                    'data' => $data
            ))
        );
        exit;
    }
}