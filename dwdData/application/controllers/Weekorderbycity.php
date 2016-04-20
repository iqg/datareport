<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午2:24
 */

require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel
class weekOrderByCityController extends BasicController{

    private $weekDatas;



    /**
     * 订单以及退单
     * */
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('weekOrderByCity');
        return false;
    }

 /**
  *  基于城市的订单量 金额 退款订单量 退款金额 表格数据json格式
  */
   public function getJsonDataAction(){

       $startDate = $this->getParam('startDate');   //查询开始日期
       $endDate = $this->getParam('endDate');      //查询结束日期
       $this->weekDatas = $this->load('weekDatas');

       $weekOrderArray = $this->weekDatas->getWeekOrder($startDate,$endDate); //周订单
       $weekRefundOrderArray = $this->weekDatas->getRefundOrder($startDate,$endDate); //周退单

       $data = array();
       $weekOrderArrayCount = count($weekOrderArray);
       $weekRefundOrderArrayCount = count($weekRefundOrderArray);
       for($i=0;$i<$weekOrderArrayCount;$i++){
           for($j=0;$j<$weekRefundOrderArrayCount;$j++){
               if($weekOrderArray[$i]['city'] == $weekRefundOrderArray[$j]['city']){
                   $a = array();
                   $a[] = $weekOrderArray[$i]['city'];
                   $a[] = $weekOrderArray[$i]['orderCount'];
                   $a[] = $weekOrderArray[$i]['price'];
                   $a[] = $weekRefundOrderArray[$j]['orderCount'];
                   $a[] = $weekRefundOrderArray[$j]['price'];
                   $data[] = $a;
                   break;
               }
           }
       }
       $json_data = array(
           "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
           "recordsTotal"    => intval( count($weekOrderArray) ),  // total number of records
           "recordsFiltered" => intval( count($weekOrderArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
           "data"            => $data   // total data array
       );
       echo json_encode($json_data);  // send data as json format
       return false;

    }


    public function excelExportAction(){

        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期
        $this->weekDatas = $this->load('weekDatas');

        $weekOrderArray = $this->weekDatas->getWeekOrder($startDate,$endDate); //周订单
        $weekRefundOrderArray = $this->weekDatas->getRefundOrder($startDate,$endDate); //周退单


        for($i=0;$i<count($weekOrderArray);$i++){
            for($j=0;$j<count($weekRefundOrderArray);$j++){
                if($weekOrderArray[$i]['city'] == $weekRefundOrderArray[$j]['city']){
                    $a = array();
                    $a[] = $weekOrderArray[$i]['city'];
                    $a[] = $weekOrderArray[$i]['orderCount'];
                    $a[] = $weekOrderArray[$i]['price'];
                    $a[] = $weekRefundOrderArray[$j]['orderCount'];
                    $a[] = $weekRefundOrderArray[$j]['price'];
                    $data[] = $a;
                    break;
                }
            }
        }
        @header('Content-Type: application/vnd.ms-excel');
        @header("Content-Disposition:attachment; filename=demo.xls");
        @header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
                    ->SetCellValue('A1', '城市')
                    ->SetCellValue('B1', '订单量')
                    ->SetCellValue('C1', '金额')
                    ->SetCellValue('D1', '退款订单量')
                    ->SetCellValue('E1', '退款金额');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        $dataCount = count($data);
        for($i=0;$i<$dataCount;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$data[$i][0])
                ->setCellValue('B'.$count,$data[$i][1])
                ->setCellValue('C'.$count,$data[$i][2])
                ->setCellValue('D'.$count,$data[$i][3])
                ->setCellValue("E".$count,$data[$i][4]);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器

        return false;
    }
}