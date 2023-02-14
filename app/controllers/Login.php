<?php
class loginController extends ApiBase {
    public function loginHandleAction()
    {
        $this->checkRequestMethod("post");

        $this->checkData(['username', 'password']);
        $requestData = $this->getRequestData();

        $username = $requestData['username'];
        $password = $requestData['password'];

        $model = new ManagerService();
        $code = $model->checkUserAndPassword($username,$password);
        $this->responseJson($code);
	}

    public function checkLoginAction()
    {
        $data = [
            'isLogin' => 0,
            'useCode' => 0
        ];
        $this->responseJson(200, $data);
    }
}
