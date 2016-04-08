<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午4:13
 */
require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class weekComplaintBranchController extends BasicController{

    private $weekComplanintBranch;



    /**
     * 上线下线活动数量
     * */
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('weekcomplaintbranch1');
        return false;
    }



    /**
     *  基于城市的下线活动数 表格数据json格式
     */
    public function getJsonDataAction(){
        $this->weekComplanintBranch = $this->load('complaintBranch');
        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期
        $cityArray = $this->weekComplanintBranch->getCity();
        $onLineArray = $this->weekComplanintBranch->getOnLine($startDate,$endDate); //上线
        $offLineArray = $this->weekComplanintBranch->getOffLine($startDate,$endDate); //下线

        $data = array();

        for($i=0;$i<count($cityArray);$i++){
            $temp = array();
            $temp['city'] = $cityArray[$i]['city'];
            for($j=0;$j<count($onLineArray);$j++){
                if(in_array($cityArray[$i]['city'],$onLineArray[$j])){
                    $temp['onLineCount'] = $onLineArray[$j]['count'] ;
                    break ;
                }else{
                    $temp['onLineCount'] = '0' ;
                }
            }

            for($k=0;$k<count($offLineArray);$k++){
                if(in_array($cityArray[$i]['city'],$offLineArray[$k])){
                    $temp['offLineCount'] = $offLineArray[$k]['count'] ;
                    break ;
                }else{
                    $temp['offLineCount'] = '0' ;
                }
            }
            $data[] = $temp;
        }
        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( count($onLineArray) ),  // total number of records
            "recordsFiltered" => intval( count($onLineArray) ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        echo json_encode($json_data);  // send data as json format
        return false;
    }

    public function excelExportAction(){
        $this->weekComplanintBranch = $this->load('complaintBranch');
        $startDate = $this->getParam('startDate');   //查询开始日期
        $endDate = $this->getParam('endDate');      //查询结束日期
        $cityArray = $this->weekComplanintBranch->getCity();
        $onLineArray = $this->weekComplanintBranch->getOnLine($startDate,$endDate); //上线
        $offLineArray = $this->weekComplanintBranch->getOffLine($startDate,$endDate); //下线

        $data = array();

        for($i=0;$i<count($cityArray);$i++){
            $temp = array();
            $temp['city'] = $cityArray[$i]['city'];
            for($j=0;$j<count($onLineArray);$j++){
                if(in_array($cityArray[$i]['city'],$onLineArray[$j])){
                    $temp['onLineCount'] = $onLineArray[$j]['count'] ;
                    break ;
                }else{
                    $temp['onLineCount'] = '0' ;
                }
            }

            for($k=0;$k<count($offLineArray);$k++){
                if(in_array($cityArray[$i]['city'],$offLineArray[$k])){
                    $temp['offLineCount'] = $offLineArray[$k]['count'] ;
                    break ;
                }else{
                    $temp['offLineCount'] = '0' ;
                }
            }
            $data[] = $temp;
        }

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
            ->SetCellValue('B1', '上线数')
            ->SetCellValue('C1', '下线数');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $count = 2;
        for($i=0;$i<count($data);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$data[$i]['city'])
                ->setCellValue('B'.$count,$data[$i]['onLineCount'])
                ->setCellValue('C'.$count,$data[$i]['offLineCount']);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器
        return false;
    }
}