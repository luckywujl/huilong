<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;
/**
 * 结算清单
 *
 * @icon fa fa-circle-o
 */
class Statement extends Backend
{
    
    /**
     * Statement模型对象
     * @var \app\admin\model\financial\Statement
     */
    protected $model = null;
    protected $searchFields = 'statement_code,customcustom.custom_code';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['getcustominfobystatementcode'];
    

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\financial\Statement;
        $this->view->assign("statementStatusList", $this->model->getStatementStatusList());
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
           
            	$list = $this->model
                    ->with(['baseproduct','customcustom'])
                    ->where($where)
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
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $params['statement_status'] = 1;
                    $params['statement_date'] = time();
                    $params['statement_operator'] = $this->auth->nickname;
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
     /**
    *反结算
    */
    public function repay($ids="")
    {
    	if ($ids) {
    		$pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $main = $this->model->where($pk, 'in', $ids)->update(['statement_status'=>0]); //获得选中的编印主表集合
    		$this->success('反结算完成');
    	}
    	$this->error(__('Parameter %s can not be empty', 'ids'));
    	$this->success('反结算完成');
    }
    
    /**
    *通过结算单号获取客户信息
    */
    public function getcustominfobystatementcode()
     {
       if (!empty($this->request->post("statement_info"))){
    	
    	$statement_info = $this->request->post("statement_info");
    	$statement = $this->model
        ->where(['statement_code'=>$statement_info])//卡状态要求是正常 
        ->find();  
      
       if ($statement){
       	$custom = new custom\Custom();
       	$custom_info = $custom
       	->where(['custom_id'=>$statement['statement_custom_id'],'custom_status'=>0])//商户状态为正常
       	->find();
       	
       	if($custom_info) {
       		$custom_info['statement_outtime']=$statement['statement_outtime'];
          //$this->error($custom_info['date'],null,null);
       
       		$this->success('执行成功',null,$custom_info);
       	}else {
        	 $this->error('卡号有误或商户状态异常，请核实',null,null);
       	}   	
       } else {
       	
    	   $this->error('结算单号输入有误或异常，请核实',null,null);
    	   
       } 
    } 
     
     
     
     }

}
