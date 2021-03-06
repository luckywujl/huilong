<?php

namespace app\admin\controller\base;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\base as base;


/**
 * 货品价格
 *
 * @icon fa fa-circle-o
 */
class Productprice extends Backend
{
    
    /**
     * Productprice模型对象
     * @var \app\admin\model\base\Productprice
     */
    protected $model = null;
    protected $searchFields = 'baseproduct.product_code,baseproduct.product_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['index','getproductprice'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\base\Productprice;

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
                    ->with(['baseproduct','baseproducttype'])
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
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $product = new base\Product();
                //添加货品品类ID
                $productinfo = $product->where('product_ID',$params['productprice_product_id'])->find();
                $params['productprice_producttype_id'] = $productinfo['product_producttype_ID'];

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
     * 根据时段获取货品价格
     */
    public function getproductprice()
    {
      if (!empty($this->request->post("product_id"))){
    	
    	$product_id = $this->request->post("product_id");
    	$custom_type = $this->request->post("custom_type");
    	$productprice = $this->model
        ->where(['productprice_product_id'=>$product_id,'productprice_customtype'=>$custom_type,'company_id'=>$this->auth->company_id])
        
        ->where(['productprice_begin_time'=>['ELT',date('H:m:s', time())]])
        ->where(['productprice_end_time'=>['EGT',date('H:m:s', time())]])
        ->find();   
       if ($productprice){
       	  //查找车型信息，获取车皮重      	   
       		$this->success(date('H:m:s', time()),null,$productprice);
       	}else {
        	 $this->error('货品价格设置不完整，请核实',null,null);
       	}   	 
       }   
    }
}
