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
class Charge extends Backend
{
    
    /**
     * Charge模型对象
     * @var \app\admin\model\financial\Charge
     */
    protected $model = null;
    protected $searchFields = 'charge_code,charge_statement_code,charge_object,charge_paymentmode,customcustom.custom_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\financial\Charge;
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
                ->where(['company_id'=>$this->auth->company_id])
                ->where('charge_object','<>','客户充值')
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
        	      $params['charge_cost'] =$params['charge_amount'];

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
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                    $charge['charge_id'] =$this->model->charge_id;//出场单ID		
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
                    ->where('charge_object','<>','客户充值')
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

}
