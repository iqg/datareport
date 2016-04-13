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
        $city = $this->getParam('city');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->getView()->assign("city", $city);
        $this->display('cbSaleRate');
        return false;
    }

    public function getJsonDataAction(){


        $columns = array(
            0 =>'city',
            1 => 'shop',
            2=> 'order_count',
            3=> 'stock',
            4=> 'sale_rate',
            5=> 'saler'
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
        print_r($cbSaleRateArray);
        exit;
        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;
        $query = array("timestamp"=>array('$gt'=>strtotime($startDate),'$lt'=>strtotime($endDate)));
        $cursor = $collection->find($query);
        $stockArray = array();
        foreach ( $cursor as $id => $value ){
            foreach($value['data'] as $key => $val){
                if(array_key_exists($val['cb_id'],$stockArray)){
                    $stockArray[$val['cb_id']]['stock'] += $val['stock'];
                }else{
                    $data = array();
                    $data['brand'] = $val['brand'];
                    $data['branch'] = $val['branch'];
                    $data['stock'] = $val['stock'];
                    $stockArray[$val['cb_id']] = $data;
                }
            }
        }
        $collectionOrder = $db->campaignOrder;
        $cursor1 = $collectionOrder->find($query);
        $orderArray = array();
        foreach($cursor1 as $id=>$val){
            foreach($val['data'] as $key=>$val){
                if(array_key_exists($val['活动ID'],$orderArray)){
                    $orderArray[$val['活动ID']]['订单量'] += $val['订单量'];
                }else{
                    $data = array();
                    $data['order_count'] = $val['订单量'];
                    $data['shop'] = $val['shop'];
                    $data['city'] = $val['city'];
                    $data['saler'] = $val['saler'];
                    $data['item'] = $val['item'];
                    $orderArray[$val['活动ID']] = $data;
                }
            }
        }

        $jsonArray = array();
        if(!empty($search)) {
            foreach ($orderArray as $key => $val) {
                if($val['city'] == $search) {
                    $exist = array_key_exists($key, $stockArray);
                    $val['sale_rate'] = $exist ? $val['order_count'] / $stockArray[$key]['stock'] : '不匹配';
                    $val['stock'] = $exist ? $stockArray[$key]['stock'] : '不匹配';
                    $jsonArray[] = $val;
                }
            }
        }else {
            foreach ($orderArray as $key => $val) {
                $exist = array_key_exists($key, $stockArray);
                $val['sale_rate'] = $exist ? $val['order_count'] / $stockArray[$key]['stock'] : '不匹配';
                $val['stock'] = $exist ? $stockArray[$key]['stock'] : '不匹配';
                $jsonArray[] = $val;

            }
        }
        if($orderDir == "asc") {
            usort($jsonArray, function ($a, $b) {
                return $a['$orderColumn'] > $b['$orderColumn'] ? 1 : -1;
            });
        }else{
            usort($jsonArray, function ($a, $b) {
                return $b['$orderColumn'] > $a['$orderColumn'] ? 1 : -1;
            });
        }
        $total = count($jsonArray);
        $jsonArray = array_slice($jsonArray,$start,$length);
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( $total ),
            "recordsFiltered" => intval( $total),
            "data"            => $jsonArray
        );


        echo json_encode($json_data);  // send data as json format
        return false;


    }
}