<?php
declare(strict_types=1);

use think\migration\Migrator;
use think\migration\db\Column;

class AddOperatorToAppointments extends Migrator
{
    public function change()
    {
        $table = $this->table('appointments');
        $table->addColumn('operator', 'string', ['limit' => 50, 'null' => true, 'comment' => '操作人员名称'])
              ->update();
    }
}