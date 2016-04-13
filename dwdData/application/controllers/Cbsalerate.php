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
            2=> '订单量',
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
                    $data['brand'] = $val['brand'];
                    $data['branch'] = $val['branch'];
                    $data['stock'] = $val['stock'];
                    $stockArray[$val['cb_id']] = $data;
                }
            }
        }

//        foreach ($cbSaleRateArray as $key => $val) {
//            $exist = array_key_exists($val['活动ID'],$stockArray);
//            $val['sale_rate'] = $exist ? $val['订单量'] / $stockArray[$val['活动ID']]['stock'] : '不匹配';
//            $val['stock'] = $exist ? $stockArray[$val['活动ID']]['stock'] : '不匹配';
//        }

        $jsonArray = array();
        foreach ($cbSaleRateArray as $key => $val) {
            $exist = array_key_exists($val['活动ID'],$stockArray);

            if($exist) {
                $val['sale_rate'] = $val['订单量'] / $stockArray[$val['活动ID']]['stock'] ;
                $val['stock'] = $stockArray[$val['活动ID']]['stock'] ;
                $jsonArray[] = $val;
            }else{
                continue;
            }
        }

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( 100000 ),
            "recordsFiltered" => intval( 100000),
            "data"            => $jsonArray
        );


        echo json_encode($json_data);  // send data as json format
        return false;


    }
}