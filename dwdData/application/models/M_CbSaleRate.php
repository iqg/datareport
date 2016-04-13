<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午5:07
 */
class M_cbSaleRate extends Model{
    public function getSale($startDate,$endDate,$search,$orderColumn,$orderDir,$start,$length){
        $sql = "select z.name 'city',s.name 'saler',b.name '门店',cb.id '活动ID',count(0) '订单量'
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.trade_status=1
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
";

     if(!empty($search)){
         $sql.="and z.name like '$search%' ";
     }
        $sql.=" order by $orderColumn $orderDir";
        if(empty($length)) {
            $sql.="group by cb.id";
            return $this->query($sql);
        }
        $sql.=" group by cb.id limit $start,$length ";
        return $this->query($sql);
    }


}