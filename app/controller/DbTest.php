<?php
declare (strict_types = 1);
namespace app\controller;

use think\facade\Db;

class DbTest
{
    public function test()
    {
        try {
            // 尝试执行一个简单的查询来测试连接
            Db::query('SELECT 1');
            return json(['code' => 0, 'message' => '数据库连接成功']);
        } catch (\Exception $e) {
            // 返回详细的错误信息
            return json([
                'code' => -1,
                'message' => '数据库连接失败',
                'error' => $e->getMessage(),
                'config' => [
                    'host' => getenv('MYSQL_ADDRESS'),
                    'username' => getenv('MYSQL_USERNAME'),
                    // 出于安全考虑，不返回实际密码
                    'password_set' => !empty(getenv('MYSQL_PASSWORD'))
                ]
            ]);
        }
    }
}