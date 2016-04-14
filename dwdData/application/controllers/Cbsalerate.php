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
            6=> 'sale_rate',
            7=> 'saler'
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

    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->cbSaleRate = $this->load('cbSaleRate');
        $cbSaleRateArray = $this->cbSaleRate->getSaleForExcel($startDate,$endDate);

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




        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '活动名称')
            ->SetCellValue('C1', '活动ID')
            ->SetCellValue('D1', '门店名')
            ->SetCellValue('E1', '订单量')
            ->SetCellValue('F1', '可卖数')
            ->SetCellValue('G1', '销售率')
            ->SetCellValue('H1', '跟进销售')
        ;
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($jsonArray);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$jsonArray[$i]['city'])
                ->setCellValue('B'.$count,$jsonArray[$i]['item'])
                ->setCellValue('C'.$count,$jsonArray[$i]['cb_id'])
                ->setCellValue('D'.$count,$jsonArray[$i]['shop'])
                ->setCellValue('E'.$count,$jsonArray[$i]['order_count'])
                ->setCellValue('F'.$count,$jsonArray[$i]['stock'])
                ->setCellValue('G'.$count,$jsonArray[$i]['sale_rate'])
                ->setCellValue('H'.$count,$jsonArray[$i]['saler'])
            ;

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器
        return false;
    }
}