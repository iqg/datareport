<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/18
 * Time: 下午5:07
 */
class M_complaintTag extends Model{

    public function getComplaintTag($startDate,$endDate){
      $sql = "select ct.name tag, count(0) count
from complaint c
inner join complaint_tags cs on cs.complaint_id=c.id
inner join complaint_tag ct on ct.id=cs.complainttag_id
where c.created_at>'".$startDate."'
and c.created_at<'".$endDate."'
group by ct.name ";

        return $this->query($sql);
}

}