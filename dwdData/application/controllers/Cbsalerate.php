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
            1 => 'item',
            2=> 'cb_id',
            3=> 'shop',
            4=> 'order_count',
            5=> 'stock',
            5=> 'sale_rate',
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
//        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,"",$orderColumn,$orderDir);
//        $totalCount = count($cbSaleRateArray);
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,$search,$orderColumn,$orderDir);
        $totalFiltered = count($cbSaleRateArray);
        $cbSaleRateArray = $this->cbSaleRate->getSale($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length);
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
                    $data['item'] = $val['item'];
                    $data['stock'] = $val['stock'];
                    $stockArray[$val['cb_id']] = $data;
                }
            }
        }


        $jsonArray = array();
        foreach ($cbSaleRateArray as $key => $val) {
            $exist = array_key_exists($val['cb_id'],$stockArray);
            if($exist) {
                $val['sale_rate'] = $val['order_count'] / $stockArray[$val['cb_id']]['stock'] ;
                $val['stock'] = $stockArray[$val['cb_id']]['stock'] ;
                $val['item'] = $stockArray[$val['cb_id']]['item'] ;
                $jsonArray[] = $val;
            }else{
                continue;
            }
        }

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( $totalFiltered ),
            "recordsFiltered" => intval( $totalFiltered),
            "data"            => $jsonArray
        );


        echo json_encode($json_data);  // send data as json format
        return false;


    }
}