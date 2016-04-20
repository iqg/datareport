<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 下午4:15
 */
require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel

class offLineDetailController extends BasicController{
    private $offLineDetail;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('offLineDetail');
        return false;
    }



    public function getJsonDataAction(){
        $requestData= $_REQUEST;
        $columns = array(
            0 =>'城市',
            1 => '销售',
            2=> '门店',
            3=> '商品',
            4=> '下线时间'
        );
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');
        $start = $this->getParam('start');
        $length = $this->getParam('length');
        $search = $requestData['search']['value'];
        $orderColumn = $columns[$requestData['order'][0]['column']];
        $orderDir =$requestData['order'][0]['dir'];

        $this->offLineDetail = $this->load('offLineDetail');
        $offLineDetailArray = $this->offLineDetail->getOffLineDetail($startDate,$endDate,$search,$orderColumn,$orderDir);
        $totalData = count($offLineDetailArray);
        $totalFiltered = $totalData;

        $offLineDetailArray = $this->offLineDetail->getOffLineDetail($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length);

        $json_data = array(
            "draw"            => intval( $this->getParam('draw')),
            "recordsTotal"    => intval( $totalData  ),
            "recordsFiltered" => intval($totalFiltered ),
            "data"            => $offLineDetailArray
        );
        echo json_encode($json_data);  // send data as json format
        return false;

    }
    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->offLineDetail = $this->load('offLineDetail');
        $offLineDetail = $this->offLineDetail->forExcelExport($startDate,$endDate);

        @header('Content-Type: application/vnd.ms-excel');
        @header("Content-Disposition:attachment; filename=demo.xls");
        @header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();


        $objPHPExcel->setActiveSheetIndex(0)
            ->SetCellValue('A1', '城市')
            ->SetCellValue('B1', '销售')
            ->SetCellValue('C1', '门店')
            ->SetCellValue('D1', '商品')
            ->SetCellValue('E1', '下线时间')
            ->SetCellValue('F1', '下线原因');
        $count = 2;
        $offLineDetailCount = count($offLineDetail);
        for($i=0;$i<$offLineDetailCount;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$count,$offLineDetail[$i]['城市'])
                ->setCellValue('B'.$count,$offLineDetail[$i]['销售'])
                ->setCellValue('C'.$count,$offLineDetail[$i]['门店'])
                ->setCellValue('D'.$count,$offLineDetail[$i]['商品'])
                ->setCellValue('E'.$count,$offLineDetail[$i]['下线时间'])
                ->setCellValue('F'.$count,$offLineDetail[$i]['下线原因']);

            $count++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器

        return false;
    }


}
