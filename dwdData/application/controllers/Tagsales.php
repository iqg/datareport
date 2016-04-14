<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 上午11:58
 */
require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class tagSalesController extends BasicController{
    private $tagSales;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('tagSales');
        return false;
    }

    public function getJsonDataAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->tagSales = $this->load('tagsales');
        $tagSalesArray = $this->tagSales->getTagSales($startDate,$endDate);
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($tagSalesArray) ),  // total number of records
            "recordsFiltered" => intval( count($tagSalesArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $tagSalesArray   // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;
    }

    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->tagSales = $this->load('tagsales');
        $tagSalesArray = $this->tagSales->getTagSales($startDate,$endDate);



        @header('Content-Type: application/vnd.ms-excel');
        @header("Content-Disposition:attachment; filename=demo.xls");
        @header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '录入销售')
            ->SetCellValue('C1', '标签类型')
            ->SetCellValue('D1', '点击次数');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($tagSalesArray);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$tagSalesArray[$i]['城市'])
                ->setCellValue('B'.$count,$tagSalesArray[$i]['录入销售'])
                ->setCellValue('C'.$count,$tagSalesArray[$i]['标签类型'])
                ->setCellValue('D'.$count,$tagSalesArray[$i]['点击次数']);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器

        return false;
    }
}