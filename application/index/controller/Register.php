<?php

namespace app\index\controller;

use think\Request;
use think\facade\Session;
use app\common\model\Sms;
use app\common\model\User;
use app\common\exception\ValidateException;

class Register extends Base
{
    public function create()
    {
        return $this->fetch('create');
    }

    public function save(Request $request)
    {
        if (!$request->isPost() || !$request->isAjax()) {
            $message = '对不起，你访问页面不存在。';
            // 在跳转前把错误提示消息写入 session 里
            Session::flash('danger', $message);
            return $this->error($message);
        }

        try {
            // 保存表单提交数据
            $param = $request->post();
            $user = User::register($param);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), null, ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            return $this->error('对不起，注册失败。');
        }

        $message = '恭喜你注册成功。';
        // 在调用 success 返回前把注册成功提示消息写入 session 里
        Session::flash('success', $message);
        return $this->success($message, '[page.root]');
    }

    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     */
    public function check_unique(Request $request)
    {
        if(!$request->isAjax()){
            return $this->redirect('[page.signup]');
        }

        $param = $request->post();
        $is_valid = User::checkFieldUnique($param);
        if($is_valid){
            echo("true");
        }else{
            echo("false");
        }
    }

    /**
     * 发送注册验证码
     * @Author   zhanghong(Laifuzi)
     */
    public function send_code(Request $request)
    {
        if(!$request->isAjax()){
            return $this->redirect('[page.signup]');
        }else if(!$request->isPost()){
            return $this->error('对不起，你访问页面不存在。');
        }

        $mobile = $request->post('mobile');
        if(empty($mobile)){
            return $this->error('对不起，注册手机号码不能为空。');
        }
        $param = ['name' => 'mobile', 'mobile' => $mobile];
        if(User::checkFieldUnique($param)){
            return $this->error('对不起，你填写的手机号码已注册。');
        }

        try {
            $sms = new Sms();
            $sms->sendCode($mobile);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('验证码发送成功。');
    }
}
