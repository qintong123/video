<?php
namespace app\admins\controller;
use think\Controller;
use think\Request;
use Util\data\Sysdb;


class BaseAdmin extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->_admin = session('admin');
        //未登录的用户不允许访问
        if(!$this->_admin){
            header('Location: /admins.php/admins/Account/login');
            exit;
        }
        //判断用户是否有权限
        $this->assign('admin',$this->_admin);
        $this->db = new Sysdb();
    }
}