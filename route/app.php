<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('/', 'Index/index');
Route::get('/db/test', 'DbTest/test');
Route::get('/api/appointment/getTimeSlots', 'Appointment/getTimeSlots');
Route::post('/api/appointment/submit', 'Appointment/submit');

// 时间段管理接口
Route::get('/api/timeslot/list', 'TimeSlotAdmin/list');
Route::post('/api/timeslot/add', 'TimeSlotAdmin/add');
Route::post('/api/timeslot/updateCapacity', 'TimeSlotAdmin/updateCapacity');
Route::post('/api/timeslot/updateStatus', 'TimeSlotAdmin/updateStatus');
Route::post('/api/timeslot/delete', 'TimeSlotAdmin/delete');
Route::post('/api/timeslot/updateDailyCapacity', 'TimeSlotAdmin/updateDailyCapacity');

Route::post('/appointment/updateStatus', 'AppointmentAdmin/updateStatus');
Route::post('/appointment/getList', 'AppointmentAdmin/getList');
Route::get('/appointment/getDetail', 'AppointmentAdmin/getDetail');
Route::post('/appointment/create', 'AppointmentAdmin/create');


// 会员管理接口
Route::post('/api/member/getList', 'MemberAdmin/getList');
Route::post('/api/member/add', 'MemberAdmin/add');
Route::post('/api/member/update', 'MemberAdmin/update');
Route::post('/api/member/delete', 'MemberAdmin/delete');
Route::get('/api/member/detail', 'MemberAdmin/detail');

