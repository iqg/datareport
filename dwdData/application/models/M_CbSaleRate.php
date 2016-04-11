<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午5:07
 */
class M_cbSaleRate extends Model{
    public function getSale($startDate,$endDate){
        $sql = "select o.branch_id '门店ID', cb.id '活动ID', i.name item, b.name shop, cb.type, cb.start_time, cb.stock,o.c n,o.c*cb.stock '周可售', z.name city, s.name saler,if (cc.c is NULL, 0, cc.c) '订单量', (if (cc.c is NULL, 0, cc.c))/(o.c*cb.stock) '销售率'
from (
select branch_id,count(0) c from
(
select *
from stats_branch_day
where type=1
and total_num>10
and created_at>='".$startDate."'
and created_at<'".$endDate."'
group by concat(branch_id, created_at)
order by created_at desc
) t
group by branch_id
) o
left join campaignbranch_has_branches cbb on cbb.`branch_id`=o.branch_id
left join branch b on b.id = o.branch_id
left join (select distinct(id), campaign_id, type, `start_time`, `stock` from campaign_branch where type<3 and enabled=1 and start_time<now() and end_time>now()) cb on cb.id = cbb.campaignbranch_id # 排除很早之前以下线的活动
left join campaign cp on cb.campaign_id = cp.id
left join item i on i.id = cp.item_id
left join saler s on b.maintainer_id = s.id
left join zone z on s.zone_id=z.id
left join (select campaign_branch_id, count(0) c from product_order where `trade_status`=1 and type<3 and created_at>'".$startDate."' and created_at<'".$endDate."' group by campaign_branch_id) cc on cc.campaign_branch_id = cb.id # 获取订单数量
where i.name is not NULL
and b.name not like '一席地%'
order by cc.c/(o.c*cb.stock) asc";
        return $this->query($sql);
    }


}