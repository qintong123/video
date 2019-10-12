<?php
/**
 * 影片标签
 */
namespace app\admins\controller;
use app\admins\controller\BaseAdmin;
use think\Controller;
use Util\data\Sysdb;

class Labels extends  BaseAdmin
{
    //频道管理
    public function channel(){
        return $this->fetch();
    }
    //资费
    public function charge(){
        return $this->fetch();
    }
    //地区
    public function area(){
        return $this->fetch();
    }
    //保存
    public  function save(){
        $pid = (int)input('post.pid');
        $ords = input('post.ords/a');
        $titles = input('post.titles/a');
        $constrollers = input('post.controllers/a');
        $methods = input('post.methods/a');
        $ishiddens = input('post.ishiddens/a');
        $status = input('post.status/a');
        //dump($ords);
        foreach ($ords as $key => $value){
            //新增
            $data['pid'] = $pid;
            $data['ord'] = $value;
            $data['title'] = $titles[$key];
            $data['controller'] =$constrollers[$key];
            $data ['method'] = $methods[$key];
            $data['ishidden'] = isset($ishiddens[$key])? 1 : 0;
            $data['status'] = isset($status[$key])? 1 :0;
            //判断条件的重要性 实现添加操作
            if($key==0 && $data['title']){
                $this->db->table('video_lable')->insert($data);
                exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
            }
            if($key > 0){
                if($data['title']==''){
                    //删除
                    $this->db->table('video_lable')->where(array('mid'=>$key))->delete();
                }else{
                    //修改
                    $this->db->table('video_lable')->where(array('mid'=>$key))->update($data);
                }
            }
        }
        exit(json_encode(array('code'=>0,'msg'=>'操作成功')));
    }
}