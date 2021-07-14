<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;

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
    protected $noNeedRight = ['pay','paymentmode','recharge','print'];

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
    	  if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                //生成单号
                $main = $this->model
                ->where('charge_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
               // ->where(['company_id'=>$this->auth->company_id,'account_object'=>'客户充值'])
                ->where(['company_id'=>$this->auth->company_id])
            	 -> order('charge_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['charge_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['charge_code'] = 'A'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['charge_code']='A'.date('Ymd').'0001';
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
    	                ->setInc('custom_account',$params['charge_amount']);
    	              $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])  
    	                ->find();
    	              $params['charge_remark'] = $custom_info['custom_account'];  
                    $result = $this->model->allowField(true)->save($params);
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
                    $this->success(null,null,$charge);
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
                    ->with(['customcustom'])
                    ->where($where)
                    ->where(['charge_object'=>'客户充值','charge_operator'=>$this->auth->nickname])
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
     * 支付
     */
    public function pay()
    {
    	  $params = $this->request->param();//接收过滤条件
    	  $this->view->assign("amount",$params['amount'] ); 
        //$this->assignconfig("ids",$ids);
        //$this->view->assign("row", $row);
        return $this->view->fetch();
    }
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
     * 充值
     */
    public function recharge()
    {
    	  if ($this->request->isPost()) {
    	   	$paymentmode = $this->request->param();//接收过滤条件
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                //生成单号
                $main = $this->model
                ->where('charge_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
               // ->where(['company_id'=>$this->auth->company_id,'account_object'=>'客户充值'])
                ->where(['company_id'=>$this->auth->company_id])
            	 -> order('charge_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['charge_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['charge_code'] = 'A'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['charge_code']='A'.date('Ymd').'0001';
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
    	                ->setInc('custom_account',$params['charge_amount']);
    	              $custom_info = $custom
    	                ->where('custom_id',$params['charge_custom_id'])  
    	                ->find();
    	                $remark = '';
    	              $params['charge_custom_account'] = $custom_info['custom_account'];  
    	                $arr = json_decode($paymentmode['payment'], true);
    	                //dump($arr);
    	                foreach($arr as $k => $v){
    	                	$payment = isset($v['paymentmode']) ?$v['paymentmode']:'自定义';
    	                 	$remark = $remark.$payment;
    	                }
    	              $params['charge_remark'] = $remark;//$custom_info['custom_account'];  
                    $result = $this->model->allowField(true)->save($params);
                    $charge['charge_id'] =$this->model->charge_id;//收支ID		
                    Db::commit();
                    $this->success(null,null,$charge);
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
     }




}
