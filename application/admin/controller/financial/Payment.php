<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;

/**
 * 收支明细
 *
 * @icon fa fa-circle-o
 */
class Payment extends Backend
{
    
    /**
     * Payment模型对象
     * @var \app\admin\model\financial\Payment
     */
    protected $model = null;
    protected $searchFields = '';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['add'];


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\financial\Payment;
        $this->view->assign("accountTypeList", $this->model->getAccountTypeList());
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
                //生成单号
                $main = $this->model
                ->where('account_date','between time',[date('Y-m-d 00:00:01'),date('Y-m-d 23:59:59')])
                ->where(['company_id'=>$this->auth->company_id])
                //->where('account_object','<>','客户充值')
            	 -> order('account_code','desc')->limit(1)->select();
        	       if (count($main)>0) {
        	       $item = $main[0];
        	  	    $code = '0000'.(substr($item['account_code'],9,4)+1);
        	  	    $code = substr($code,strlen($code)-4,4);
        	      	$params['account_code'] = 'A'.date('Ymd').$code;
        	      	} else {
        	  	   	$params['account_code']='A'.date('Ymd').'0001';
        	      	}
        	      
        	      $params['account_date'] =time();	
               $params['account_operator'] = $this->auth->nickname;//经手人信息为当前操作员 

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
        $params = $this->request->param();//接收过滤条件
        $this->view->assign("row", $params);
        return $this->view->fetch();
    }

}
