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

        $columns = array(
            0 =>'city',
            1 => '总订单量',
            2=> '已验证订单量',
            3=> '验证率'
        );
        $requestData= $_REQUEST;
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $start = $this->getParam('start');
        $length = $this->getParam('length');
        $search = $requestData['search']['value'];
        $orderColumn = $columns[$requestData['order'][0]['column']];
        $orderDir =$requestData['order'][0]['dir'];

        $this->cbSaleRate = $this->load('cbSaleRate');
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,"",$orderColumn,$orderDir);
        $totalData = count($cbSaleRateArray);
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,$search,$orderColumn,$orderDir);
        $totalFiltered = count($cbSaleRateArray);
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length);
        $cbSaleArray = array();
        foreach($cbSaleRateArray as $key => $val){
            $a = array();
            $a['order_count'] = $val['订单量'];
            $a['saler'] = $val['saler'];
            $a['shop'] = $val['shop'];
            $a['city'] = $val['city'];
            $a['cb_id'] = $val['活动ID'];
            $cbSaleArray[$val['活动ID']] = $a;
        }

// 连接到mongodb
//        $m = new MongoClient("mongodb://sa:sa@10.0.0.10:27017/iqg_prod");
          $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
//        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@127.0.0.1:2717/iqg_prod");

        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;

        $query = array("timestamp"=>array('$gt'=>strtotime($startDate),'$lt'=>strtotime($endDate)));

        $cursor = $collection->find($query);

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

        foreach($cbSaleArray as $key => $val){
            $exist = array_key_exists($key,$stockArray);
            $cbSaleArray[$key]['销售率'] = $exist ? $val['order_count'] / $stockArray[$key]['stock'] : '0';
            $cbSaleArray[$key]['stock'] = $exist ? $stockArray[$key]['stock'] : '0';
        }
        $jsonArray = array();
        foreach($cbSaleArray as $key => $val){
            $a = array();
            $a['order_count'] = $val['order_count'];
            $a['shop'] = $val['shop'];
            $a['cb_id'] = $val['cb_id'];
            $a['saler'] = $val['saler'];
            $a['city'] = $val['city'];
            $a['stock'] = $val['stock'];
            $a['sale_rate'] = $val['销售率'];
            $jsonArray[] = $a;
        }

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $jsonArray   // total data array
        );

        echo json_encode($json_data);  // send data as json format
        return false;

    }
}