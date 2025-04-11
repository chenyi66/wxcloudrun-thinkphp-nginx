<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMembersTable extends Migrator
{
    public function change()
    {
        $table = $this->table('members');
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '会员姓名'])
              ->addColumn('phone', 'string', ['limit' => 11, 'comment' => '手机号'])
              ->addColumn('balance', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0.00, 'comment' => '充值金额'])
              ->addColumn('create_time', 'datetime', ['comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
              ->addIndex(['phone'], ['unique' => true, 'name' => 'idx_phone'])
              ->create();
    }
}