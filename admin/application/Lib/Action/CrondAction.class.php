<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/10
 * Time: 11:17
 */
class CrondAction extends Action
{


    /**
     * 个人推广统计
     */
    public function sdktg()
    {
        $event = A('SdkTg','Event');
        $event->run();
    }
}