<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 16/3/23
 * Time: 下午5:33
 */
class M_verificationRate extends Model{

    public function getSqyVerificationRate($startDate,$endDate,$orderColumn,$orderDir,$start,$end){
        $sql = "select ob.zn '城市', ob.c '总订单量', rb.c '已验证订单量', rb.c/ob.c '验证率'
from
(
select z.id z,z.name zn, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.trade_status=1
and o.type=6
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) ob
left join
(
select z.id zd, z.name, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.redeem_time is not null
and o.type=6
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) rb on rb.zd=ob.z
where ob.c>1
order by $orderColumn $orderDir
";

   if(empty($end)) {
       return $this->query($sql);
   }
        $sql.=" limit $start,$end ";
        return $this->query($sql);
    }

    public function getWxpVerificationRate($startDate,$endDate){
        $sql = "select ob.zn '城市', ob.c '总订单量', rb.c '已验证订单量', rb.c/ob.c '验证率'
from
(
select z.id z,z.name zn, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.trade_status=1
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) ob
left join
(
select z.id zd, z.name, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.redeem_time is not null
and o.type<3
and o.created_at>'".$startDate."'
and o.created_at<'".$endDate."'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) rb on rb.zd=ob.z
where ob.c>1
";

        return $this->query($sql);
    }


    public function getSqyVerificationRateForExcel($startDate,$endDate)
    {
        $sql = "select ob.zn '城市', ob.c '总订单量', rb.c '已验证订单量', rb.c/ob.c '验证率'
from
(
select z.id z,z.name zn, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.trade_status=1
and o.type=6
and o.created_at>'" . $startDate . "'
and o.created_at<'" . $endDate . "'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) ob
left join
(
select z.id zd, z.name, count(0) c
from product_order o
left join campaign_branch cb on cb.id = o.campaign_branch_id
left join campaign cp on cb.campaign_id = cp.id
left join campaignbranch_has_branches cbb on cbb.`campaignbranch_id`=cb.id
left join branch b on cbb.branch_id = b.id
left join saler s on s.id=b.`maintainer_id`
left join zone z on s.zone_id=z.id
where o.redeem_time is not null
and o.type=6
and o.created_at>'" . $startDate . "'
and o.created_at<'" . $endDate . "'
and b.`redeem_code_source`=1
and b.name not like '西树%'
and b.name not like '摩提%'
and b.name not like '真功夫%'
and b.name not like '牛奶棚%'
and b.name not like '康师傅%'
and b.name not like '大唐人家%'
and b.name not like '友田达%'
and b.name not like '伊姿瘦身%'
and b.name not like '禾绿%'
and b.name not like 'A+1SPA%'
and b.name not like '拍我丫%'
and b.name not like '寿全斋%'
and b.name not like '到家美食会%'
and b.name not like '华胜奔驰%'
and b.name not like '纽西之谜%'
and b.name not like '盘子女人坊%'
and b.name not like '1点点%'
group by z.id
) rb on rb.zd=ob.z
where ob.c>1
";
        return $this->query($sql);
    }

}