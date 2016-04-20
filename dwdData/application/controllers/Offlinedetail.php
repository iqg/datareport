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

        $this->offLineDetail = $this->load('offLinedetail');
        $offLineDetail = $this->offLineDetail->getOffLineDetail($startDate,$endDate);
        print_r($offLineDetail);
        exit;

    }
}