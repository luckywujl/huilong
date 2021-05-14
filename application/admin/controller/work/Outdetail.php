<?php

namespace app\admin\controller\work;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\work as work;
use app\admin\model\custom as custom;

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
                    	
                    	$staparams['statement_outoperator'] = $this->auth->nickname;//出操作员即可当前操作
                    	
                    	$indetail_info = $this->model->where('iodetail_ID',$params['iodetail_in_id'])->find();
                    
                    	$staparams['statement_inoperator'] = $indetail_info['iodetail_operator'];//进操作员
                    
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
    
   

}
