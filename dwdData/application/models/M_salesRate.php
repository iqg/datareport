<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 上午11:37
 */
class M_salesRate extends Model{
    public function getSalesRate($startDate,$endDate){
        $sql = "select z.name '城市', count(0) '每日可销', sum(o.c*cb.stock) '每周应销', sum(if (cc.c is NULL, 0, cc.c)) '每周实销', sum(if (cc.c is NULL, 0, cc.c))/sum(o.c*cb.stock) '周销售率'
from (
select branch_id,count(0) c from
(

select *
from stats_branch_day
where type=1
and total_num>10
and created_at>'".$startDate."'
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
group by z.id ";
        return $this->query($sql);
    }
}