<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;
use app\admin\model\financial as financial;

/**
 * 收支明细
 *
 * @icon fa fa-circle-o
 */
class Recharge extends Backend
{
    
    /**
     * Recharge模型对象
     * @var \app\admin\model\financial\Recharge
     */
    protected $model = null;
    protected $searchFields = 'charge_code,customcustom.custom_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\financial\Recharge;
        $this->view->assign("chargeTypeList", $this->model->getChargeTypeList());
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
            $total = $this->model
            		  ->field('count(*) as count,sum(charge_principal) as charge_principal,sum(charge_subsidy) as charge_subsidy')
            		  ->where($where)
            		  ->where(['charge_object'=>'客户充值','charge_operator'=>$this->auth->nickname])
            		  ->select();
            $list_total=[];
            $list_total['charge_type'] = '合计：';
            $list_total['charge_principal'] = sprintf("%.2f", $total[0]['charge_principal']);
            $list_total['charge_subsidy'] = sprintf("%.2f", $total[0]['charge_subsidy']);		
           

            $list = $this->model
                    ->with(['customcustom'])
                    ->where($where)
                    ->where(['charge_object'=>'客户充值','charge_operator'=>$this->auth->nickname])
                    ->order($sort, $order)
                    ->paginate($limit);
             $list[] = $list_total;      

            foreach ($list as $row) {
                
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 打印
     */
    public function print()
    {
    	 $params = $this->request->param();//接收过滤条件
    	 if(input('?charge_id')) {
    	   $charge_info = $this->model
    	   ->where('charge_id',$params['charge_id'])->find();
    	 
    	  $custom = new custom\Custom();
    	  $custom_info = $custom
    	    ->where('custom_id',$charge_info['charge_custom_id'])
    	    ->find();
    	  $charge_info['charge_date'] =date('Y-m-d H:i:s', $charge_info['charge_date']);
    	  $charge_info['custom_name'] = $custom_info['custom_name'];
    	  $charge_info['custom_tel'] = $custom_info['custom_tel'];
    	  $charge_info['custom_businessarea'] = $custom_info['custom_businessarea'];
    	  $charge_info['custom_address'] = $custom_info['custom_address'];
    	  $charge_info['custom_customtype']= $custom_info['custom_customtype'];//客户类型

        $result = array("data" => $charge_info);
       
    	 }else { 
        
         }
        
        return json($result);
    }
    
  
    
    /**
     * 充值
     */
    public function recharge()
    {
        $n = '';//名称
    	  $m = '';//电话
    	  $a = 0;//扣款金额
    	  $b = 0;//余额
    	  if ($this->request->isPost()) {
    	   	$paymentmode = $this->request->param();//接收过滤条件
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                //生成单号
                $main = $this->model
                ->where('charge_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                ->where(['company_id'=>$this->auth->company_id,'charge_object'=>'客户充值'])
            	 -> order('charge_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['charge_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['charge_code'] = 'R'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['charge_code']='R'.date('Ymd').'0001';
        	      	}
        	      
        	      $params['charge_date'] =time();
        	      $params['charge_type'] ='0';	

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->company_id;
                }
                $params['charge_operator'] = $this->auth->nickname;//经手人信息为当前操作员
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    //更新客户余额
                    $custom = new custom\Custom();
    	              $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])
    	                ->setInc('custom_principal',$params['charge_principal']);
    	                $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])
    	                ->setInc('custom_subsidy',$params['charge_subsidy']);
    	                $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])
    	                ->setInc('custom_account',$params['charge_principal']+$params['charge_subsidy']);
    	              $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])  
    	                ->find();
    	              $n = $custom_info['custom_name'];//名称
    	              if($custom_info['custom_sms']==1) {
    	                  	$m = $custom_info['custom_tel'];//电话
    	                  }else {
    	                  	$m='';
    	                  }
    	              $a = $params['charge_principal']+$params['charge_subsidy'];//扣款金额
    	              $b = $custom_info['custom_account'];//余额
    	              $params['charge_custom_account'] = $custom_info['custom_account'];  
    	              $arr = json_decode($paymentmode['payment'], true);
    	              $paymentmode = '';
    	              $account = new financial\Account();
    	              $account_info = []; 
    	              //生成单号
                	  $main = $account
                	  			->field('account_code')
                				->where('account_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                				->where(['company_id'=>$this->auth->company_id])
                				//->where('account_object','<>','客户充值')
            	 				->order('account_code','desc')->limit(1)->select();
        	       		if (count($main)>0) {
        	       				$item = $main[0];
        	  	    				$code_A = '0000'.(substr($item['account_code'],9,4));
        	  	    				//$code_A = substr($code_A,strlen($code_A)-4,4);
        	      				//$acc['account_code'] = 'A'.date('Ymd').$code_A;
        	      			} else {
        	  	   				$code_A='0000';
        	      			}
    	        
    	                foreach($arr as $k => $v){  //在此循环中添加保存到account表中的代码，每一种支付方式均需要保存一次。
    	                  if((isset($v['paymentmode']) ?$v['paymentmode']:'现金')=='储值卡') {
    	                  $this->error(__('暂不开通卡对卡转账功能'));
    	                  }
    	                	$acc = []; 
    	                	$code_A = '0000'.($code_A+1);
        	  	    			$code_A = substr($code_A,strlen($code_A)-4,4);
        	      			$acc['account_code'] = 'A'.date('Ymd').$code_A;
        	      			
        	      			$acc['account_date'] =time();	
        	      			$acc['account_type'] ='0';
        	      			$acc['account_object'] = $params['charge_object'];
        	      			$acc['account_custom_id'] = $params['charge_custom_id'];
        	      			$acc['account_amount'] =isset($v['payamount']) ?$v['payamount']:'0';
        	      			$acc['account_cost'] =isset($v['payamount']) ?$v['payamount']:'0';

                			if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    			$acc[$this->dataLimitField] = $this->auth->company_id;
                			}
                			$acc['account_custom_account'] = $params['charge_custom_account'];//将实时余额也写入付款记录表中
               			$acc['account_operator'] = $this->auth->nickname;//经手人信息为当前操作员
               			$acc['account_statement_code'] = $params['charge_code'];
               			$acc['account_paymentmode'] = isset($v['paymentmode']) ?$v['paymentmode']:'现金';
               			$acc['account_remark'] = $params['charge_remark'];
               			$acc['account_handovers'] = 0;
               			$account_info[] =$acc;
               			
    	                
    	                	$payment = (isset($v['paymentmode']) ?$v['paymentmode']:'自定义').(isset($v['payamount']) ?$v['payamount']:'自定义').'、';
    	                 	$paymentmode = $paymentmode.$payment;
    	                }
    	                
    	              $params['charge_paymentmode'] = mb_substr($paymentmode,0,strlen($paymentmode)-1);
                    $result = $this->model->allowField(true)->save($params);
                    $result1 = $account->allowField(true)->saveall($account_info);
                    $charge['charge_id'] =$this->model->charge_id;//收支ID		
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
                	if($m!=='') {
                	 //发送短信
             	     $sendrul = 'http://api.smsbao.com/sms?u=ha_hlgp&p=6ab37e4a4b3b3f7a06a0227acffd4240&m='.$m.'&c=【汇隆果品】尊敬的'.urlencode($n).'，您本次充值'.urlencode($a).'元，账户余额为'.urldecode($b).'元。';//.urlencode($content);
                	  $res = file_get_contents($sendrul);
                	  //完成短信发送
                }
                    $this->success('充值完成，正在打印凭证',null,$charge);
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
     }




}
