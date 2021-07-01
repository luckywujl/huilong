<?php

namespace app\admin\controller\base;

use app\common\controller\Backend;

/**
 * 车型
 *
 * @icon fa fa-circle-o
 */
class Mototype extends Backend
{
    
    /**
     * Mototype模型对象
     * @var \app\admin\model\base\Mototype
     */
    protected $model = null;
    //protected $selectpageFields = 'mototype';
    protected $searchFields = 'mototype';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['index','getmototypetare'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\base\Mototype;

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
     * 根据车型获取车型皮重
     */
    public function getmototypetare()
    {
      if (!empty($this->request->post("mototype"))){
    	
    	$mototype = $this->request->post("mototype");
    	$mototypetare = $this->model
        ->where(['mototype'=>$mototype,'company_id'=>$this->auth->company_id])
        ->find();   
       if ($mototypetare){
       	  //查找车型信息，获取车皮重      	   
       		$this->success(null,null,$mototypetare);
       	}else {
        	 $this->error('通道信息异常，请核实',null,null);
       	}   	 
       }   
    }

}
