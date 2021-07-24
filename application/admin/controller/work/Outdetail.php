<?php

namespace app\admin\controller\work;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\work as work;
use app\admin\model\custom as custom;
use app\admin\model\financial as financial;

/**
 * 进/出明细
 *
 * @icon fa fa-circle-o
 */
class Outdetail extends Backend
{
    
    /**
     * Outdetail模型对象
     * @var \app\admin\model\work\Outdetail
     */
    protected $model = null;
    protected $searchFields = 'iodetail_plate_number';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
	 protected $noNeedRight = ['add','accept'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\work\Outdetail;
        $this->view->assign("iodetailIotypeList", $this->model->getIodetailIotypeList());
        $this->view->assign("iodetailStatusList", $this->model->getIodetailStatusList());
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
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                //生成单号
                $main = $this->model
                ->where('iodetail_iotime','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                ->where(['company_id'=>$this->auth->company_id])
                ->where('iodetail_iotype',0)
            	 ->order('iodetail_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['iodetail_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['iodetail_code'] = 'C'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['iodetail_code']='C'.date('Ymd').'0001';
        	      	}
        	      	
                $params['iodetail_iotype'] = 0;//出入类型为出
                $params['iodetail_status'] =1; //已离场
                $params['iodetail_operator'] = $this->auth->nickname;
                $params['iodetail_remark'] = $params['iodetail_product_price'].'+'.$params['iodetail_discount'];

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->company_id;
                }
                $result = false;
                
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    
                   
                    $result = $this->model->allowField(true)->save($params);
                    $staparams['statement_outdetail_id'] =$this->model->iodetail_ID;//出场单ID
                    //在此处加入将入场记录状态变更为离场状态
                    $this->model->where('iodetail_ID',$params['iodetail_in_id'])->update(['iodetail_status'=>1]);
                    //在些处加入保存结算单的代码
                    //1、先确定结算单单号
                    $sta = new work\Statement();
                   
                    $sta_info = $sta
                    
                      ->where('statement_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
             		    ->where(['company_id'=>$this->auth->company_id]) 
            			 ->order('statement_code','desc')->limit(1)->select();
 
        	     		  if (count($sta_info)>0) {
        	       	  $item = $sta_info[0];
        	       	   
        	  	    	  $code = '0000'.(substr($item['statement_code'],9,4)+1);
        	  	   	  $code = substr($code,strlen($code)-4,4);
        	      	  $staparams['statement_code'] = 'S'.date('Ymd').$code;
        	      	    } else {
        	  	   	  $staparams['statement_code']='S'.date('Ymd').'0001';
        	  	   	
        	      	  }//完成单号确定
       	      	 
        	      	  //2、填充各字段的值
                    $staparams['statement_date'] = time();//待恢复收费后纠正
                    
                    $staparams['statement_plate_number'] = $params['iodetail_plate_number'];
                    $staparams['statement_mototype'] = $params['iodetail_mototype'];
                    $staparams['statement_product_id'] = $params['iodetail_product_id'];
                    $staparams['statement_custom_id'] = $params['iodetail_custom_id'];
         
                    $custom = new custom\Custom();
                    $custom_info = $custom
                         ->where(['custom_id'=>$params['iodetail_custom_id'],'company_id'=>$this->auth->company_id])
                         ->find();
                     $staparams['statement_customtype'] = $custom_info['custom_customtype']; 
                   
                     
                    //确定客户类型，决定皮重与毛重
                    $customtype = new custom\Customtype();
                    
                    $customtype_info = $customtype
                    		->where(['customtype'=>$staparams['statement_customtype'],'company_id'=>$this->auth->company_id])
                    		->find();
                  	
                    	if($customtype_info['customtype_attribute']=="1") {//购买方，出场重量为毛重，进场重量为皮重
                    	$staparams['statement_GW'] = $params['iodetail_weight'];//出场重量
                    	$staparams['statement_tare'] = $params['iodetail_inweight'];//进场重量
                    	
                    	} else { //售卖方，出场重量为皮重，进场重量为毛重
                    	$staparams['statement_GW'] = $params['iodetail_inweight'];//出场重量
                    	$staparams['statement_tare'] = $params['iodetail_weight'];//进场重量
                    	if($params['iodetail_plate_number']<>'_无_') {
                    	//卖方出场，将重量视为车皮重保存入库
                    	$info =[];
                    	$info['moto_platenumber'] = $params['iodetail_plate_number'];
                    	$info['moto_type'] =$params['iodetail_mototype'];
                    	$info['moto_tare'] =$params['iodetail_weight'];
                    	$info['moto_date'] =time();
                    	$info['moto_tarecode'] =$params['iodetail_code'];
                    	$info['moto_operator'] =$this->auth->nickname;
                    	$info['company_id'] =$this->auth->company_id;
                    	//1、先进库在查询该车牌号，如果找到，再进行比较大小，如果找不到则添加
                    	$moto = new work\Motoinfo();
                    	$motoinfo = $moto
                    	    ->where(['moto_platenumber'=>$info['moto_platenumber'],'company_id'=>$info['company_id']])
                    	    ->find();
                    	 //2、如果找到再比大小   
                    	 if($motoinfo) {
                    	 	if($motoinfo['moto_tare']>$info['moto_tare']) {
                    	 		$resu=$moto
                    	    ->where(['moto_platenumber'=>$info['moto_platenumber'],'company_id'=>$info['company_id']])
                    	    ->update($info);
                    	 	}else{	
                    	 	}                  	 
                    	 }else {//如查找不到则直接添加
                    	   $resu = $moto->allowField(true)->save($info);
                    	   }
                      }
                    	}
                    	//以上代码完成车皮重保存
                    	$staparams['statement_NW'] = $params['iodetail_NW'];//毛重减皮重等于净重
                    	
                    	$staparams['statement_product_price'] = $params['iodetail_product_price'];//单价
                    	$staparams['statement_discount'] = $params['iodetail_discount'];//折扣
                   	
                    	$staparams['statement_cost'] = $params['iodetail_cost'];//金额
                    	$staparams['statement_intime'] = $params['iodetail_intime'];//进场时间
                    	$staparams['statement_outtime'] = $params['iodetail_iotime'];//离场时间
                    	  
                    	$staparams['statement_remark'] = $params['iodetail_remark'];//备注
                    	 
                    	$staparams['statement_indetail_id'] = $params['iodetail_in_id'];//进场单ID
                    	
                    	$staparams['statement_checker']= $params['iodetail_checker'];//检查人
                    	
                    	$staparams['statement_status'] = 0;//未清算
                    	
                    	$staparams['company_id'] = $this->auth->company_id;//数据归属
                    	
                    	$staparams['statement_operator'] = $this->auth->nickname;//出操作员即可当前操作
                    	
                    	$indetail_info = $this->model->where('iodetail_ID',$params['iodetail_in_id'])->find();
                    
                    	$staparams['statement_operator'] = $indetail_info['iodetail_operator'];//进操作员
                    
                    	$result1 = $sta->allowField(true)->save($staparams);
                    		
                    	
                    Db::commit();
                     $statement['statement_id'] =$sta->statement_id;//出场单ID	
                    	//$this->error(__('No rows were inserted'));
                     $this->success(null,null,$statement); 
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        //当前是否为关联查询
        $this->relationSearch = true;
        $params = $this->request->param();//接收过滤条件
        //$this->error($params['iodetail_custom_id']);
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(input('?iodetail_custom_id')) {
                 $list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->where(['iodetail_custom_id'=>$params['iodetail_custom_id']])
                    ->order($sort, $order)
                    ->paginate($limit);
                 } else {
                 	$list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
                 }

            foreach ($list as $row) {
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 刷新
     */
    public function refresh()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        $params = $this->request->param();//接收过滤条件
        //$this->error($params['iodetail_custom_id']);
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(input('?iodetail_custom_id')) {
                 $list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->where(['iodetail_custom_id'=>$params['iodetail_custom_id']])
                    ->order($sort, $order)
                    ->paginate($limit);
                 } else {
                 	$list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
                 }

            foreach ($list as $row) {
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
    * 离场结算
    */
    public function accept() 
    {
    		if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $paymentmode = $this->request->param();//接收支付的所有信息
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                $custom = new custom\Custom();
                $account = new financial\Account();
                //生成单号
                $main = $this->model
                ->where('iodetail_iotime','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                ->where(['company_id'=>$this->auth->company_id])
                ->where('iodetail_iotype',0)  //出场
            	 ->order('iodetail_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['iodetail_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['iodetail_code'] = 'L'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['iodetail_code']='L'.date('Ymd').'0001';
        	      	}
        	      	
                $params['iodetail_iotype'] = 0;//出入类型为出
                $params['iodetail_status'] =1; //已离场
                $params['iodetail_operator'] = $this->auth->nickname;
                $params['iodetail_remark'] = $params['iodetail_remark'];

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->company_id;
                }
                $result = false;
                
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    
                   
                    $result = $this->model->allowField(true)->save($params);
                    $staparams['statement_outdetail_id'] =$this->model->iodetail_ID;//出场单ID
                    //在此处加入将入场记录状态变更为离场状态
                    $this->model->where('iodetail_ID',$params['iodetail_in_id'])->update(['iodetail_status'=>1]);
                    //在些处加入保存结算单的代码
                    //1、先确定结算单单号
                    $sta = new work\Statement();
                   
                    $sta_info = $sta
                    
                      ->where('statement_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
             		    ->where(['company_id'=>$this->auth->company_id]) 
            			 ->order('statement_code','desc')->limit(1)->select();
 
        	     		  if (count($sta_info)>0) {
        	       	  $item = $sta_info[0];
        	       	   
        	  	    	  $code = '0000'.(substr($item['statement_code'],9,4)+1);
        	  	   	  $code = substr($code,strlen($code)-4,4);
        	      	  $staparams['statement_code'] = 'S'.date('Ymd').$code;
        	      	    } else {
        	  	   	  $staparams['statement_code']='S'.date('Ymd').'0001';
        	  	   	
        	      	  }//完成单号确定
       	      	 
        	      	  //2、填充各字段的值
                    $staparams['statement_date'] = time();//待恢复收费后纠正
                    
                    $staparams['statement_plate_number'] = $params['iodetail_plate_number'];
                    $staparams['statement_mototype'] = $params['iodetail_mototype'];
                    $staparams['statement_product_id'] = $params['iodetail_product_id'];
                    $staparams['statement_custom_id'] = $params['iodetail_custom_id'];
         
                    $custom = new custom\Custom();
                    $custom_info = $custom
                         ->where(['custom_id'=>$params['iodetail_custom_id'],'company_id'=>$this->auth->company_id])
                         ->find();
                     $staparams['statement_customtype'] = $custom_info['custom_customtype']; 
                   
                     
                    //确定客户类型，决定皮重与毛重
                    $customtype = new custom\Customtype();
                    
                    $customtype_info = $customtype
                    		->where(['customtype'=>$staparams['statement_customtype'],'company_id'=>$this->auth->company_id])
                    		->find();
                  	
                    	if($customtype_info['customtype_attribute']=="1") {//购买方，出场重量为毛重，进场重量为皮重
                    	$staparams['statement_GW'] = $params['iodetail_weight'];//出场重量
                    	$staparams['statement_tare'] = $params['iodetail_inweight'];//进场重量
                    	
                    	} else { //售卖方，出场重量为皮重，进场重量为毛重
                    	$staparams['statement_GW'] = $params['iodetail_inweight'];//出场重量
                    	$staparams['statement_tare'] = $params['iodetail_weight'];//进场重量
                    	if($params['iodetail_plate_number']<>'_无_') {
                    	//卖方出场，将重量视为车皮重保存入库
                    	$info =[];
                    	$info['moto_platenumber'] = $params['iodetail_plate_number'];
                    	$info['moto_type'] =$params['iodetail_mototype'];
                    	$info['moto_tare'] =$params['iodetail_weight'];
                    	$info['moto_date'] =time();
                    	$info['moto_tarecode'] =$params['iodetail_code'];
                    	$info['moto_operator'] =$this->auth->nickname;
                    	$info['company_id'] =$this->auth->company_id;
                    	//1、先进库在查询该车牌号，如果找到，再进行比较大小，如果找不到则添加
                    	$moto = new work\Motoinfo();
                    	$motoinfo = $moto
                    	    ->where(['moto_platenumber'=>$info['moto_platenumber'],'company_id'=>$info['company_id']])
                    	    ->find();
                    	 //2、如果找到再比大小   
                    	 if($motoinfo) {
                    	 	if($motoinfo['moto_tare']>$info['moto_tare']) {
                    	 		$resu=$moto
                    	    ->where(['moto_platenumber'=>$info['moto_platenumber'],'company_id'=>$info['company_id']])
                    	    ->update($info);
                    	 	}else{	
                    	 	}                  	 
                    	 }else {//如查找不到则直接添加
                    	   $resu = $moto->allowField(true)->save($info);
                    	   }
                      }
                    	}
                    	//以上代码完成车皮重保存
                    	$staparams['statement_NW'] = $params['iodetail_NW'];//毛重减皮重等于净重	
                    	$staparams['statement_product_price'] = $params['iodetail_price'];//单价
                    	$staparams['statement_discount'] = $params['iodetail_discount'];//折扣  	
                    	$staparams['statement_cost'] = $params['iodetail_cost'];//金额
                    	$staparams['statement_pay'] = $params['iodetail_cost'];//金额
                    	$staparams['statement_intime'] = $params['iodetail_intime'];//进场时间
                    	$staparams['statement_outtime'] = $params['iodetail_iotime'];//离场时间         	  
                    	$staparams['statement_remark'] = $params['iodetail_remark'];//备注          	 
                    	$staparams['statement_indetail_id'] = $params['iodetail_in_id'];//进场单ID               	
                    	$staparams['statement_checker']= $params['iodetail_checker'];//检查人              	
                    	$staparams['statement_status'] = 1;//未清算                	
                    	$staparams['company_id'] = $this->auth->company_id;//数据归属           	
                    	$staparams['statement_operator'] = $this->auth->nickname;//出操作员即可当前操作            	
                    	$indetail_info = $this->model->where('iodetail_ID',$params['iodetail_in_id'])->find();                
                    	//以上完成结算单保存
                    //将pay传送过来的支付方式及金额信息（二维JSON）转换成为二维数组，然后遍历
    	              $arr = json_decode($paymentmode['payment'], true);
    	              $paymentmode = ''; 
    	              $account_info = []; 
    	              //生成单号
                	  $main = $account
                	  			->field('account_code')
                				->where('account_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                				->where(['company_id'=>$this->auth->company_id])
            	 				->order('account_code','desc')->limit(1)->select();
        	       		if (count($main)>0) {
        	       				$item = $main[0];
        	  	    				$code_A = '0000'.(substr($item['account_code'],9,4));
        	  	    				//$code_A = substr($code_A,strlen($code_A)-4,4);
        	      				//$acc['account_code'] = 'A'.date('Ymd').$code_A;
        	      			} else {
        	  	   				$code_A='0000';
        	      			}
                    //生成单号
                    foreach($arr as $k => $v){  //在此循环中添加保存到account表中的代码，每一种支付方式均需要保存一次。
    	                  //为每种支付方式保存一次付款记录
    	                	$acc = []; 
    	                	$code_A = '0000'.($code_A+1);
        	  	    			$code_A = substr($code_A,strlen($code_A)-4,4);
        	      			$acc['account_code'] = 'A'.date('Ymd').$code_A;

        	      			$acc['account_date'] =time();	
        	      			$acc['account_type'] ='0';
        	      			$acc['account_object'] = '入场费';;
        	      			if((isset($v['payremark']) ?$v['payremark']:'0')=='') {
        	      				$acc['account_custom_id'] = $staparams['statement_custom_id'];//如果支付方式里不含会员ID，则用结算单中的会员ID
        	      			}else {
        	      				$acc['account_custom_id'] = isset($v['payremark']) ?$v['payremark']:'0';//使用传递过来的会员ID
        	      			}
        	      			$acc['account_amount'] =isset($v['payamount']) ?$v['payamount']:'0';
        	      			$acc['account_cost'] =isset($v['payamount']) ?$v['payamount']:'0';
        	      

                			if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    			$acc[$this->dataLimitField] = $this->auth->company_id;
                			}
               			$acc['account_operator'] = $this->auth->nickname;//经手人信息为当前操作员
               			$acc['account_statement_code'] = $staparams['statement_code'];
               			$acc['account_paymentmode'] = isset($v['paymentmode']) ?$v['paymentmode']:'现金';
               			$acc['account_handovers'] = 0;
               			$acc['account_remark'] = $staparams['statement_remark'];
               			
               			//以上完成付款记录的添加
               			//下面加入储值支付的扣储值卡代码
               			if ($acc['account_paymentmode']=='储值卡') {
                    			//更新客户余额
    	              			$custom_info = $custom
    	                			->where('custom_id',isset($v['payremark']) ?$v['payremark']:'0')
    	                			->find();
    	                		//将实时余额保存到结算表中
    	                		
               				$staparams['statement_custom_account'] = $custom_info['custom_account']-$acc['account_cost'];
               				$acc['account_custom_account'] = $staparams['statement_custom_account'];//将实时余额也写入付款记录表中
    	                		if($custom_info['custom_principal']>=$acc['account_cost']) {	//如果本金账户够支付，则全部用本金支付
    	                			$custom_result = $custom
    	                				->where('custom_id',isset($v['payremark']) ?$v['payremark']:'0')
    	                				->setDec('custom_principal',$acc['account_cost']);
    	                		} else { //否则先用本金支付，不够的用补贴账户支付
    	                			$custom_result = $custom
    	                				->where('custom_id',isset($v['payremark']) ?$v['payremark']:'0')
    	                				->setDec('custom_subsidy',($acc['account_cost']-$custom_info['custom_principal']));
    	                			$custom_result = $custom
    	                				->where('custom_id',isset($v['payremark']) ?$v['payremark']:'0')
    	                				->setDec('custom_principal',$custom_info['custom_principal']);	
    	                		}
    	                		$custom_result = $custom    //不管用本金还是补贴支付，都要更新账户余额
    	                				->where('custom_id',isset($v['payremark']) ?$v['payremark']:'0')
    	                				->setDec('custom_account',$acc['account_cost']);
    	                	$n = $custom_info['custom_name'];//名称
    	                  $m = $custom_info['custom_tel'];//电话
    	                  $a = $acc['account_cost'];//扣款金额
    	                  $b = $custom_info['custom_account']-$acc['account_cost'];//余额
                    		}
                    		$account_info[] =$acc;
               			//完成储值卡扣款
    	                	$payment = (isset($v['paymentmode']) ?$v['paymentmode']:'自定义').(isset($v['payamount']) ?$v['payamount']:'自定义').'、';
    	                 	$paymentmode = $paymentmode.$payment;
    	                }
    	              $staparams['statement_paymentmode'] = mb_substr($paymentmode,0,strlen($paymentmode)-2);//去掉尾部的、号 
						  $result1 = $sta->allowField(true)->save($staparams);
                    $statement['statement_id'] =$sta->statement_id;//结算单ID
                    $statement['type'] = 1;
                    $result = $account->allowField(true)->saveall($account_info);
                    Db::commit();        
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                	  //发送短信
             	     $sendrul = 'http://api.smsbao.com/sms?u=luckywujl&p=635fcbe5a0f9a1d9bb83ca8392d0c827&m='.$m.'&c=【汇隆果品】尊敬的'.urlencode($n).'，您本次缴费'.urlencode($a).'元，账户余额为'.urldecode($b).'元。';//.urlencode($content);
                	  $res = file_get_contents($sendrul);
                	  //完成短信发送
                    $this->success('正在打印离场结算单，请稍等...',null,$statement); 
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
    
    }
    
   

}
