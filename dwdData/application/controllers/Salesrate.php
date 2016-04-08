<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 上午11:34
 */
class salesRateController extends BasicController{
    private $salesRate;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('salesrate');
        return false;
    }

    public function getJsonDataAction(){
        $this->salesRate = $this->load('salesrate');
        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期
        $salesRateArray = $this->salesRate->getSalesRate($startDate,$endDate);


        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($salesRateArray) ),  // total number of records
            "recordsFiltered" => intval( count($salesRateArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $salesRateArray   // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;
    }
}