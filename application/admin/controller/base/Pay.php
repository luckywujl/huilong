<?php

namespace app\admin\controller\base;

use app\common\controller\Backend;

/**
 * 支付方式
 *
 * @icon fa fa-circle-o
 */
class Pay extends Backend
{
    
    /**
     * Pay模型对象
     * @var \app\admin\model\base\Pay
     */
    protected $model = null;
    protected $searchFields = 'account_code,customcustom.custom_name';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $noNeedRight = ['pay','paymentmode'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\base\Pay;

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

}
