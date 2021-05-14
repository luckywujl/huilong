<?php

namespace app\admin\controller\work;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;
use app\admin\model\base as base;
use app\admin\model\work as work;
/**
 * 结算清单
 *
 * @icon fa fa-circle-o
 */
class Statement extends Backend
{
    
    /**
     * Statement模型对象
     * @var \app\admin\model\work\Statement
     */
    protected $model = null;
    protected $searchFields = 'statement_plate_number,statement_code,customcustom.custom_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\work\Statement;

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
                    ->with(['customcustom','baseproduct'])
                    ->where($where)
                    ->where(['statement_status'=>0])  //未结算,入数据
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
    	 if(input('?statement_id')) {
    	   $statement_info = $this->model
    	   ->where('statement_id',$params['statement_id'])->find();
    	  $product = new base\Product();
    	  $product_info = $product
    	    ->where('product_id',$statement_info['statement_product_id'])
    	    ->find();
    	  $statement_info['product_name'] = $product_info['product_name'];
    	  $custom = new custom\Custom();
    	  $custom_info = $custom
    	    ->where('custom_id',$statement_info['statement_custom_id'])
    	    ->find();
    	    
    	  $statement_info['custom_name'] = $custom_info['custom_name'];
    	  $statement_info['custom_tel'] = $custom_info['custom_tel'];
    	  $statement_info['custom_customtype'] = $custom_info['custom_customtype'];
    	  
    	  $statement_info['custom_businessarea'] = $custom_info['custom_businessarea'];
    	  $statement_info['intime'] = date('Y-m-d H:i:s', $statement_info['statement_intime']);
        $statement_info['outtime'] = date('Y-m-d H:i:s', $statement_info['statement_outtime']);
        $statement_info['statement_date'] = date('Y-m-d', $statement_info['statement_date']);
        $iodetail = new work\Indetail();
        $iodetail_info = $iodetail
          ->where('iodetail_id',$statement_info['statement_indetail_id'])
          ->find();
        $statement_info['card_code'] = $iodetail_info['iodetail_card_code'];//卡号
        

        $result = array("data" => $statement_info);
       
    	 }else { 
        
         }
        
        return json($result);
    }

}
