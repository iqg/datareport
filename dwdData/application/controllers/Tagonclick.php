<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/23
 * Time: 下午3:58
 */
require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class tagOnclickController extends BasicController{

    private $tagOnclick;

    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('tagonclick');
        return false;
    }

    public function getJsonDataAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->tagOnclick = $this->load('tagonclick');
        $tagOnclickArray = $this->tagOnclick->getTagOnclick($startDate,$endDate);
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($tagOnclickArray) ),  // total number of records
            "recordsFiltered" => intval( count($tagOnclickArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $tagOnclickArray   // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;
        ;
    }
    public function excelExportAction(){

        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $this->tagOnclick = $this->load('tagonclick');
        $tagOnclickArray = $this->tagOnclick->getTagOnclick($startDate,$endDate);

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '操作人ID')
            ->SetCellValue('B1', '操作人姓名')
            ->SetCellValue('C1', '标签类型')
            ->SetCellValue('D1', '点击次数');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($tagOnclickArray);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$tagOnclickArray[$i]['操作人ID'])
                ->setCellValue('B'.$count,$tagOnclickArray[$i]['操作人姓名'])
                ->setCellValue('C'.$count,$tagOnclickArray[$i]['标签类型'])
                ->setCellValue('D'.$count,$tagOnclickArray[$i]['点击次数']);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器

        return false;
    }

}