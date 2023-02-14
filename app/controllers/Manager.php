<?php

class ManagerController extends ApiUser
{
    public function getInfoAction()
    {
        $managerInfo = [
            'email' => $this->userInfo['email'],
            'job' => $this->userInfo['job'],
            'mobile' => $this->userInfo['mobile'],
            'nickname' => $this->userInfo['nickname'],
            'username' => $this->userInfo['username'],
            'role_id' => explode(',', $this->userInfo['role_id']),
            'department_ids' => explode(',', $this->userInfo['department_ids'])
        ];

        $this->responseJson(200, ['data' => $managerInfo]);
    }


}
