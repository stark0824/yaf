<?php
// +----------------------------------------------------------------------
// | Author:stark
// +----------------------------------------------------------------------
// | Date:	2023/02/14
// +----------------------------------------------------------------------

class ApiUser extends ApiBase
{

    private $_userInfo = [];
    public $userInfo = [];
    public $publicAction = [
        '/manager/getinfo',
        '/authority/getmangermenu', // 获取菜单
        '/manager/managerlogout', // 登出操作
    ];
    private $_authorityParams = [];
    private $_authorityIds = [];

    final public function init()
    {
        parent::init();
        $this->userInfo = $this->getUserInfo();
        if (empty($this->userInfo)) {

            $this->responseJson(9996);

        } else {
            $controller = strtolower($this->getRequest()->controller);
            $action = strtolower($this->getRequest()->action);
            $this->checkAuthority($controller, $action);
        }
    }

    private function checkAuthority(string $controller, string $action)
    {
        if ($controller === 'filters') {
            $res = true;
        } else if (in_array("/{$controller}/{$action}", $this->publicAction, true)) {
            $res = true;
        } else {
            $res = false;

            $roleId = $this->userInfo['role_id'];
            $roleIds = $roleId ? explode(',', $roleId) : [];

            $service = new ManagerRoleService();
            $managerRole = $service->getManagerRoleByIds($roleIds);

            if (!empty($managerRole)) {
                $result = [];
                array_map(function ($value) use (&$result) {
                    if(isset($value['vm_authority'])){
                        $result = array_merge($result, explode(',', $value['vm_authority']));
                    }
                }, $managerRole);
                $authority = $result;
            } else {
                $authority = [];
            }

            $this->_authorityIds = $authority;

            if ($this->_authorityIds || $this->userInfo['is_administrator']) {
                $manager = new ManagerRoleService();
                $authorityList = $manager->getAuthListByAction($controller,$action);
                $this->_authorityParams = [];

                foreach ($authorityList as $authority) {
                    if (in_array($authority['id'], $this->_authorityIds, true)
                        || $this->userInfo['is_administrator']) {
                        $res = true;
                        $this->_authorityParams[] = $authority['param'] ?? [];
                    }
                }
            }
        }

        if (!$res) {
            $this->responseJson(7000);
        }
    }

    protected function examineAuthorityParam(string $param)
    {
        return in_array($param, $this->_authorityParams, true);
    }

    protected function examineAuthority(string $controller, string $action, string $param = '')
    {
        $search = [
            'where' => [
                'delete' => 0,
                'controller' => $controller,
                'action' => $action
            ]
        ];
        $authorityList = ChangpeiModule_Cpwxw_Admin_VmManagerAuthority::getInstance()
            ->getAllByPrepareSql([], $search, true);

        $authorityParams = [];
        foreach ($authorityList as $authority) {
            if (in_array($authority['id'], $this->_authorityIds, true)
                || $this->userInfo['is_administrator']) {
                $authorityParams[] = $authority['param'];
            }
        }

        if ($param) {
            return in_array($param, $authorityParams, true);
        } else {
            return (bool)$authorityParams;
        }
    }

    protected function getUserInfo(): array
    {
        $managerId = $_SESSION['manager_id'] ?? 0;

        if (!$this->_userInfo && $managerId) {
            $model = new ManagerService();
            $this->_userInfo  = $model->getManagerById($managerId);
        }
        return $this->_userInfo;
    }

}
