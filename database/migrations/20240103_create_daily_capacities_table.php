<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateDailyCapacitiesTable extends Migrator
{
    public function change()
    {
        $table = $this->table('daily_capacities');
        $table->addColumn('time_slot_id', 'integer', ['comment' => '时间段ID'])
              ->addColumn('date', 'date', ['comment' => '日期'])
              ->addColumn('capacity', 'integer', ['comment' => '容量'])
              ->addColumn('create_time', 'datetime', ['null' => true])
              ->addColumn('update_time', 'datetime', ['null' => true])
              ->addIndex(['time_slot_id', 'date'], ['unique' => true])
              ->create();
    }
}