<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/17
 * Time: 下午3:55
 */

class weekDataController extends BasicController{




    public function excelExportAction(){
        require_once(dirname(__FILE__).'/phpexcel/PHPExcel.php');//加载PHPExcel
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment; filename=demo.xls");
        header('Cache-Control: max-age=0');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'demo');//把demo写入A1
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'demo2');//把demo2写入A2
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//加粗A2

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');//输出到浏览器

        return false;
    }
}