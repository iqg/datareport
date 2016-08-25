<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/7
 * Time: 下午6:34
 */

class UserController extends BasicController {

    private $weekDatas;

    public function indexAction(){

    }

    public function loginAction() {
        $this->display('main');
        return false;
    }

    public function registerAction()
    {
        echo 'register';
        return false;
    }

    public function jsonDataAction(){
        $requestData= $_REQUEST;
        $columns = array(
            0 =>'city',
            1 => 'orderCount',
            2=> 'price'
        );
        $order = $columns[$requestData['order'][0]['column']];
        $dir = $requestData['order'][0]['dir'];
        $start = $requestData['start'];
        $length = $requestData['length'];

        $this->weekDatas = $this->load('weekDatas');
        $arr1 = $this->weekDatas->getWeekData();
        $arr  = $this->weekDatas->getWeekDatas($requestData['search']['value'],$order,$start,$length,$dir);
        $recordsFiltered = count($arr);
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($arr1) ),  // total number of records
            "recordsFiltered" => intval( $recordsFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $arr   // total data array
        );

        echo json_encode($json_data);  // send data as json format
        return false;

    }
}