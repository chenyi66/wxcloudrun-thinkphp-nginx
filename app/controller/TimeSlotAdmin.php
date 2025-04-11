<?php
declare (strict_types = 1);
namespace app\controller;

use app\model\TimeSlot;
use app\model\DailyCapacity;
use think\Request;
use think\response\Html;
use think\response\Json;

class TimeSlotAdmin
{

    public function index(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/timeslot_admin.html'));
    }
    // 获取时间段列表
    public function list()
    {
        $slots = TimeSlot::order('time', 'asc')->select();
        return json(['code' => 0, 'data' => $slots]);
    }

    // 更新时间段容量
    public function updateCapacity(Request $request)
    {
        $id = $request->param('id');
        $capacity = $request->param('capacity');

        if (!$id || !is_numeric($capacity) || $capacity < 0) {
            return json(['code' => 1, 'message' => '参数错误']);
        }

        try {
            TimeSlot::updateCapacity($id, $capacity);
            return json(['code' => 0, 'message' => '更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '更新失败：' . $e->getMessage()]);
        }
    }

    // 更新时间段状态
    public function updateStatus(Request $request)
    {
        $id = $request->param('id');
        $status = $request->param('status');

        if (!$id || !in_array($status, [0, 1])) {
            return json(['code' => 1, 'message' => '参数错误']);
        }

        try {
            TimeSlot::updateStatus($id, $status);
            return json(['code' => 0, 'message' => '更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '更新失败：' . $e->getMessage()]);
        }
    }

    // 添加新时间段
    public function add(Request $request)
    {
        $time = $request->param('time');
        $capacity = $request->param('capacity', 2);

        if (!$time || !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return json(['code' => 1, 'message' => '时间格式错误']);
        }

        try {
            $slot = new TimeSlot;
            $slot->time = $time;
            $slot->capacity = $capacity;
            $slot->save();
            return json(['code' => 0, 'message' => '添加成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '添加失败：' . $e->getMessage()]);
        }
    }

    // 删除时间段
    public function delete(Request $request)
    {
        $id = $request->param('id');

        if (!$id) {
            return json(['code' => 1, 'message' => '参数错误']);
        }

        try {
            TimeSlot::destroy($id);
            return json(['code' => 0, 'message' => '删除成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '删除失败：' . $e->getMessage()]);
        }
    }

    // 更新指定日期的时间段容量
    public function updateDailyCapacity(Request $request)
    {
        $date = $request->param('date');
        $id = $request->param('id');
        $capacity = $request->param('capacity');

        if (!$date || !$id || !is_numeric($capacity) || $capacity < 0) {
            return json(['code' => 1, 'message' => '参数错误']);
        }

        try {
            // 检查时间段是否存在
            $timeSlot = TimeSlot::find($id);
            if (!$timeSlot) {
                return json(['code' => 1, 'message' => '时间段不存在']);
            }

            // 更新或创建特定日期的容量记录
            $dailyCapacity = DailyCapacity::where([
                'time_slot_id' => $id,
                'date' => $date
            ])->findOrEmpty();

            if ($dailyCapacity->isEmpty()) {
                $dailyCapacity = new DailyCapacity;
                $dailyCapacity->time_slot_id = $id;
                $dailyCapacity->date = $date;
            }
            $dailyCapacity->capacity = $capacity;
            $dailyCapacity->save();

            return json(['code' => 0, 'message' => '更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '更新失败：' . $e->getMessage()]);
        }
    }
}