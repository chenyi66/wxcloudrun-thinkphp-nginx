<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAppointmentsTable extends Migrator
{
    public function change()
    {
        $table = $this->table('appointments');
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '预约人姓名'])
              ->addColumn('phone', 'string', ['limit' => 11, 'comment' => '联系电话'])
              ->addColumn('age', 'integer', ['signed' => false, 'comment' => '年龄'])
              ->addColumn('service', 'string', ['limit' => 100, 'comment' => '预约服务项目'])
              ->addColumn('appointment_date', 'date', ['comment' => '预约日期'])
              ->addColumn('appointment_time', 'string', ['limit' => 5, 'comment' => '预约时间'])
              ->addColumn('status', 'integer', ['default' => 1, 'limit' => 1, 'comment' => '状态：1已预约 2已完成 3已取消'])
              ->addColumn('create_time', 'datetime', ['comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
              ->addIndex(['appointment_date', 'appointment_time'], ['name' => 'idx_appointment_datetime'])
              ->create();
    }
}