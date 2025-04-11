<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

class Member extends Model
{
    protected $name = 'members';
    protected $autoWriteTimestamp = true;

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'phone'       => 'string',
        'balance'     => 'decimal',
        'create_time' => 'datetime',
        'update_time' => 'datetime',
    ];

    // 验证手机号格式
    public static function validatePhone($phone)
    {
        return preg_match('/^1[3-9]\d{9}$/', $phone);
    }

    // 根据手机号查找会员
    public static function findByPhone($phone)
    {
        return self::where('phone', $phone)->find();
    }
}