<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/8
 * Time: 11:55
 */
class GameEvent
{
    private $userid = 73;

    public function syncGameInfo($gameid)
    {
        $gameModel = M('tg_game');
        $packageModel = M('tg_package');
        $sourceModel = M('tg_source');
        $gwebGameModel = M('gweb_game');
        $gwebPackage = M('gweb_package');

        $game = $gameModel->where(array('gameid' => $gameid))->find();
        error_log("[".date('Y-m-d H:i:s')."]".print_r($game,true),3,'../a.log');

        $source = $sourceModel->where(array('gameid' => $game['gameid'],'userid' => $this->userid,'activeflag'=>1))->field('sourcesn')->find();

        error_log("[".date('Y-m-d H:i:s')."]".print_r($source,true),3,'../a.log');

        if(empty($source)){
           return false;
        }

        $package = $packageModel->where(array('gameid' => $gameid, 'activeflag' => 1, 'isnowactive' => 1))->order('packageid desc')->find();

        error_log("[".date('Y-m-d H:i:s')."]".print_r($package,true),3,'../a.log');

        if(empty($package)){
           return false;
        }


        $packageFile = C('packageStoreFolder').$game['packagename'];
        error_log("[".date('Y-m-d H:i:s')."]".print_r($packageFile,true),3,'../a.log');

        if(!file_exists($packageFile)){
            return false;
        }

        require_once dirname(__FILE__)."/../Action/apk.class.php";
        $apkobj = new apk();
        $apkobj->open($packageFile);
        $versionCode = $apkobj->getVersionCode();
        if(empty($versionCode)){
            return false;
        }

        $where = array(
            'name' => $game['gamename']
        );

        $gwebGame = $gwebGameModel->where($where)->find();

        $gwebPackage->add(array(
            'gameid' => $gwebGame['id'],
            'name' => $package['packageversion'],
            'size' => $package['gamesize'],
            'vername' => $package['gameversion'],
            'vercode' => $versionCode,
            'createtime' => time()
        ));

        $data = array(
            'extlink' => C('tgdomain').'/publicdownload/'.$source['sourcesn'],
            'pkgsize' => $package['gamesize'].'M',
            'tggameid' => $game['gameid'],
        );
        $gwebGameModel->where($where)->save($data);

        error_log("[".date('Y-m-d H:i:s')."]".print_r($gwebGameModel->getLastSql(),true),3,'../a.log');

    }
}