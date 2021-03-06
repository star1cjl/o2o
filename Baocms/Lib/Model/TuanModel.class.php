<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanModel extends CommonModel {

    protected $pk = 'tuan_id';
    protected $tableName = 'tuan';

    public function _format($data) {
        $data['save'] = $data['price'] - $data['tuan_price'];
        $data['price'] = $data['price'];
        $data['tuan_price'] = $data['tuan_price'];
        $data['mobile_fan'] = $data['mobile_fan'];
        $data['settlement_price'] = $data['settlement_price'];
        $data['discount'] = $data['tuan_price'] * 10 / $data['price'];
        return $data;
    }

    public function CallDataForMat($items) { //专门针对CALLDATA 标签处理的
        if (empty($items))
            return array();
        $obj = D('Shop');
        $shop_ids = array();
        foreach ($items as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $shops = $obj->itemsByIds($shop_ids);
        foreach ($items as $k => $val) {
            $val['shop'] = $shops[$val['shop_id']];
            $items[$k] = $val;
        }
        return $items;
    }

}
