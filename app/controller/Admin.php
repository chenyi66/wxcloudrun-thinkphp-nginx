<?php
declare (strict_types = 1);
namespace app\controller;
use think\Request;
use think\response\Html;
use think\response\Json;

class Admin
{
    public function index(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/nav.html'));
    }

    public function statistics(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/appointment_statistics.html'));
    }

    public function appointment(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/appointment_admin.html'));
    }
    public function timeslot(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/timeslot_admin.html'));
    }
    public function member(): Html
    {
        # html路径: ../view/index.html
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/member_admin.html'));
    }
}