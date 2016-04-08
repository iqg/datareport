<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/4/8
 * Time: 下午2:46
 */
class cbSaleRateController extends BasicController{
    private $cbSaleRate;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('cbSaleRate');
        return false;
    }

    public function getJsonDataAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->cbSaleRate = $this->load('cbSaleRate');
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate);

// 连接到mongodb
//        $m = new MongoClient("mongodb://sa:sa@10.0.0.10:27017/iqg_prod");
        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;

        $query = array('data[0]["item_id"]'=>530);
        $query = array("timestamp"=>array('$gt'=>strtotime('2015/12/18'),'$lt'=>strtotime('2015/12/20')));
        $cursor = $collection->find();

        $array = array();
        foreach ($cursor as $document) {
            $array[] = $document['data'];
        }
        $stockArray = array();
        foreach($array as $key1 => $val1){
            foreach($val1 as $key => $val){
                if(array_key_exists($val['cb_id'],$stockArray)){
                    $stockArray[$val['cb_id']]['stock'] += $val['stock'];
                }else {
                    $data = array();
                    $data['item'] = $val['item'];
                    $data['brand'] = $val['brand'];
                    $data['branch'] = $val['branch'];
                    $data['cb_id'] = $val['cb_id'];
                    $data['stock'] = $val['stock'];
                    $stockArray[$val['cb_id']] = $data;
                }
            }
        }

        $sales = array();
        foreach($stockArray as $key => $val){
            foreach($cbSaleRateArray as $key1 => $val1){
                if($val['cb_id'] == $val1['cb_id']){
                    $a = array();
                    $a['销售率'] = $val1['order_count'] / $val['stock'];
                    $a['城市'] = $val1['city'];
                    $a['门店名'] = $val1['shop'];
                    $a['跟进销售'] = $val1['跟进销售'];
                    $a['订单量'] = $val1['order_count'];
                    $a['活动名'] = $val1['item'];
                    $sales[$val['cb_id']] = $a;
                    break;
                }
            }
        }
        print_r($sales);
        exit;
//        $json_data = array(
//            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
//            "recordsTotal"    => intval( count($tagOnclickArray) ),  // total number of records
//            "recordsFiltered" => intval( count($tagOnclickArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
//            "data"            => $tagOnclickArray   // total data array
//        );
//        echo json_encode($json_data);  // send data as json format
        return false;
        ;
    }
}