<?php
declare (strict_types = 1);
namespace app\model;

use think\Model;

class Appointment extends Model
{
    // 设置表名
    protected $name = 'appointments';
    
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'name'            => 'string',
        'phone'           => 'string',
        'age'             => 'int',
        'service'         => 'string',
        'appointment_date' => 'date',
        'appointment_time' => 'string',
        'status'          => 'int',
        'create_time'     => 'datetime',
        'update_time'     => 'datetime'
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 状态常量
    const STATUS_BOOKED = 1;    // 已预约
    const STATUS_COMPLETED = 2;  // 已完成
    const STATUS_CANCELLED = 3;  // 已取消

    // 获取状态文字说明
    public static function getStatusText($status)
    {
        $statusMap = [
            self::STATUS_BOOKED => '已预约',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消'
        ];
        return $statusMap[$status] ?? '未知状态';
    }
}