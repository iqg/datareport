<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午5:07
 */
class M_complaintBranch extends Model{
     public function getOnLine($startDate,$endDate){
         $sql = "select z.name city, count(0) count
from log_campaign_branch_operation l
left join campaign_branch cb on l.campaign_branch_id=cb.id
left join campaignbranch_has_branches cbb on cbb.campaignbranch_id=cb.id
left join branch b on b.id= cbb.branch_id
left join saler s on s.id = b.maintainer_id
left join zone z on z.id= b.zone_id
where l.status=5
and l.enabled=3
and l.type = 1
and (l.remark is Null or l.remark='' or l.remark = '批量上线')
and l.created_at>'".$startDate."'
and l.created_at<'".$endDate."'
group by z.id
";
         return $this->query($sql);
     }

    public function  getOffLine($startDate,$endDate){
        $sql = "select z.name city, count(0) count
from log_campaign_branch_operation l
left join campaign_branch cb on l.campaign_branch_id=cb.id
left join campaignbranch_has_branches cbb on cbb.campaignbranch_id=cb.id
left join branch b on b.id=cbb.branch_id
left join saler s on s.id = b.maintainer_id
left join zone z on z.id= b.zone_id
where l.status=5
and l.enabled<3
and l.type = 1
and l.created_at>'".$startDate."'
and l.created_at<'".$endDate."'
group by z.id "
;

        return $this->query($sql);
    }

    public function  getCity(){
        $sql = "select name as city from zone where enabled = 1";
        return $this->query($sql);
    }

}