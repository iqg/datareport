<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 下午4:15
 */
class offLineDetailController extends BasicController{
    private $offLineDetail;
    public function jumpAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->getView()->assign("startDate", $startDate);
        $this->getView()->assign("endDate", $endDate);
        $this->display('offLinedetail');
        return false;
    }

    public function excelExportAction(){
        $startDate = $this->getParam('startDate');
        $endDate = $this->getParam('endDate');

        $this->offLineDetail = $this->load('offLinedetail');
        $offLineDetail = $this->offLineDetail->getOffLineDetail($startDate,$endDate);


    }
}