<?php

namespace app\admin\controller\custom;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\financial as financial;

/**
 * 客户信息
 *
 * @icon fa fa-circle-o
 */
class Custom extends Backend
{
    
    /**
     * Custom模型对象
     * @var \app\admin\model\custom\Custom
     */
    protected $model = null;
    protected $searchFields = 'custom_name,custom_code,custom_address,custom_IDentity';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['index'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\custom\Custom;
        $this->view->assign("customStatusList", $this->model->getCustomStatusList());
        $this->view->assign("customSmsList", $this->model->getCustomSmsList());
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
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                //加入身份证检测，以防重复开户
                $custom_info = $this->model
                       ->where(['custom_IDentity'=>$params['custom_IDentity'],'company_id'=>$this->auth->company_id])
                       ->find();
                if($custom_info) {
                  $this->error(__('该身份证号码已被'.$custom_info['custom_name'].'['.$custom_info['custom_code'].']开户，请不要重复开户,'));
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
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    
    /**
     * 批量扣费
     */
    public function deduction($object="",$cost="")
    {
        if ($object) {
        	 if($cost) {
        	 	$account = new financial\Account(); //定义模型
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
        	      			
        	 	$custom_info = $this->model
        	 			->where(['custom_sms'=>1,'company_id'=>$this->auth->company_id])
        	 			->select();
        	 	foreach ($custom_info as $k=>$v) {//依次进行扣费操作
        	 		if($v['custom_principal']>=$cost) { //如果本金额够支付
        	 			$acc = []; 
    	            $code_A = '0000'.($code_A+1);
        	  	    	$code_A = substr($code_A,strlen($code_A)-4,4);
        	      	$acc['account_code'] = 'A'.date('Ymd').$code_A;
        	      			
        	      	$acc['account_date'] =time();	
        	      	$acc['account_type'] ='0';
        	      	$acc['account_object'] = $object;
        	      	$acc['account_custom_id'] =$v['custom_id'];
        	      	$acc['account_amount'] =$cost;
        	      	$acc['account_cost'] =$cost;

                	if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    	$acc[$this->dataLimitField] = $this->auth->company_id;
                	}
               	$acc['account_operator'] = $this->auth->nickname;//经手人信息为当前操作员
               	
               	$acc['account_paymentmode'] = '储值卡';
               	$acc['account_handovers'] = 1;//不纳入交接班数据
               	$acc['account_remark'] = '批量扣费-'.$object;
               	$acc['account_custom_account']=$v['custom_account']-$cost;		
               	$account_info[] =$acc;		
        	 			$custom_result = $this->model  //使用本金账户支付
    	                				->where('custom_id',$v['custom_id'])
    	                				->setDec('custom_principal',$cost);
    	            $custom_result = $this->model  //使用本金账户支付
    	                				->where('custom_id',$v['custom_id'])
    	                				->setDec('custom_account',$cost);
    	            
    	             //发送短信
             	     $sendrul = 'http://api.smsbao.com/sms?u=luckywujl&p=635fcbe5a0f9a1d9bb83ca8392d0c827&m='.$v['custom_tel'].'&c=【汇隆果品】尊敬的'.urlencode($v['custom_name']).'，您本次缴费'.urlencode($cost).'元('.urlencode($object).')，账户余额为'.urldecode($v['custom_account']-$cost).'元。';//.urlencode($content);
                	  $res = file_get_contents($sendrul);
                	  //完成短信发送
        	 		} else { //如不够支付，则关闭短信通知，并发送关闭消息
        	 			$custom_result = $this->model  //关闭短信通知
    	                				->where('custom_id',$v['custom_id'])  
    	                				->update(['custom_sms'=>0]);
    	             //发送短信
             	     $sendrul = 'http://api.smsbao.com/sms?u=luckywujl&p=635fcbe5a0f9a1d9bb83ca8392d0c827&m='.$v['custom_tel'].'&c=【汇隆果品】尊敬的'.urlencode($v['custom_name']).'，您本次缴费'.urlencode($cost).'元('.urlencode($object).')因余额不足失败，我们将暂时为您关闭短信通知服务,请尽快充值！';//.urlencode($content);
                	  $res = file_get_contents($sendrul);
                	  //完成短信发送   				
        	 		}
        	 	}
        	 	$result1 = $account->allowField(true)->saveall($account_info);//保存收款记录
        	 	if($result1) {
        	 	$this->success('扣费完成');
        	  }
        	 $this->error('扣费失败！');
        	 }
        	 $this->error('请输入有效的扣费金额！');
        }
        $this->error('请输入有效的扣费项目！');
    }
}
