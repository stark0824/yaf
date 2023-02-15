<?php

class AuthorityController extends ApiUser {
    public function getMangerMenuAction()
    {
        $roleId = $this->userInfo['role_id'];
        $roleIds = $roleId ? explode(',', $roleId) : [];
        $service = new ManagerRoleService();
        $menuList = $service->getManagerRoleByIds($roleIds);
        $this->responseJson(200, ['list' => $menuList, 'topActive' => 0]);
    }
}
