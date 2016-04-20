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
        if($type == "sqy") {
            $this->display('sqyVerificationRate');
        }else{
            $this->display('wxpVerificationRate');
        }

        return false;
    }

    public function getSqyJsonDataAction(){
        $requestData= $_REQUEST;


        $columns = array(
            0 =>'城市',
            1 => '总订单量',
            2=> '已验证订单量',
            3=> '验证率'
        );

        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $start = $this->getParam('start');
        $length = $this->getParam('length');
        $searchValue = $this->getParam('search[value]');
        $orderColumn = $columns[$requestData['order'][0]['column']];
        $orderDir =$requestData['order'][0]['dir'];

        $this->verificationRate = $this->load('verificationRate');
        $sqyVerificationRateArray = $this->verificationRate->getSqyVerificationRate($startDate,$endDate,$orderColumn,$orderDir);
        $totalData = count($sqyVerificationRateArray);
        $totalFiltered = $totalData;

        $sqyVerificationRateArray = $this->verificationRate->getSqyVerificationRate($startDate,$endDate,$orderColumn,$orderDir,$start,$length);

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( $totalData  ),
            "recordsFiltered" => intval($totalFiltered ),
            "data"            => $sqyVerificationRateArray
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
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( count($wxpVerificationRateArray ) ),
            "recordsFiltered" => intval( count($wxpVerificationRateArray ) ),
            "data"            => $wxpVerificationRateArray
        );
        echo json_encode($json_data);
        return false;

    }
    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->verificationRate = $this->load('verificationRate');
        $sqyVerificationRateArray = $this->verificationRate->getSqyVerificationRateForExcel($startDate,$endDate);


        @header('Content-Type: application/vnd.ms-excel');
        @header("Content-Disposition:attachment; filename=demo.xls");
        @header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '总订单量')
            ->SetCellValue('C1', '已验证订单量')
            ->SetCellValue('D1', '验证率')
        ;
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