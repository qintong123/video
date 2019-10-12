<?php


namespace app\admins\controller;
use think\Controller;
use Util\data\Sysdb;


class Admin extends BaseAdmin
{
    //管理员列表
    public function index(){
        $data['lists'] = $this->db->table('admins')->lists();
        $data['groups'] = $this->db->table('admin_groups')->lists();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //添加管理员
    function add(){
        $id = (int)input('get.id');
        //加载管理员
        $data['item'] = $this->db->table('admins')->where(array('id'=>$id))->item();
        //dump($data);
        //exit();
        //加载角色
        $data['groups'] = $this->db->table('admin_groups')->cates('gid');
        $this->assign('data',$data);
        return $this->fetch();
    }

    //保存管理员
    public function save(){
        $id = (int)input('post.id');
        $data['username'] = trim(input('post.username'));
        $data['gid'] =(int)trim(input('post.gid'));
        $password = trim(input('post.pwd'));
        $data['truename'] = trim(input('post.truename'));
        $data['status'] = (int)(input('post.status'));
        //校验
        if(!$data['username']){
            exit(json_encode(array('code'=>1,'msg'=>'用户名不能为空')));
        }
        if(!$data['gid']){
            exit(json_encode(array('code'=>1,'msg'=>'角色不能为空')));
        }
        if($id == 0 && !$password){
            exit(json_encode(array('code'=>1,'msg'=>'密码不能为空')));
        }
        if(!$data['truename']){
            exit(json_encode(array('code'=>1,'msg'=>'姓名不能为空')));
        }

        //密码的加密 md5的方式
        if($password){
            $data['password'] = md5($data['username']).$password;
        }
        //预防不修改的时出现的错误
        $res = true;
        if($id == 0) {
            //检查用户是否已存在
            $item = $this->db->table('admins')->where(array('username' => $data['username']))->item();
            if ($item) {
                exit(json_encode(array('code' => 1, 'msg' => '该用户已存在')));
            }
            //保存用户
            $data['add_time'] = time();
            $res = $this->db->table('admins')->insert($data);
        }else{
            $this->db->table('admins')->where(array('id'=>$id))->update($data);
        }

        if(!$res){
            exit(json_encode(array('code'=>1,'msg'=>'保存失败')));
        }
        exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
        //查看传输的类型
        //dump($data);
    }
    //删除
    public  function delete($id){
        $id = (int)input('post.id');
        $res = $this->db->table('admins')->where(array('id'=>$id))->delete();
        if (!$res)
        {
            exit(json_encode(array('code'=>1,'msg'=>'删除失败')));
        }
        exit(json_encode(array('code'=>0,'msg'=>'删除成功')));
    }
}