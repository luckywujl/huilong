<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\financial as financial;

/**
 * 交班记录
 *
 * @icon fa fa-circle-o
 */
class Handovers extends Backend
{
    
    /**
     * Handovers模型对象
     * @var \app\admin\model\financial\Handovers
     */
    protected $model = null;
    protected $searchFields = '';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['index','detail'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\financial\Handovers;

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
     * 查看明细
     */
    public function detail($ids = null)
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
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        //获取支付方式信息
        $handoversdetail = new financial\Handoversdetail();//定义数据模型
        $handovers_paymentmode = $handoversdetail
        	->field('handovers_detail_paymentmode')
        	->where(['handovers_detail_paymentmode'=>['NEQ','合计'],'handovers_id'=>$row['handovers_id']])
        	->group('handovers_detail_paymentmode')
        	->select();
        $handovers_object = $handoversdetail
            	->field('handovers_detail_object')
            	//->where($where)
            	->where(['handovers_id'=>$row['handovers_id']])
            	->group('handovers_detail_object')
            	//->order('handovers_detail_object')
            	->paginate($limit);
        
        	
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            
            $handovers_list=[]; 
            foreach ($handovers_object as $k => $v) {
               $rows = [];
               $rows['handovers_detail_object'] = $v['handovers_detail_object'];
               $handovers_detail = $handoversdetail
               	->where($where)
               	->where(['handovers_id'=>$row['handovers_id'],'handovers_detail_object'=>$v['handovers_detail_object']])
               	->order('handovers_detail_paymentmode')
               	->select();
               foreach ($handovers_detail as $j=> $u) {
               	$rows[$u['handovers_detail_paymentmode'].'-count'] = $u['handovers_detail_paycount'];
               	$rows[$u['handovers_detail_paymentmode'].'-cost'] = $u['handovers_detail_payamount'];      
               } 
               $handovers_list[] = $rows;
            }
            $result = array("total" => $handovers_object->total(), "rows" => $handovers_list);
            return json($result);
            
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
        
        $this->assignconfig("item",$handovers_paymentmode ); 
        $this->assignconfig("ids",$ids);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
}
