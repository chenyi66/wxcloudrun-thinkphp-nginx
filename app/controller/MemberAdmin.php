<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Member;
use think\facade\Validate;
use think\Request;
use think\response\Html;
use think\response\Json;

class MemberAdmin
{

    public function index(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/member_admin.html'));
    }
    // 获取会员列表
    public function getList()
    {
        $page = input('page/d', 1);
        $limit = input('limit/d', 10);
        $keyword = input('keyword', '');

        $query = Member::order('id', 'desc');
        if ($keyword) {
            $query->where('name|phone', 'like', "%{$keyword}%");
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->select();

        // 获取每个会员的消费总金额
        foreach ($list as $member) {
            $totalSpent = \think\facade\Db::name('appointments')
                ->where('name', $member->name)
                ->where('status', 2) // 只统计已完成的预约
                ->sum('amount');
            $member->total_spent = $totalSpent ?: 0;
            $member->remaining_balance = $member->balance - $member->total_spent;
        }

        return json(['code' => 0, 'msg' => 'success', 'data' => [
            'total' => $total,
            'items' => $list
        ]]);
    }

    // 添加会员
    public function add()
    {
        $data = input('post.');
        
        // 验证数据
        $validate = Validate::rule([
            'name' => 'require|max:50',
            'phone' => 'require|length:11',
            'balance' => 'float|egt:0'
        ]);

        if (!$validate->check($data)) {
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }

        // 验证手机号格式
        if (!Member::validatePhone($data['phone'])) {
            return json(['code' => 1, 'msg' => '手机号格式不正确']);
        }

        // 检查手机号是否已存在
        if (Member::findByPhone($data['phone'])) {
            return json(['code' => 1, 'msg' => '该手机号已注册']);
        }

        // 创建会员
        $member = new Member;
        if ($member->save($data)) {
            return json(['code' => 0, 'msg' => '添加成功']);
        }
        return json(['code' => 1, 'msg' => '添加失败']);
    }

    // 更新会员信息
    public function update()
    {
        $data = input('post.');
        
        // 验证数据
        $validate = Validate::rule([
            'id' => 'require|number',
            'name' => 'require|max:50',
            'phone' => 'require|length:11',
            'balance' => 'float|egt:0'
        ]);

        if (!$validate->check($data)) {
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }

        // 验证手机号格式
        if (!Member::validatePhone($data['phone'])) {
            return json(['code' => 1, 'msg' => '手机号格式不正确']);
        }

        // 检查手机号是否已被其他会员使用
        $existMember = Member::findByPhone($data['phone']);
        if ($existMember && $existMember->id != $data['id']) {
            return json(['code' => 1, 'msg' => '该手机号已被其他会员使用']);
        }

        // 更新会员信息
        $member = Member::find($data['id']);
        if (!$member) {
            return json(['code' => 1, 'msg' => '会员不存在']);
        }

        if ($member->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    // 删除会员
    public function delete()
    {
        $id = input('post.id/d');
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }

        $member = Member::find($id);
        if (!$member) {
            return json(['code' => 1, 'msg' => '会员不存在']);
        }

        if ($member->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }
}