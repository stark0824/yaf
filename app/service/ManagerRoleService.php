<?php

class ManagerRoleService {

    public $model;
    public $authModel;

    private $disabledIds = [];
    public function __construct()
    {
        $this->model = new ManagerRoleModel();
        $this->authModel = new ManagerAuthorityModel();
    }

    public function getManagerRoleByIds(array $roleIds) :array
    {
        if(empty($roleIds)) return [];
        $managerRole = $this->model->getManagerRoleByIds($roleIds);
        if ($managerRole) {
            $result = [];
            array_map(function ($value) use (&$result) {
                $result = array_merge($result, explode(',',$value['vm_authority']));
            }, $managerRole);
            $authority = $result;
        } else {
            $authority = [];
        }
        $authList = $this->authModel->getAuthList();
        $isAdministrator = 1; //超级管理员
        return $this->_getMenuList($authList, $authority, $isAdministrator);
    }

    private function _getMenuList(array $authList, array $authority, $isAdministrator, $pid = 0, $level = 0): array
    {
        $menus = [];
        $level ++;
        foreach ($authList as $key => $menu) {
            if ($menu['pid'] != $pid) continue;
            if (!$isAdministrator && (!in_array($menu['id'], $authority) ||
                    in_array($menu['id'], $this->disabledIds))) {
                unset($authList[$key]);
                continue;
            }

            $item = [
                'id' => $menu['id'],
                'title' => $menu['title'],
                'iconClass' => $menu['icon'],
                'name' => $menu['name']
            ];

            if ($level < 3 ) {
                $item['subset'] = $this->_getMenuList($authList, $authority,
                    $isAdministrator, $menu['id'], $level);
            }

            $menus[] = $item;
        }
        return $menus;
    }

    public function getAuthListByAction($controller,$action)
    {
        return $this->authModel->checkAuth($controller,$action);
    }

}