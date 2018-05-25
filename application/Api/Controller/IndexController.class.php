<?php
/**
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/12
 * Time: 11:09
 */


namespace Api\Controller;

class IndexController extends BaseController
{

        public function index(){
            ob_clean();
            exit(json_encode(['code'=>0]));
        }

}