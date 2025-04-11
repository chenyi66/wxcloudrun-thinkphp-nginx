<?php
declare (strict_types = 1);
namespace app\controller;

use app\model\TimeSlot;
use app\model\DailyCapacity;
use app\model\Appointment;
use think\Request;
use think\response\Html;
use think\response\Json;
use think\facade\Db;
use think\facade\Filesystem;

class AppointmentAdmin
{
    public function index(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/appointment_admin.html'));
    }

    public function updateStatus(Request $request)
    {
        $id = $request->param('id');
        $status = $request->param('status');
        $operator = $request->param('operator');
        $amount = $request->param('amount');
        $remark = $request->param('remark');

        if (!$id || !$status || !$operator) {
            return json(['code' => 1, 'message' => '参数不完整']);
        }

        // 验证状态值是否有效
        $validStatus = [2, 3]; // 2: 已完成, 3: 已取消
        if (!in_array($status, $validStatus)) {
            return json(['code' => 1, 'message' => '无效的状态值']);
        }

        // 如果是完成状态，需要验证金额
        if ($status == 2 && (!$amount || floatval($amount) <= 0)) {
            return json(['code' => 1, 'message' => '请输入有效的项目金额']);
        }

        try {
            $updateData = [
                'status' => $status,
                'operator' => $operator,
                'update_time' => date('Y-m-d H:i:s'),
                'remark' => $remark
            ];

            if ($status == 2) {
                $updateData['amount'] = $amount;
            }

            // 处理图片上传
            $file = $request->file('image');
            if ($file) {
                $savename = Filesystem::disk('public')->putFile('appointment_images', $file);
                if ($savename) {
                    $updateData['image'] = '/storage/' . $savename;
                }
            }

            Db::name('appointments')->where('id', $id)->update($updateData);

            return json(['code' => 0, 'message' => '状态更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '状态更新失败：' . $e->getMessage()]);
        }
    }

    public function getList(Request $request)
    {
        $startDate = $request->param('start_date');
        $endDate = $request->param('end_date');
        $status = $request->param('status');
        $name = $request->param('name');

        $query = Appointment::order('appointment_date DESC, appointment_time DESC');

        if ($startDate) {
            $query = $query->where('appointment_date', '>=', $startDate);
        }
        if ($endDate) {
            $query = $query->where('appointment_date', '<=', $endDate);
        }
        if ($status !== null && $status !== '') {
            $query = $query->where('status', $status);
        }
        if ($name) {
            $query = $query->where('name', 'like', '%'.$name.'%');
        }

        $appointments = $query->select();

        // 格式化状态文字
        foreach ($appointments as &$appointment) {
            $appointment['status_text'] = Appointment::getStatusText($appointment['status']);
        }

        return json(['code' => 0, 'data' => $appointments]);
    }

    public function getDetail(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            return json(['code' => 1, 'message' => '参数不完整']);
        }

        try {
            $appointment = Db::name('appointments')->where('id', $id)->find();
            if (!$appointment) {
                return json(['code' => 1, 'message' => '预约记录不存在']);
            }

            return json(['code' => 0, 'data' => $appointment]);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => '获取详情失败']);
        }
    }
}