<?php

namespace app\admin\controller\base;

use app\common\controller\Backend;
use app\admin\model\custom as custom;

/**
 * 支付方式
 *
 * @icon fa fa-circle-o
 */
class Pay extends Backend
{
    
    /**
     * Pay模型对象
     * @var \app\admin\model\base\Pay
     */
    protected $model = null;
    protected $searchFields = 'account_code,customcustom.custom_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['pay','paymentmode','cardpay'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\base\Pay;

    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 支付方式
     */
    public function paymentmode()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 支付
     */
    public function pay()
    {
    	  $params = $this->request->param();//接收过滤条件
    	  if($params['amount']) {
    	  $this->view->assign("amount",$params['amount'] ); 
        return $this->view->fetch();
     } else {
      	$this->error(__('No rows were inserted'));
     }
    }
    
    /**
    *验证储值卡
    */
    public function cardpay()
    {
    	if ($this->request->isPost()) {
    		$card = new custom\Card();
    		$card_code = $this->request->post('card_code');
    		$card_password = $this->request->post('card_password');
    		$card_amount = $this->request->post('card_amount');
    		$card_in = $card
        ->where(['card_code|card_encode'=>$card_code,'card_status'=>0,'company_id'=>$this->auth->company_id])//卡状态要求是正常 
        ->find();  
      
       if ($card_in){
       	$custom = new custom\Custom();
       	$custom_info = $custom
       	->where(['custom_id'=>$card_in['card_custom_id'],'custom_password'=>$card_password,'custom_status'=>0])//商户状态为正常
       	->find();
       	if($custom_info) {
       		if($custom_info['custom_account']>=$card_amount) {
       		$this->success('执行成功',null,$custom_info);
       	}else {
       		$this->error('账户余额不足，请充值！',null,null);
       	}
       	}else {
        	 $this->error('卡号有误或密码错误，请核实',null,null);
       	}   	
       } else {
       	$custom = new custom\Custom();
       	$custom_info = $custom
       	->where(['custom_name|custom_code|custom_tel'=>$card_code,'custom_password'=>$card_password,'custom_status'=>0])//商户状态为正常
       	->find();
         if($custom_info) {
       		if($custom_info['custom_account']>=$card_amount) {
       		$this->success('执行成功',null,$custom_info);
       	   }else {
       		$this->error('账户余额不足，请充值！',null,null);
       	  }
         } else {
    	   $this->error('02卡号有误或状态异常，请核实',null,null);
    	   }
       } 
    		
    	}
      $params = $this->request->param();//接收过滤条件
    	if($params['amount']) {
    		$this->view->assign("amount",$params['amount'] ); 
      	return $this->view->fetch();
    	} else {
      	$this->error(__('No rows were inserted'));
     	}
    
    }

}
