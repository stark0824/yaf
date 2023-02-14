<?php
// +----------------------------------------------------------------------
// | Author:stark
// +----------------------------------------------------------------------
// | Date:	2023/02/11
// +----------------------------------------------------------------------
// | Desc:	Base 类
// +----------------------------------------------------------------------
class ApiBase extends Yaf_Controller_Abstract
{
    private $_requestData;
    private $_headerData;
    private $_method;
    private $_userInfo = [];
    private $_action;
    public function init()
    {
        // 允许跨域
        $allowOrigin = '*';
        header("Access-Control-Allow-Origin: {$allowOrigin}");
        header("Access-Control-Allow-Methods:POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Max-Age: 7200");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers:  Origin, Content-Length, Authorization, X-Requested-With, Content-Type, Accept, X_Requested_With");

        // 禁用视图
        Yaf_Dispatcher::getInstance()->disableView();

        $this->_action = strtolower($this->getRequest()->controller) . '/' . strtolower($this->getRequest()->action);
        $this->checkIpWhitelist();

        $this->_method = strtoupper($this->getRequest()->method);
        switch ($this->_method) {
            case "POST":
                $post = file_get_contents('php://input');
                $this->_requestData = json_decode($post, true);
                break;
            case "GET":
            case "DELETE":
                $this->_requestData = array_merge($this->getRequest()->getParams(), $this->getRequest()->getQuery());
                break;
            case "PUT":
                //提交的数据为json
                $put = file_get_contents('php://input');
                $this->_requestData = json_decode($put, true);
                break;
            default:
                $this->responseJson(405, "");
        }
    }

    public function getRequestData()
    {
        return $this->_requestData;
    }

    public function getHeaderData()
    {
        return $this->_headerData;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * 获取用户信息
     * @return array
     */
    protected function getUserInfo(): array
    {
        $mid = $_SESSION['manager_id'] ?? 0;
        if (!$this->_userInfo && $mid) {
            $this->_userInfo = ChangpeiModule_Cpwxw_Admin_Manager::getInstance()->getManagerById($mid);
        }
        return $this->_userInfo;
    }

    protected function getAction(): string
    {
        return $this->_action;
    }

    public function checkRequestMethod($requestMethod)
    {
        if ($this->_method != strtoupper($requestMethod)) {
            $this->responseJson(405);
        }
    }

    protected function checkData(array $data = [])
    {
        $checkData = [];
        $checkData = array_merge($checkData, $data);
        $this->_requestData = $this->_requestData ? $this->_requestData : [];
        $keys = array_keys($this->_requestData);
        foreach ($checkData as $v) {
            if (!in_array($v, $keys)) {
               // $res = APPENV == 2 ? [] : ['_msg_' => "缺少参数“{$v}”"];
                $this->responseJson(4203);
            }
        }
    }


    /**
     * @param $code
     * @param array $res
     * @param int $obj 0 默认 1 强转对象
     *@return void
     */
    protected function responseJson($code, array $res = [], int $obj = 0)
    {
        $this->response($code, $res, $obj);
        $this->getResponse()->response();
        exit();
    }

    /**
     * @param $code
     * @param array $res
     * @param int $obj 默认 1 强转对象
     */
    protected function response($code, array $res = [], int $obj = 0)
    {
        $responseData = [];
        if ($code != 200) {
            $obj = 1;
            $res = $res ? $res : [];
        }

        if (isset($res['_msg_'])) {
            $msg = $res['_msg_'];
            unset($res['_msg_']);
        } else {
            //$msg = Message::getMessage($code);
        }

        $responseData['code'] = $code;
        //$responseData['msg'] = $msg;
        $responseData['data'] = $res;

        $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
        if ($obj) {
            $this->getResponse()->setBody(json_encode($responseData, JSON_FORCE_OBJECT));
        } else {
            $this->getResponse()->setBody(json_encode($responseData));
        }
    }

    /**
     * 下载响应 header
     * @param $filename
     * @param $content
     */
    protected function responseAttachment($filename, $content)
    {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');

        echo $content;
        die();
    }

    /**
     * 操作日志
     * @param $remark
     */
    protected function log($remark)
    {
        $data = [
            "manager_id" => $_SESSION['manager_id'] ?? 0,
            "remark" => $remark,
            "requestData" => $this->_requestData,
        ];
        $header = $this->getRequest()->controller . '/' . $this->getRequest()->action;

        $hideParams = ['password', 'user_psw'];
        foreach ($hideParams as $key) {
            if (isset($data['requestData'][$key])) {
                $data['requestData'][$key] = '******';
            }
        }

        ChangpeiLib_Log_Log::info("admin", $header, json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    private function checkIpWhitelist()
    {
        $publicActions = [
            'login/loginhandle',
            'login/checklogin',
            'login/sendcode'
        ];

        if (!in_array($this->_action, $publicActions, true)) {
//            if (!ChangpeiModule_Cpwxw_Common_Config::getInstance()->checkIpWhitelist()) {
//                $_SESSION['manager_id'] = 0;
//                $this->responseJson(9996);
//            }
        }
    }

    /**
     * 导出 csv
     * @param $list
     * @param $title
     * @param $filename
     */
    protected function exportCsv($list, $title, $filename)
    {
        $string = "";
        foreach ($list as $key => $value) {
            $row = [];

            foreach ($title as $k => $val) {
                $cellValue = $value[$k] ?? '';

                $cell = iconv('utf-8', 'gbk//IGNORE', $cellValue);
                // 大于 1 亿 的数字加 \t，否则会变成科学计数法
                if (is_numeric($cell) && $cell > 99999999) {
                    $row[] = '"' . str_replace('"', '\"', $cell) . "\t" . '"';
                } else {
                    $row[] = '"' . str_replace('"', '\"', $cell) . '"';
                }
            }

            $string .= implode(",", $row) . PHP_EOL;
        }

        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');

        echo $string;
        die();
    }
}
