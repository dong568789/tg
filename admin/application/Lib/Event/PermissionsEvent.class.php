<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/3
 * Time: 14:34
 */
class PermissionsEvent
{
    /**
     * 获取菜单项
     * @return array
     */
    public function  getMenu($uri)
    {
        $menu = $this->getUserPermissions();
        $itemMenu = array();
        foreach($menu as $value){
            if($value['status']  > 0)
                continue;

            if($value['parent'] == 0){
                $itemMenu[$value['id']]['id'] = $value['id'];
                $itemMenu[$value['id']]['title'] = $value['title'];
                $itemMenu[$value['id']]['url'] = $value['url'];
                $itemMenu[$value['id']]['parent'] = $value['parent'];
                $itemMenu[$value['id']]['icon'] = $value['icon'];
            }else{
                $itemMenu[$value['parent']]['children'][$value['id']] = $value;

                if($uri == $value['url']){
                    $itemMenu[$value['parent']]['active'] = true;
                    $itemMenu[$value['parent']]['children'][$value['id']]['active'] = true;
                }
            }
        }

        ksort($itemMenu);
        return $itemMenu;
    }


    public function getUserPermissions()
    {
        $adminpermissions = S('adminpermissions');
        if(empty($adminpermissions)){
            $departmentModel = M('sys_department');
            $menumodel = M('sys_menu');

            $admin = $this->getUserById($_SESSION['adminid']);

            if(empty($admin['department_id'])){
                return array();
            }

            $where = array(
                'id' => $admin['department_id']
            );
            $dep = $departmentModel->where($where)->field('id,menuids')->find();

            $arrMenu = explode(',', $dep['menuids']);

            $where = array(
                'id' => array('in', $arrMenu),
                'type' => 11
            );
            $menu = $menumodel->where($where)
                ->field("id,(CASE WHEN `first` > '' THEN `first` ELSE `second` END) as title,parentsid as parent,status,url,icon")
                ->select();

            //更新用户父节点
            $parentMenu = $this->updateParent($menu, $arrMenu);
            $menu = array_merge($menu, $parentMenu);
            $adminpermissions = array();
            foreach($menu as $value){
                $adminpermissions[$value['id']] = $value;
            }
            S('adminpermissions',$adminpermissions);
        }
        //print_r($adminpermissions);exit;
        return $adminpermissions;
    }

    public function getUserById($userid)
    {
        $sysUserInfo = session('sys_user_info');
        if(empty($sysUserInfo)){
            $usermodel = M('sys_admin');

            $where = array(
                'id' => $userid
            );
            $sysUserInfo = $usermodel->where($where)->field('id,department_id,status')->find();
            session('sys_user_info', $sysUserInfo);
        }
        return $sysUserInfo;

    }

    public function updateParent(&$menu, $allperm)
    {
        $item = array();
        foreach($menu as $value){
            if($value['parent'] <> 0 && !in_array($value['parent'], $allperm)){
                $item[] = $value['parent'];
            }
        }

        if(empty($item)){
            return array();
        }

        $menumodel = M('sys_menu');
        $where = array(
            'id' => array('in', array_unique($item)),
            'status' => 0,
            'type' => 11
        );
        $parentMenu = $menumodel->where($where)
            ->field("id,(CASE WHEN `first` > '' THEN `first` ELSE `second` END) as title,parentsid as parent,status,url,icon")
            ->select();

        return (array)$parentMenu;
    }
}