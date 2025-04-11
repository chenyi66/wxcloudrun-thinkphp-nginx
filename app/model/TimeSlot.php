<?php
declare (strict_types = 1);
namespace app\model;

use think\Model;
use think\facade\Db;

class TimeSlot extends Model
{
    // 设置表名
    protected $name = 'time_slots';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'time'        => 'string',
        'capacity'    => 'int',
        'status'      => 'boolean',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 获取所有可用的时间段
    public static function getAvailableSlots()
    {
        return self::where('status', 1)
                   ->order('time', 'asc')
                   ->select();
    }

    // 更新时间段容量
    public static function updateCapacity($id, $capacity)
    {
        return self::where('id', $id)
                   ->update(['capacity' => $capacity]);
    }

    // 更新时间段状态
    public static function updateStatus($id, $status)
    {
        return self::where('id', $id)
                   ->update(['status' => $status]);
    }

    // 检查时间段数量是否超过限制
    public static function checkSlotLimit()
    {
        $count = self::count();
        if ($count >= 8) {
            throw new \Exception('时间段数量已达到上限（8个）');
        }
        return true;
    }

    // 设置指定日期的时间段容量
    public function setDailyCapacity($date, $capacity)
    {
        $dailyCapacity = Db::name('daily_capacities')
            ->where('time_slot_id', $this->id)
            ->where('date', $date)
            ->find();

        if ($dailyCapacity) {
            // 更新已存在的记录
            Db::name('daily_capacities')
                ->where('id', $dailyCapacity['id'])
                ->update(['capacity' => $capacity]);
        } else {
            // 创建新记录
            Db::name('daily_capacities')->insert([
                'time_slot_id' => $this->id,
                'date' => $date,
                'capacity' => $capacity,
                'create_time' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }
}