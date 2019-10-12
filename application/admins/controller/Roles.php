<?php
/**
 * 角色管理
 */
namespace app\admins\controller;
use app\admins\controller\BaseAdmin;

class Roles extends BaseAdmin{

    // 角色列表
    public function index(){
        $data['roles'] = $this->db->table('admin_groups')->lists();
        $this->assign('data',$data);
        return $this->fetch();
    }

    // 角色添加 角色需要有权限  菜单
    public function add(){
        //gid用于判断是编辑还是添加
        $gid = (int)input('get.gid');
        $role = $this->db->table('admin_groups')->where(array('gid'=>$gid))->item();
        $role && $role['rights'] && $role['rights'] = json_decode($role['rights']);
        $this->assign('role',$role);
        //获取菜单 对于条件的判断  cates函数的使用
        $menu_list = $this->db->table('admin_menus')->where(array('status'=>0))->cates('mid');
        $menus = $this->gettreeitems($menu_list);
        $results = array();
        foreach ($menus as $value) {
            $value['children'] = isset($value['children'])?$this->formatMenus($value['children']):false;
            $results[] = $value;
        }
        $this->assign('menus',$results);
        return $this->fetch();
    }

    //分解菜单的层级（算法）
    private function gettreeitems($items){
        $tree = array();
        foreach ($items as $item) {
            if(isset($items[$item['pid']])){
                $items[$item['pid']]['children'][] = &$items[$item['mid']];
            }else{
                $tree[] = &$items[$item['mid']];
            }
        }
        return $tree;
    }

    //将所有子菜单提取到一个子菜单中，递归函数的使用
    private function formatMenus($items,&$res = array()){
        foreach($items as $item){
            if(!isset($item['children'])){
                $res[] = $item;
            }else{
                $tem = $item['children'];
                unset($item['children']);
                $res[] = $item;
                $this->formatMenus($tem,$res);
            }
        }
        return $res;
    }

    //保存文件
    public function save(){
        $gid = (int)input('post.gid');

        //trim取消空白符
        $data['title'] = trim(input('post.title'));
        $menus = input('post.menu/a');
        if(!$data['title']){
            exit(json_encode(array('code'=>1,'msg'=>'角色名称不能为空')));
        }
        //array_keys获取数组的键值
        $menus && $data['rights'] = json_encode(array_keys($menus));
        //修改和保存 传递的值不一样
        if($gid){
            $this->db->table('admin_groups')->where(array('gid'=>$gid))->update($data);
        }else{
            $this->db->table('admin_groups')->insert($data);
        }

        exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
    }

    // 删除  实际开发过程中用户需要判断用户使用的角色 然后再进行删除操作
    public function deletes(){
        $gid = (int)input('gid');
        $this->db->table('admin_groups')->where(array('gid'=>$gid))->delete();
        exit(json_encode(array('code'=>0,'msg'=>'删除成功')));
    }
}