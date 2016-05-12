<?php
/**
 * Created by PhpStorm.
 * User: szl
 * Date: 16-5-11
 * Time: 上午10:26
 */
@require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel
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

    public function getJsonDataAction()
    {

        $columns = array(
            0 => 'city',
            1 => 'order_count'
        );
        $requestData = $_REQUEST;
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $start = $this->getParam('start');
        $length = $this->getParam('length');
        $search = $requestData['search']['value'];
        $orderColumn = $columns[$requestData['order'][0]['column']];
        $orderDir = $requestData['order'][0]['dir'];
        $this->salesRateByCity = $this->load('SaleRateByCity');

        $SaleRateByCityArray = $this->salesRateByCity->getSaleRate($startDate, $endDate, $search, $orderColumn, $orderDir);

        $totalFiltered = count($SaleRateByCityArray);
        $SaleRateByCityArray = $this->salesRateByCity->getSaleRate($startDate, $endDate, $search, $orderColumn, $orderDir, $start, $length);
        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;
        $query = array("timestamp" => array('$gt' => strtotime($startDate), '$lt' => strtotime($endDate)));
        $cursor = $collection->find($query);
        $stockArray = array();
        foreach ($cursor as $id => $value) {
            foreach ($value['data'] as $key => $val) {
                $x = "%**#".$val['item'];
                $boolean = strpos($x, "到家美食会");
                $boolean2 = strpos($x, "需支付运费");
                if ($val['delivery_type'] == 1 && $boolean == 0 && $boolean2 == 0 && $val['type'] < 3) {
                    if (array_key_exists($val['city'], $stockArray)) {
                        $stockArray[$val['city']]['stock'] += $val['stock'];
                    } else {
                        $data = array();
                        $data['stock'] = $val['stock'];
                        $data['city'] = $val['city'];
                        $stockArray[$val['city']] = $data;
                    }

                } else {
                    continue;
                }
            }
        }

            $jsonArray = array();
            foreach ($SaleRateByCityArray as $key => $val) {
                $exist = array_key_exists($val['city'], $stockArray);
                if ($exist && $val['order_count']) {
                    $val['sale_rate'] = round($val['order_count'] / $stockArray[$val['city']]['stock'], 4);
                    $val['stock'] = $stockArray[$val['city']]['stock'];
                    $val['city'] = $stockArray[$val['city']]['city'];
                    $jsonArray[] = $val;
                } else {
                    continue;
                }
            }

            $json_data = array(
                "draw" => intval($this->getParam('draw')),
                "recordsTotal" => intval($totalFiltered),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $jsonArray
            );
            echo json_encode($json_data);  // send data as json format
            return false;

    }
    public function excelExportAction()
    {
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->salesRateByCity = $this->load('SaleRateByCity');

        $SaleRateByCityArray = $this->salesRateByCity->getSaleForExcel($startDate,$endDate);

        $m = new MongoClient("mongodb://iqg_prod:oq9ghGYj9ViR@10.132.163.91:27017/iqg_prod");
        $db = $m->iqg_prod;
        $collection = $db->campaignbydate;
        $query = array("timestamp"=>array('$gt'=>strtotime($startDate),'$lt'=>strtotime($endDate)));
        $cursor = $collection->find($query);
        $stockArray = array();
        foreach ($cursor as $id => $value) {
            foreach ($value['data'] as $key => $val) {
                $x = "%**#".$val['item'];
                $boolean = strpos($x, "到家美食会");
                $boolean2 = strpos($x, "需支付运费");
                if ($val['delivery_type'] == 1 && $boolean == 0 && $boolean2 == 0 && $val['type'] < 3) {
                    if (array_key_exists($val['city'], $stockArray)) {
                        $stockArray[$val['city']]['stock'] += $val['stock'];
                    } else {
                        $data = array();
                        $data['stock'] = $val['stock'];
                        $data['city'] = $val['city'];
                        $stockArray[$val['city']] = $data;
                    }

                } else {
                    continue;
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
        @header('Content-Type: application/vnd.ms-excel');
        @header("Content-Disposition:attachment; filename=$startDate-$endDate.基于城市的销售率.xls");
        @header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '订单数')
            ->SetCellValue('C1', '总库存')
            ->SetCellValue('D1', '销售率')
        ;

        $count = 2;
        $length = count($jsonArray);
        for($i=0;$i<$length;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$jsonArray[$i]['city'])
                ->setCellValue('B'.$count,$jsonArray[$i]['order_count'])
                ->setCellValue('C'.$count,$jsonArray[$i]['stock'])
                ->setCellValue('D'.$count,$jsonArray[$i]['sale_rate'])
            ;

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器
        return false;
    }
    }