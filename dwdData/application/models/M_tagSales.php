<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/24
 * Time: 下午1:26
 */
class M_tagSales extends Model{
    public function getTagSales(){
        $sql = "select z.name '城市',s.name '录入销售',if(lc.remark=1,'需审核',if(lc.remark=2,'审核通过',if(lc.remark=3,'审核不通过',if(lc.remark=4,'培训受阻',if(lc.remark=5,'再培训',if(lc.remark=6,'培训通过',lc.remark)))))) '标签类型',count(0) '点击次数'
from `log_campaign_branch_operation` lc
left join user u on u.id=lc.user_id
left join `campaign_branch` cb on cb.id=lc.`campaign_branch_id`
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where lc.type=3
and lc.created_at>'2016-2-26'
and lc.created_at<'2016-3-4'
group by concat(s.id,lc.remark)
";
        return $this->query($sql);
    }
}