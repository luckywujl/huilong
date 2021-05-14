<?php

namespace app\admin\controller\report;

use app\common\controller\Backend;

/**
 * 结算清单
 *
 * @icon fa fa-circle-o
 */
class Business extends Backend
{
    
    /**
     * Business模型对象
     * @var \app\admin\model\report\Business
     */
    protected $model = null;
    protected $searchFields = '';
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\report\Business;
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
    *查看报表
    */
    public function report() 
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $params = $this->request->param();//接收过滤条件
        //$params['statement_customtype'] = mb_convert_encoding($params['statement_customtype'], 'UTF-8','ascii,GB2312,gbk,UTF-8');;
        //$this->success($params['statement_customtype']);
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
      
        //先获取总共有几天,以及每天的汇总合计，用于排在表格的尾行
        
        
        $statement_day_total = $this->model
                ->field('FROM_UNIXTIME(statement_outtime,"%Y-%m-%d") as statement_outtime,sum(statement_NW) as statement_NW,sum(statement_cost) as statement_cost,count(*) as statement_number')   
                ->where($where)
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])
               
                ->group('FROM_UNIXTIME(statement_outtime,"%Y-%m-%d")')
                ->order('statement_outtime asc') 
                ->select(); 
               
       //再计算所有的汇总总量，用于排在表格的右下角
        $statement_total_NW = $this->model
                ->where($where)  
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])             
                ->sum('statement_NW'); 
        $statement_total_cost = $this->model
                ->where($where)  
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])             
                ->sum('statement_cost'); 
        $statement_total_number = $this->model
                ->where($where)  
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])             
                ->count();         
                
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list=[];    
            //计算有多少个客户，并求出不区分日期的该客户的总量，用于排在表格的尾列
            $statement_custom_total = $this->model
                ->with('customcustom')
                ->field('statement_custom_id,customcustom.custom_name as custom_name,customcustom.custom_code as custom_code,sum(statement_NW) as statement_NW,sum(statement_cost) as statement_cost,count(*) as statement_number')   
                ->where($where)
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])
                ->group('statement_custom_id')
                ->order('statement_NW desc') 
                ->paginate($limit); 
                
            //组装行数据-即每个客户的的名称，总数，每一天的汇总数
            foreach ($statement_custom_total as $k => $v) {
               $row = [];
               $row['statement_custom_id'] = $v['statement_custom_id'];
               $row['custom_name'] = $v['custom_name'];
               $row['custom_code'] = $v['custom_code'];
               $row['statement_NW'] = $v['statement_NW'];
               $row['statement_cost'] = round($v['statement_cost'],2);
               $row['statement_number'] = $v['statement_number'];
               //获取每个节点的数据，即每客户每天的汇总量
               
               $statement_custom_day_total = $this->model
                ->field('statement_custom_id,FROM_UNIXTIME(statement_outtime,"%Y-%m-%d") as statement_outtime,sum(statement_NW) as statement_NW,sum(statement_cost) as statement_cost,count(*) as statement_number')   
                ->where($where)
                ->where('statement_outtime','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])
                ->where('statement_custom_id',$v['statement_custom_id'])
                ->group('statement_custom_id, FROM_UNIXTIME(statement_outtime,"%Y-%m-%d")')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($statement_custom_day_total as $j => $u) {
                 $x =$u['statement_outtime'].'-NW';
                 $row[$x] = $u['statement_NW'];
                 $z =$u['statement_outtime'].'-cost';
                 $row[$z] = round($u['statement_cost'],2);
                 
                 $y =$u['statement_outtime'].'-number';
                 
                 $row[$y] = $u['statement_number'];
                 
               }
              $list[] =$row;  	
               }  
                
               //添加尾部合计
               $row = [];
               $row['custom_code'] ='小计：';
               $row['statement_NW'] = $statement_total_NW;
               $row['statement_cost'] = $statement_total_cost;
               $row['statement_number'] = $statement_total_number;
               
               foreach ($statement_day_total as $j => $u) {
                 $x =$u['statement_outtime'].'-NW';
                 $row[$x] = $u['statement_NW'];
                  
                 $z =$u['statement_outtime'].'-cost';
         
                 $row[$z] = round($u['statement_cost'],2);
                
                 $y =$u['statement_outtime'].'-number';
                 $row[$y] = $u['statement_number'];
                 
                }
                $list[] =$row;  	           

            $result = array("total" => $statement_custom_total->total(), "rows" => $list);

            

            return json($result);
        }
        $this->assignconfig("item",$statement_day_total);
        return $this->view->fetch();
    
    }

}
