<?php
declare (strict_types = 1);
namespace app\controller;

//use app\BaseController;
use think\facade\Db;
use think\Request;

class Appointment 
{
    // 获取可预约时间段
    public function getTimeSlots(Request $request)
    {
        $date = $request->param('date');
        if (!$date) {
            return json(['code' => 1, 'message' => '日期不能为空']);
        }

        // 获取可用的时间段和其默认容量
        $timeSlots = Db::table('time_slots')
            ->alias('ts')
            ->where('ts.status', 1)
            ->field(['ts.time', 'ts.capacity as default_capacity'])
            ->order('ts.time', 'asc')
            ->select()
            ->toArray();

        if (empty($timeSlots)) {
            return json(['code' => 0, 'data' => []]);
        }

        // 获取特定日期的容量配置
        $dailyCapacities = Db::table('daily_capacities')
            ->alias('dc')
            ->join('time_slots ts', 'dc.time_slot_id = ts.id')
            ->where('dc.date', $date)
            ->field(['ts.time', 'dc.capacity'])
            ->select()
            ->toArray();

        // 将daily_capacities数据转换为以time为键的关联数组，方便查找
        $dailyCapacityMap = [];
        foreach ($dailyCapacities as $capacity) {
            $dailyCapacityMap[$capacity['time']] = $capacity['capacity'];
        }

        // 获取已预约数量
        $bookedSlots = Db::name('appointments')
            ->where('appointment_date', $date)
            ->field('appointment_time, COUNT(*) as count')
            ->group('appointment_time')
            ->select()
            ->toArray();

        // 将已预约数据转换为以time为键的关联数组
        $bookedSlotsMap = [];
        foreach ($bookedSlots as $slot) {
            $bookedSlotsMap[$slot['appointment_time']] = $slot['count'];
        }

        // 整合数据并计算剩余容量
        foreach ($timeSlots as &$slot) {
            // 优先使用特定日期的容量配置，如果没有则使用默认容量
            $capacity = isset($dailyCapacityMap[$slot['time']]) 
                ? $dailyCapacityMap[$slot['time']] 
                : $slot['default_capacity'];
            
            // 获取已预约数量
            $booked = $bookedSlotsMap[$slot['time']] ?? 0;
            
            // 计算剩余容量
            $remaining = $capacity - $booked;
            
            // 保持原有的数据结构
            $slot['capacity'] = $remaining;
            $slot['available'] = $remaining > 0;
            $slot['remaining'] = $remaining;
            unset($slot['default_capacity']);
        }

        return json(['code' => 0, 'data' => $timeSlots]);
    }

    // 提交预约
    public function submit(Request $request)
    {
        $data = $request->post();

        // 验证必填字段
        if (!$data['name'] || !$data['phone'] || !$data['age'] || 
            !$data['service'] || !$data['date'] || !$data['time']) {
            return json(['code' => 1, 'message' => '请填写完整信息']);
        }

        // 验证手机号格式
        if (!preg_match('/^1[3-9]\d{9}$/', $data['phone'])) {
            return json(['code' => 1, 'message' => '手机号格式不正确']);
        }

        // 验证年龄
        if (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 150) {
            return json(['code' => 1, 'message' => '年龄不合法']);
        }

        // 检查时间段是否已满
        $bookedCount = Db::name('appointments')
            ->where('appointment_date', $data['date'])
            ->where('appointment_time', $data['time'])
            ->count();

        if ($bookedCount >= 10) {
            return json(['code' => 1, 'message' => '该时间段预约已满']);
        }

        // 保存预约信息
        try {
            Db::name('appointments')->insert([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'age' => $data['age'],
                'service' => $data['service'],
                'appointment_date' => $data['date'],
                'appointment_time' => $data['time'],
                'status' => 1, // 1: 已预约
                'create_time' => date('Y-m-d H:i:s')
            ]);

            return json(['code' => 0, 'message' => '预约成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '预约失败，请重试']);
        }
    }

    
}