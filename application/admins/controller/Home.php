<?php
namespace app\admins\controller;
use Util\data\Sysdb;
use app\admins\controller\BaseAdmin;
/**
后台管理页面
 */
class Home extends BaseAdmin{
    public function  index(){
        $menus = false;
        $role = $this->db->table('admin_groups')->where(array('gid'=>$this->_admin['gid']))->item();
        if($role){
            $role['rights'] = (isset($role['rights']) && $role['rights'])?json_decode($role['rights'],true):[];
            //dump($role);
        }
        if($role['rights']){
            //$where = 'mid in('.implode(',',$role['rights']) .') and ishidden=0 ane status=0';
            //sql语句的使用
            $where = 'mid in('.implode(',',$role['rights']).') and ishidden=0 and status=0';
            $menus = $this->db->table('admin_menus')->where($where)->cates('mid');
            $menus && $menus =$this->gettreeitems($menus);
        }
        //dump($menus);
        $this->assign('role',$role);

       $this->assign('menus',$menus);

       //网站名的渲染
        $site = $this->db->table('sites')->where(array('names'=>'site'))->item();
        $site && $site['values'] = json_decode($site['values']);
        $this->assign('site',$site);

        return $this->fetch();
    }

    //分解菜单层级算法
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

    public function welcome(){
        return $this->fetch();
    }
}
