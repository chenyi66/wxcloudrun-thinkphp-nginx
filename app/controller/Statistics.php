<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Appointment;
use think\facade\Db;
use think\response\Html;
use think\response\Json;

class Statistics
{
    public function index(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/appointment_statistics.html'));
    }
    /**
     * 获取按姓名统计的预约数据
     */
    public function getStatistics()
    {
        $data = input('post.');
        $name = $data['name'] ?? '';

        try {
            $query = Db::table('appointments')
                ->field([
                    'name',
                    'COUNT(*) as count',
                    'SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as total_amount'
                ])
                ->group('name');

            if ($name) {
                $query->where('name', 'like', '%'.$name.'%');
            }

            $statistics = $query->select();

            return json(['code' => 0, 'data' => $statistics]);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 获取指定姓名的预约详情
     */
    public function getDetail()
    {
        $data = input('post.');
        $name = $data['name'] ?? '';

        if (empty($name)) {
            return json(['code' => 1, 'message' => '姓名不能为空']);
        }

        try {
            $appointments = Db::table('appointments')
                ->where('name', 'like', '%'.$name.'%')
                ->select()
                ->map(function ($item) {
                    $item['status_text'] = $this->getStatusText($item['status']);
                    return $item;
                });

            return json(['code' => 0, 'data' => $appointments]);
        } catch (\Exception $e) {
            return json(['code' => 1, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 获取状态文本
     */
    private function getStatusText($status)
    {
        $statusMap = [
            1 => '待完成',
            2 => '已完成',
            3 => '已取消'
        ];
        return $statusMap[$status] ?? '未知';
    }
}