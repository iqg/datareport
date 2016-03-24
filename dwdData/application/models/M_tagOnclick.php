<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/23
 * Time: 下午4:01
 */
class M_tagOnclick extends Model{

    public function getTagOnclick($startDate,$endDate){
        $sql = "select u.id '操作人ID',u.username '操作人用户名',if(lc.remark=1,'需审核',if(lc.remark=2,'审核通过',if(lc.remark=3,'审核不通过',if(lc.remark=4,'培训受阻',if(lc.remark=5,'再培训',if(lc.remark=6,'培训通过',lc.remark)))))) '标签类型',count(0) '点击次数'
from `log_campaign_branch_operation` lc
left join user u on u.id=lc.user_id
where lc.type=3
and lc.created_at>'".$startDate."'
and lc.created_at<'".$endDate."'
and lc.remark not in (1,5)
group by concat(u.id,lc.remark)
";

        return $this->query($sql);
    }
}