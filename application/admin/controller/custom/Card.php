<?php

namespace app\admin\controller\custom;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\custom as custom;


/**
 * 卡信息
 *
 * @icon fa fa-circle-o
 */
class Card extends Backend
{
    
    /**
     * Card模型对象
     * @var \app\admin\model\custom\Card
     */
    protected $model = null;
    protected $searchFields = 'card_code,card_encode,customcustom.custom_name,customcustom.custom_code,customcustom.custom_address';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['getcardinfo'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\custom\Card;
        $this->view->assign("cardStatusList", $this->model->getCardStatusList());
        $this->view->assign("customStatusList", $this->model->getCustomStatusList());
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
                    ->with(['customcustom'])
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
     * 查找卡信息
     */
    public function getcardinfo()
    {
      if (!empty($this->request->post("card_info"))){
    	
    	$card_info = $this->request->post("card_info");
    	$card = $this->model
        ->where(['card_code|card_encode'=>$card_info,'card_status'=>0])//卡状态要求是正常 
        ->find();  
      
       if ($card){
       	$custom = new custom\Custom();
       	$custom_info = $custom
       	->where(['custom_id'=>$card['card_custom_id'],'custom_status'=>0])//商户状态为正常
       	->find();
       	if($custom_info) {
       		$custom_info['card_code']=$card['card_code'];
       		$custom_info['card_id']=$card['card_id'];
       
       		$this->success('执行成功',null,$custom_info);
       	}else {
        	 $this->error('卡号有误或商户状态异常，请核实',null,null);
       	}   	
       } else {
       	$custom = new custom\Custom();
       	$custom_info = $custom
       	->where(['custom_name'=>$card_info,'custom_status'=>0])//商户状态为正常
       	->find();
         if($custom_info) {
       		$custom_info['card_code']=$card['card_code'];
       		$custom_info['card_id']=$card['card_id'];
       
       		$this->success('执行成功',null,$custom_info);
         } else {
    	   $this->error('卡号有误或状态异常，请核实',null,null);
    	   }
       } 
    } 
    
 }

}
