<?php

class ManagerService {

    public $model;
    public function __construct()
    {
        $this->model = new ManagerModel();
    }

    public function checkUserAndPassword($username,$password): int
    {
        $manager = $this->model->checkUser($username);
        if(!$manager){
           return 4220;
        }

        $managerId = $manager['id'];
        if($manager['password'] != $this->_encryptPassword($password,$managerId)){
            return 4220;
        }

        $_SESSION['manager_id'] = $manager['id'];
        return 200;
    }

    private function _encryptPassword($password,$managerId): string
    {
        return md5(md5($password) . md5($managerId));
    }

    public function getManagerById($id)
    {
        if(empty($id)) return [];
        return $this->model->getManagerById($id);
    }
}