<?php

class IndexController extends Yaf_Controller_Abstract {

    public function init() {
		$this->getView()->assign("header", "Yaf Example");
	}


	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

        $password = '123456';
        $id = 1;
        echo md5(Index . phpmd5($password) . md5($id));die();

		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
}
