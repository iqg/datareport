<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/23
 * Time: 下午5:28
 */

require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class verificationRateController extends BasicController{
     private $verificationRate;

    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $type = $this->getParam('type');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        if($type="sqy") {
            $this->display('sqyVerificationRate');
        }else{
            $this->display('wxpVerificationRate');
        }

        return false;
    }

    public function getSqyJsonDataAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->verificationRate = $this->load('verificationRate');
        $sqyVerificationRateArray = $this->verificationRate->getSqyVerificationRate($startDate,$endDate);
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count(sqyVerificationRateArray ) ),  // total number of records
            "recordsFiltered" => intval( count(sqyVerificationRateArray ) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $sqyVerificationRateArray    // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;

    }

    public function getWxpJsonDataAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->verificationRate = $this->load('verificationRate');
        $wxpVerificationRateArray = $this->verificationRate->getWxpVerificationRate($startDate,$endDate);
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($wxpVerificationRateArray ) ),  // total number of records
            "recordsFiltered" => intval( count($wxpVerificationRateArray ) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $wxpVerificationRateArray    // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;

    }
    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->verificationRate = $this->load('verificationRate');
        $sqyVerificationRateArray = $this->verificationRate->getSqyVerificationRate($startDate,$endDate);


        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '总订单量')
            ->SetCellValue('C1', '已验证订单量')
            ->SetCellValue('D1', '验证率')
        ;
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($sqyVerificationRateArray);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$sqyVerificationRateArray[$i]['城市'])
                ->setCellValue('B'.$count,$sqyVerificationRateArray[$i]['总订单量'])
                ->setCellValue('C'.$count,$sqyVerificationRateArray[$i]['已验证订单量'])
                ->setCellValue('D'.$count,$sqyVerificationRateArray[$i]['验证率'])
            ;

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器
        return false;
    }
}