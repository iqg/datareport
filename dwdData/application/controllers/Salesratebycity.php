<?php
/**
 * Created by PhpStorm.
 * User: szl
 * Date: 16-5-11
 * Time: 上午10:26
 */
class salesRateByCityController extends BasicController{
    private $salesRateByCity;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('salesRateByCity');
        return false;
    }

    public function getJsonDataAction(){

        $columns = array(
            0 =>'city',
            1=> 'order_count'
        );
        $requestData= $_REQUEST;
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $start = $this->getParam('start');
        $length = $this->getParam('length');
        $search = $requestData['search']['value'];
        $orderColumn = $columns[$requestData['order'][0]['column']];
        $orderDir =$requestData['order'][0]['dir'];
        $this->salesRateByCity = $this->load('SaleRateByCity');

        $SaleRateByCityArray = $this->salesRateByCity->getSaleRate($startDate,$endDate,$search,$orderColumn,$orderDir);

        $totalFiltered = count($SaleRateByCityArray);
        $SaleRateByCityArray = $this->salesRateByCity->getSaleRate($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length);
        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;
        $query = array("timestamp"=>array('$gt'=>strtotime($startDate),'$lt'=>strtotime($endDate)));
        $cursor = $collection->find($query);
        $stockArray = array();
        foreach ( $cursor as $id => $value ){
            foreach($value['data'] as $key => $val){
                if(array_key_exists($val['city'],$stockArray)){
                    $stockArray[$val['city']]['stock'] += $val['stock'];
                }else{
                    $data = array();
                    $data['stock'] = $val['stock'];
                    $data['city'] = $val['city'];
                    $stockArray[$val['city']] = $data;
                }
            }
        }


        $jsonArray = array();
        foreach ($SaleRateByCityArray as $key => $val) {
            $exist = array_key_exists($val['city'],$stockArray);
            if($exist && $val['order_count'] ) {
                $val['sale_rate'] = round($val['order_count'] / $stockArray[$val['city']]['stock'],4) ;
                $val['stock'] = $stockArray[$val['city']]['stock'] ;
                $val['city'] = $stockArray[$val['city']]['city'] ;
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