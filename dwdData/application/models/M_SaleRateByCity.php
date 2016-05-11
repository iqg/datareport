<?php
/**
 * Created by PhpStorm.
 * User: szl
 * Date: 16-5-11
 * Time: 上午10:58
 */
class M_SaleRateByCity extends Model{
    public function getSaleRate($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length){
        $sql = "select z.name 'city', count(o.id) 'order_count'
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaignbranch_has_branches cbb on cbb.campaignbranch_id=cb.id
left join branch b on cbb.branch_id = b.id
left join zone z on b.zone_id=z.id
where o.trade_status=1
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
";

        if(!empty($search)){
            $sql.="and z.name like '$search%' ";
        }
        if(empty($length)) {
            $sql.=" group by z.id";
            $sql.=" order by $orderColumn $orderDir";
            return $this->query($sql);
        }
        $sql.="group by z.id";
        $sql.=" order by $orderColumn $orderDir";
        $sql.=" limit $start,$length ";
        return $this->query($sql);
    }

    public function getSaleForExcel($startDate,$endDate){
        $sql = "select z.name 'city', count(o.id) 'order_count'
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaignbranch_has_branches cbb on cbb.campaignbranch_id=cb.id
left join branch b on cbb.branch_id = b.id
left join zone z on b.zone_id=z.id
where o.trade_status=1
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
group by z.id
";
        return $this->query($sql);
    }

}