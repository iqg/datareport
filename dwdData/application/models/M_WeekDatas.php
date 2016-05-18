<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/16
 * Time: 下午4:05
 */

class M_weekDatas extends Model{



    public function getRefundOrder($startDate,$endDate){
        $sql = "select z.name city, count(o.id) orderCount, round(sum(o.price),2) price
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id = b.maintainer_id
left join zone z on s.zone_id=z.id
where o.status=6
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
group by z.id
 ";
        return $this->query($sql);

    }


    public  function getWeekOrder($startDate,$endDate){
        $sql = "select z.name city, count(o.id)  orderCount, round(sum(o.price),2) price
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id = b.maintainer_id
left join zone z on s.zone_id=z.id
where o.trade_status=1
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
group by z.id
 ";
        return $this->query($sql);
    }

}