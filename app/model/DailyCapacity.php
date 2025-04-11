<?php
declare (strict_types = 1);
namespace app\model;

use think\Model;

class DailyCapacity extends Model
{
    protected $table = 'daily_capacities';
    protected $autoWriteTimestamp = true;

    // 定义关联关系
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}