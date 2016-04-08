<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午4:13
 */

require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class complaintTagController extends BasicController{

    private $complaintTag;


    /**
     * 投诉类型统计
     * */
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('complaintTag');
        return false;
    }

    /**
     *  基于城市的下线活动数 表格数据json格式
     */
    public function getJsonDataAction(){
        $this->complaintTag = $this->load('complainttag');
        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期

        $complaintTagArray = $this->complaintTag->getComplaintTag($startDate,$endDate);//获取投诉类型统计

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($complaintTagArray) ),  // total number of records
            "recordsFiltered" => intval( count($complaintTagArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $complaintTagArray   // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;

    }

    public function excelExportAction(){
        $this->complaintTag = $this->load('complainttag');
        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期

        $complaintTagArray = $this->complaintTag->getComplaintTag($startDate,$endDate);//获取投诉类型统计



        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '投诉类型')
            ->SetCellValue('B1', '数量');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($complaintTagArray);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$complaintTagArray[$i]['tag'])
                ->setCellValue('B'.$count,$complaintTagArray[$i]['count']);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器
        return false;
    }
}