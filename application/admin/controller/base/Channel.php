<?php

namespace app\admin\controller\base;

use app\common\controller\Backend;
use app\admin\model\work as work;
use app\admin\model\custom as custom;

/**
 * 进出通道
 *
 * @icon fa fa-circle-o
 */
class Channel extends Backend
{
    
    /**
     * Channel模型对象
     * @var \app\admin\model\base\Channel
     */
    protected $model = null;
    //protected $selectpageFields = 'channel';
    protected $searchFields = 'channel';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['index','getinchannelinfo','getoutchannelinfo','clearplate'];


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\base\Channel;
        $this->view->assign("channelIotypeList", $this->model->getChannelIotypeList());
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
     * 获取车牌识别结查和称重信息
     */
    public function getinchannelinfo()
    {
      if (!empty($this->request->post("channel_info"))){
    	
    	$channel_info = $this->request->post("channel_info");
    	$channel = $this->model
        ->where(['channel'=>$channel_info])
        ->find();   
       if ($channel){    
       		$this->success(null,null,$channel);
       	}else {
        	 $this->error('通道信息异常，请核实',null,null);
       	}   	 
       }   
    }
        
    /**
     * 获取车牌识别结查和称重信息
     */
    public function getoutchannelinfo()
    {
      if (!empty($this->request->post("channel_info"))){
    	
    	$channel_info = $this->request->post("channel_info");
    	$channel = $this->model
        ->where(['channel'=>$channel_info])
        ->find();   
       if ($channel){ 
            if ($channel['channel_plate_number']){
            	
            	$indetail = new work\Indetail();
            	$detail_info = $indetail
            		->where(['Iodetail_plate_number'=>$channel['channel_plate_number'],'iodetail_iotype'=>1,'iodetail_status'=>0])
            		->where(['company_id'=>$this->auth->company_id])
            		->find();
            	if ($detail_info){
            		$channel['iodetail_ID'] = $detail_info['iodetail_ID'];
            		$channel['iodetail_mototype'] = $detail_info['iodetail_mototype'];		
            		$channel['iodetail_iotime'] = $detail_info['iodetail_iotime'];
            		$channel['iodetail_card_id'] = $detail_info['iodetail_card_id'];
            		$channel['iodetail_card_code'] = $detail_info['iodetail_card_code'];
            		$channel['iodetail_product_id'] = $detail_info['iodetail_product_id'];
            		$channel['iodetail_weight'] = $detail_info['iodetail_weight'];
            		$channel['iodetail_checker'] =$detail_info['iodetail_checker'];
            	} else{
            		$this->error('车辆入场信息异常，请核实',null,null);
            	}
            	$card = new custom\Card();
            	$card_info = $card
                ->where(['card_id'=>$detail_info['iodetail_card_id'],'card_status'=>0])//卡状态要求是正常 
                ->find();  
      
              if ($card_info){
              	
             	 $custom = new custom\Custom();
                $custom_info = $custom 
               	->where(['custom_id'=>$card_info['card_custom_id'],'custom_status'=>0])//商户状态为正常
               	->find();
               	if($custom_info) {
               		$channel['iodetail_custom_id'] = $custom_info['custom_id'];
               	   $channel['iodetail_custom_name'] = $custom_info['custom_name'];
               		$channel['iodetail_custom_address'] = $custom_info['custom_address'];
               		$channel['iodetail_custom_customtype'] = $custom_info['custom_customtype'];
               		$customtype = new custom\Customtype();
               		$customtype_info = $customtype
               		  ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
               		  ->find();
               		$channel['iodetail_custom_customtype_attribute'] = $customtype_info['customtype_attribute'];  
                } else {
               	$this->error('卡号有误或商户状态异常，请核实',null,null);
                }
               } else{
               	$this->error('卡号有误或商户状态异常，请核实',null,null);
               }
       		$this->success(null,null,$channel);
       	}else {
        	 $this->success(null,null,$channel);
       	}   	 
    } else {
			$this->error('通道信息异常，请核实',null,null);
    }
    }
    }
    
    
    /**
     * 清空车牌信息
     */
    public function clearplate()
    {
      if (!empty($this->request->post("channel_info"))){
    	$channel_info = $this->request->post("channel_info");
    	$this->model
        ->where(['channel'=>$channel_info])
        ->update(['channel_plate_number'=>'_无_']);    
       }   
    }
   
}
