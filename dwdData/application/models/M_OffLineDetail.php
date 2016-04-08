<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 下午4:18
 */
class M_offLineDetail extends Model{
    public function getOffLineDetail($startDate,$endDate){
        $sql = "select z.name '城市',s.name '销售',b.name '门店', i.name '商品', l.created_at '下线时间', oi.content '下线原因'
from log_campaign_branch_operation l
left join campaign_branch cb on l.campaign_branch_id=cb.id
left join campaignbranch_has_branches cbb on cbb.campaignbranch_id=cb.id
left join branch b on b.id=cbb.branch_id
left join saler s on s.id=b.`maintainer_id`
left join zone z on z.id=s.zone_id
left join campaign c on cb.campaign_id=c.id
left join item i on i.id=c.item_id
left join order_feedback_item oi on oi.id=l.reason_id
where l.status=5
and l.enabled<3
and l.created_at>'".$startDate."'
and l.created_at<'".$endDate."'
";
        return $this->query($sql);
    }
}