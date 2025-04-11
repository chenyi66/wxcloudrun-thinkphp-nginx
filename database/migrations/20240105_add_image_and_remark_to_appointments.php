<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddImageAndRemarkToAppointments extends Migrator
{
    public function change()
    {
        $table = $this->table('appointments');
        $table->addColumn('image', 'string', ['limit' => 255, 'null' => true, 'comment' => '上传的图片路径'])
              ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注信息'])
              ->update();
    }
}