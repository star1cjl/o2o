<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopfavoritesModel extends CommonModel{
    protected $pk   = 'favorites_id';
    protected $tableName =  'shop_favorites';
    
    public function check($shop_id,$user_id){
        $data = $this->find(array('where'=>array('shop_id'=>(int)$shop_id,'user_id'=>(int)$user_id)));
        return $this->_format($data);
    }
    
}