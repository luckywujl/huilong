<?php

namespace app\admin\controller\work;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;
use app\admin\model\base as base;
use app\admin\model\work as work;
use app\admin\model\financial as financial;

/**
 * 进/出明细
 *
 * @icon fa fa-circle-o
 */
class Indetail extends Backend
{
    
    /**
     * Indetail模型对象
     * @var \app\admin\model\work\Indetail
     */
    protected $model = null;
    protected $searchFields = 'iodetail_plate_number,iodetail_code,iodetail_card_code,customcustom.custom_name,customcustom.custom_tel';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['add','list','getindetailinfobyid','getindetailinfobyplate','getindetailinfobycard','getoutdetailinfo'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\work\Indetail;
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
                ->where('company_id',$this->auth->company_id)
                ->where('iodetail_iotype',1)
            	 -> order('iodetail_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['iodetail_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['iodetail_code'] = 'J'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['iodetail_code']='J'.date('Ymd').'0001';
        	      	}
                
                $params['iodetail_iotype'] = 1;//出入类型为入
                $params['iodetail_iotime'] =time();//出入类型为入
                $params['iodetail_weight'] =$params['iodetail_GW'];//进出重量
                $params['iodetail_status'] = 0;//初始为在场状态
                $params['iodetail_operator'] = $this->auth->nickname;//添加操作员信息
                $custom = new custom\Custom();
                $custom_info = $custom
                ->where(['custom_id'=>$params['iodetail_custom_id'],'custom_status'=>0,'company_id'=>$this->auth->company_id])//商户状态为正常
                ->find();
                $customtype = new custom\Customtype();
                $customtype_info = $customtype
                ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
                ->find();
                if($customtype_info['customtype_attribute']==1) {
                  $params['iodetail_product_id'] = '';//如果是采购方进场，则无需货品信息
                  //如查是采购方，可视为空车，将空车车皮重写入库中
                  
                }

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
                    //$this->error(__('No rows were inserted'));
                    
                    $iodetail['iodetail_id'] =$this->model->iodetail_ID;//进场单ID	
                    //如查是采购方，可视为空车，将空车车皮重写入库中
                    if($customtype_info['customtype_attribute']==1) {
                    	$info =[];
                    	$info['moto_platenumber'] = $this->model->iodetail_plate_number;
                    	$info['moto_type'] =$this->model->iodetail_mototype;
                    	$info['moto_tare'] =$this->model->iodetail_weight;
                    	$info['moto_date'] =time();
                    	$info['moto_tarecode'] =$this->model->iodetail_code;
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
                    Db::commit();	
                    $this->success(null,null,$iodetail); //返回进场单号给
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
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    //->where(['iodetail_status'=>0,'iodetail_iotype'=>1])  //未结算,入数据
                    ->where(['iodetail_iotype'=>1])  //未结算,入数据
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 进场收费
     */
    public function add()
    {
        if ($this->request->isPost()) {
         	$paymentmode = $this->request->param();//接收支付的所有信息
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $custom = new custom\Custom();
                $account = new financial\Account();
               
                //生成单号
                $main = $this->model
                ->where('iodetail_iotime','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                ->where('company_id',$this->auth->company_id)
                ->where('iodetail_iotype',1)
            	 -> order('iodetail_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['iodetail_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['iodetail_code'] = 'J'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['iodetail_code']='J'.date('Ymd').'0001';
        	      	}
                
                $params['iodetail_iotype'] = 1;//出入类型为入
                $params['iodetail_status'] = 1;//初始为在场状态
                $params['iodetail_iotime'] = time();//进场时间
                $params['iodetail_operator'] = $this->auth->nickname;//添加操作员信息
                $params['iodetail_weight'] = $params['iodetail_GW'];
                $custom = new custom\Custom();
                $custom_info = $custom
                ->where(['custom_id'=>$params['iodetail_custom_id'],'custom_status'=>0,'company_id'=>$this->auth->company_id])//商户状态为正常
                ->find();
                $customtype = new custom\Customtype();
                $customtype_info = $customtype
                ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
                ->find();
                if($customtype_info['customtype_attribute']==1) {
                  $params['iodetail_product_id'] = '';//如果是采购方进场，则无需货品信息
                  //如查是采购方，可视为空车，将空车车皮重写入库中
                  
                }

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
                    $staparams['statement_indetail_id'] =$this->model->iodetail_ID;//进场单ID
                    //开始保存结算单
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
                    $staparams['statement_date'] = time();
                    $staparams['statement_plate_number'] = $params['iodetail_plate_number'];
                    $staparams['statement_mototype'] = $params['iodetail_mototype'];
                    $staparams['statement_product_id'] = $params['iodetail_product_id'];
                    $staparams['statement_custom_id'] = $params['iodetail_custom_id'];
                    $staparams['statement_customtype'] = $params['iodetail_custom_customtype'];
                    $staparams['statement_GW'] = $params['iodetail_GW'];//毛重
                    $staparams['statement_tare'] = $params['iodetail_tare'];//皮重
                    $staparams['statement_NW'] = $params['iodetail_NW'];//净重
                    $staparams['statement_product_price'] = $params['iodetail_price'];//单价
                    $staparams['statement_discount'] = 100;//折扣
                    $staparams['statement_cost'] = $params['iodetail_cost'];//金额
                    $staparams['statement_intime'] = time();;//进场时间
                    $staparams['statement_pay'] = $params['iodetail_cost'];//金额
                    $staparams['statement_remark'] = $params['iodetail_remark'];//备注 
                    $staparams['statement_checker']= $params['iodetail_checker'];//检查人
                    $staparams['statement_status'] = 1;//未清算
                    $staparams['company_id'] = $this->auth->company_id;//数据归属
                    $staparams['statement_operator'] = $this->auth->nickname;//出操作员即可当前操作
                    
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
                    $this->success('正在打印入场收费单据，请稍等...',null,$statement); //返回进场单号给
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    
    /**
     * 根据车牌查找对应的入场记录
     */
    public function getindetailinfobyplate()
    {
      	
    	 if (!empty($this->request->post("iodetail_plate_number"))){
    	 	$plate_number = $this->request->post("iodetail_plate_number");
    	 	
    	 	$detail_info = $this->model
    	 		->where(['Iodetail_plate_number'=>$plate_number,'iodetail_iotype'=>1,'iodetail_status'=>0])
            ->where(['company_id'=>$this->auth->company_id])
            ->find();
         if($detail_info) {
         	$custom = new custom\Custom();
            $custom_info = $custom 
              ->where(['custom_id'=>$detail_info['iodetail_custom_id'],'custom_status'=>0])//商户状态为正常
              ->find();
            if($custom_info) {
               $detail_info['iodetail_custom_id'] = $custom_info['custom_id'];
               $detail_info['iodetail_custom_name'] = $custom_info['custom_name'];
               $detail_info['iodetail_custom_address'] = $custom_info['custom_address'];
               $detail_info['iodetail_custom_customtype'] = $custom_info['custom_customtype'];
               $customtype = new custom\Customtype();
               		$customtype_info = $customtype
               		  ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
               		  ->find();
               		$detail_info['iodetail_custom_customtype_attribute'] = $customtype_info['customtype_attribute'];  
                 
                $this->success(null,null,$detail_info);   
            } else { 
              $this->error('卡号有误或商户状态异常，请核实',null,null);
         }  
    	 } else {
    	    $this->error('未找到该车辆的入场信息，请核实',null,null);
    	 } 
    }
   }
   
   /**
     * 根据卡信息查找对应的入场记录
     */
    public function getindetailinfobycard()
    {
    	//当前是否为关联查询
        $this->relationSearch = true;
    	 if (!empty($this->request->post("card_info"))){
    	 	$card_info = $this->request->post("card_info");
    	 	//1、先根据卡号或卡内码查找卡信息
    	 	$cardinfo = new custom\Card();
    	 	$card = $cardinfo
          ->where(['card_code|card_encode'=>$card_info,'card_status'=>0])//卡状态要求是正常 
          ->find();
          //2、根据找到的卡信息，查找入场记录
          if ($card){
          	$detail_info = $this->model
          	
    	 		->where(['Iodetail_card_id'=>$card['card_id'],'iodetail_iotype'=>1,'iodetail_status'=>0])
            ->where(['company_id'=>$this->auth->company_id])
            ->select();
            } else {
            	//3、如果输入的不是卡号或卡内码，再根据入场单号查找入场记录
            $detail_info = $this->model
            
    	 		->where(['Iodetail_code'=>$card_info,'iodetail_iotype'=>1,'iodetail_status'=>0])
            ->where(['company_id'=>$this->auth->company_id])
            ->select();	
          	
          } 
         if($detail_info) {
         	   $custom = new custom\Custom();
               $custom_info = $custom 
                ->where(['custom_id'=>$detail_info[0]['iodetail_custom_id'],'custom_status'=>0])//商户状态为正常
                ->find();
               $customtype = new custom\Customtype();
               $customtype_info = $customtype
                 ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
                 ->find();
                 
               $detail['recordnumber'] = count($detail_info);  
               $detail['data'] =$detail_info;  
               $detail['custom'] =$custom_info;
               $detail['customtype'] = $customtype_info;
               
               $this->success(null,null,$detail); 
           
         
    	 } else {
    	    $this->error('未找到该卡的入场信息，请核实',null,null);
    	 }
    }
   }
   
    /**
     * 打印
     */
    public function print()
    {
    	 $params = $this->request->param();//接收过滤条件
    	 if(input('?iodetail_id')) {
    	   $iodetail_info = $this->model
    	   ->where('iodetail_id',$params['iodetail_id'])->find();
    	  $product = new base\Product();
    	  $product_info = $product
    	    ->where('product_id',$iodetail_info['iodetail_product_id'])
    	    ->find();
    	  $iodetail_info['product_name'] = $product_info['product_name'];
    	  $custom = new custom\Custom();
    	  $custom_info = $custom
    	    ->where('custom_id',$iodetail_info['iodetail_custom_id'])
    	    ->find();
    	  $iodetail_info['custom_name'] = $custom_info['custom_name'];
    	  $iodetail_info['custom_tel'] = $custom_info['custom_tel'];
    	  $iodetail_info['custom_businessarea'] = $custom_info['custom_businessarea'];
    	  $iodetail_info['intime'] = date('Y-m-d H:i:s', $iodetail_info['iodetail_iotime']);
    	  $iodetail_info['custom_customtype']= $custom_info['custom_customtype'];//客户类型
    	  $iodetail_info['iodetail_operator'] = $this->auth->nickname;
       

        $result = array("data" => $iodetail_info);
       
    	 }else { 
        
         }
        
        return json($result);
    }
    
    /**
     * 出场记录列表
     */
    public function list()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        $params = $this->request->param();//接收过滤条件
    	 	//1、先根据卡号或卡内码查找卡信息
    	 	$cardinfo = new custom\Card();
    	 	$card = $cardinfo
          ->where(['card_code|card_encode'=>$params['card_info'],'card_status'=>0])//卡状态要求是正常 
          ->find();
          
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->where('iodetail_custom_id',$card['card_custom_id'])
                    ->where(['iodetail_status'=>0,'iodetail_iotype'=>1])  //未结算,入数据
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 根据卡信息查找对应的入场记录
     */
    public function getindetailinfobyid()
    {
    		
    	 if (!empty($this->request->param())){
    	 	$params = $this->request->param();//接收过滤条件
    	 	   $ids = $params['iodetail_id'];
            
    	 	   $row = $this->model->get($ids);
    	 	   
            //再根据入场单号ID查找入场记录
            $detail_info = $this->model
            
    	 		->where(['iodetail_ID'=>$row['iodetail_ID'],'iodetail_iotype'=>1,'iodetail_status'=>0])
            ->where(['company_id'=>$this->auth->company_id])
            ->select();	
          //	$this->error($params['iodetail_id'],null,null);
          // $this->error($ids,null,null);
         if($detail_info) {
         	   $custom = new custom\Custom();
               $custom_info = $custom 
                ->where(['custom_id'=>$detail_info[0]['iodetail_custom_id'],'custom_status'=>0])//商户状态为正常
                ->find();
               $customtype = new custom\Customtype();
               $customtype_info = $customtype
                 ->where(['customtype'=>$custom_info['custom_customtype'],'company_id'=>$this->auth->company_id])
                 ->find();
                 
               $detail['recordnumber'] = count($detail_info);  
               $detail['data'] =$detail_info;  
               $detail['custom'] =$custom_info;
               $detail['customtype'] = $customtype_info;
               
               $this->success(null,null,$detail); 
           
         
    	 } else {
    	    $this->error('未找到该卡的入场信息，请核实',null,null);
    	 }
    	 }
    }
   
}
