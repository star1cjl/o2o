<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17
 * Time: 9:31
 */
class ChaoshiorderAction extends CommonAction
{
    /**peace
     * 显示订单详情
     */
    public function index(){
        $Chaoshiorder = M('Chaoshi_order');
        import('ORG.Util.Page'); // 导入分页类

        // 显示待审核的评论
        $map = array('closed' => 0);

        // 保存搜索信息到cookie中
        if(cookie(md5('ChaoshiOrderSearchIndexMessage'))){
            $map = cookie(md5('ChaoshiOrderSearchIndexMessage'));
            if(cookie(md5('ChaoshiOrderSearchIndexMessageOrder'))){
                $map['name'] = cookie(md5('ChaoshiOrderSearchIndexMessageOrder'));
            }
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 搜索信息待完善
            if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
                $map['name'] = array('LIKE', '%' . $keyword . '%');
                $this->assign('keyword', $keyword);
            }

            if (!$keyword) {
                // 根据提交方式判断，是否清除cookie相应信息
                unset($map['name']);
            }

            // 支付状态搜索
            if ($pay_status = (int)$this->_param('pay_status')) {
                if ($pay_status == 100) {
                    unset($map['pay_status']);
                } elseif ($pay_status == 99) {
                    $map['pay_status'] = 0;
                } else {
                    $map['pay_status'] = $pay_status;
                }
                $this->assign('pay_status', $map['pay_status']);
            }

            // 订单状态搜索
            if ($status = (int)$this->_param('status')) {
                if ($status == 100) {
                    unset($map['status']);
                } else if ($status == 99) {
                    $map['status'] = 0;
                } else {
                    $map['status'] = $status;
                }
                $this->assign('status', $map['status']);
            }
        }

        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $keyword = substr(str_replace(' ', '', $map['name'][1]), 1, -1);
            if($keyword != ''){
                $this->assign('keyword', $keyword);
            }
        }

        // 保存搜索信息到cookie中，有效时间15分钟
        cookie(md5('ChaoshiOrderSearchIndexMessage'), $map, 900);
        cookie(md5('ChaoshiOrderSearchIndexMessageOrder'), $map['name'], 900);

        // 分页
        $Page = new Page($Chaoshiorder->where($map)->count(), 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $list = $Chaoshiorder
            ->field('order_id,store_id,name,phone,pay_price,num,pay_status,status,arbitrament_status')
            ->where($map)
            ->order(array('order_id' => 'desc'))
            ->limit($Page->firstRow, $Page->listRows)
            ->select();

        // 选中状态
        $this->assign('pay_status_2', $map['pay_status']);
        $this->assign('status_2', $map['status']);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    /**peace
     * 接收一个订单的order_id
     * 订单详情页面
     */
    public function detail($order_id){
        // 有效的订单
        $map = array('closed' => 0);

        if(is_numeric($order_id) && ($order_id = (int)$order_id)){
            $orderModel = D('Chaoshiorder');
            $list = $orderModel->where($map)->find($order_id);
            if(!$list){
                $this->baoError('该订单不存在或无效');
            }

            $this->assign('list', $list);
            $this->display();
        }
        $this->baoError('该数据无效，请重新选择');
    }

    /**peace
     * ajax请求订单商品详情；
     */
    public function productDetail($order_id = 0, $create_time = 0){
        // 数据合法性验证
        if(!(is_numeric($order_id) && $order_id = (int)$order_id)){
            echo json_encode(array('msg' => '非法id数据', 'error' => '400'));
        }
        if(!(is_numeric($create_time) && $create_time = (int)$create_time)){
            echo json_encode(array('msg' => '非法数据', 'error' => '400'));
        }

        // 查找该订单数据
        $findCreate_time = M('ChaoshiOrder')
            ->where(array('order_id' => $order_id))
            ->getField('create_time');
        if($findCreate_time != $create_time){
            echo json_encode(array('msg' => '未找到订单信息', 'error' => '204'));
        }

        // 查找订单商品
        $findOrderProducts = M('ChaoshiOrderProduct')
            ->alias('ord')
            ->field('ord.*, pro.product_name')
            ->join('LEFT JOIN bao_chaoshi_product pro ON pro.product_id=ord.product_id')
            ->where(array('order_id' => $order_id))
            ->select();
        if(!$findOrderProducts){
            echo json_encode(array('msg' => '未找到订单商品信息', 'error' => '204'));
        }
        echo json_encode(array('msg' => '执行成功', 'info' => $findOrderProducts, 'error' => '200'));
    }

    /**peace
     * 初始化搜索引擎，订单列表
     */
    public function initialIndex(){
        cookie(md5('ChaoshiOrderSearchIndexMessage'), NULL, 0);
        $this->baoSuccess('初始化成功', U('chaoshiorder/index'), 1000);
    }
}