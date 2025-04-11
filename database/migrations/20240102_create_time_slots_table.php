<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTimeSlotsTable extends Migrator
{
    public function change()
    {
        $table = $this->table('time_slots');
        $table->addColumn('time', 'string', ['limit' => 5, 'comment' => '时间段'])
              ->addColumn('capacity', 'integer', ['signed' => false, 'default' => 2, 'comment' => '容量'])
              ->addColumn('status', 'boolean', ['signed' => false, 'default' => 1, 'comment' => '状态：1启用，0禁用'])
              ->addColumn('create_time', 'datetime', ['null' => true])
              ->addColumn('update_time', 'datetime', ['null' => true])
              ->addIndex(['time'], ['unique' => true])
              ->create();

        // 插入默认时间段
        $defaultSlots = [
            ['time' => '08:30', 'capacity' => 2],
            ['time' => '09:40', 'capacity' => 2],
            ['time' => '10:50', 'capacity' => 2],
            ['time' => '14:30', 'capacity' => 2],
            ['time' => '15:50', 'capacity' => 2],
            ['time' => '17:30', 'capacity' => 2],
            ['time' => '18:50', 'capacity' => 2],
            ['time' => '20:00', 'capacity' => 2]
        ];

        $this->table('time_slots')->insert($defaultSlots)->save();
    }
}