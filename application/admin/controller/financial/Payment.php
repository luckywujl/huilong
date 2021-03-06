<?php

namespace app\admin\controller\financial;

use app\common\controller\Backend;
use think\Db;
use app\admin\model\financial as financial;
use app\admin\model as model;
use app\admin\model\base as base;


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
    protected $noNeedRight = ['index','handoversindex','handovers'];


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
     * 收支明细
     */
    public function handoversindex()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();  
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                    ->with(['customcustom'])
                    ->where($where)
                    ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
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
     * 交班汇总
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $handovers = new financial\Handovers();//定义数据模型
        $handoversdetail = new financial\Handoversdetail();//定义数据模型
        $paymentmode = new base\Paymentmode();//支付方式
            $handoverstime = $this->model
            			//->where($where)
            			->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
            			->order('account_date','asc')->limit(1)->select();
            if (count($handoverstime)>0) {
        	       $item = $handoverstime[0];
        	       $handovers_begintime = $item['account_date'];//交班开始时间
        	  	    $handoverstime = $this->model
            			//->where($where)
            			->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
            			->order('account_date','desc')->limit(1)->select();
            	 $item = $handoverstime[0];
        	       $handovers_endtime = $item['account_date'];//交班截止时间
        	      	} else {
        	  	   	//$this->error('没有交班数据！');
        	  	   	$handovers_begintime = time();//交班截止时间
        	  	   	$handovers_endtime = time();//交班截止时间
        	      	}
        	    //0、先查一下有没有需要接上一班的数据
        	    $handovers_last = $handovers
        	    	->where(['handovers_successor'=>$this->auth->nickname,'handovers_status'=>0,'company_id'=>$this->auth->company_id])
        	    	->find();

            //1、先获取总共有几种支付方式,包含接上班的以及各种支付方式汇总笔数和金额，用于排在表格最下面一行
            
            if($handovers_last) {//如果有接上班数据则
              if($handovers_last['handovers_type']==0) {
              	$handovers_last_detail = $handoversdetail  //如果是全交下班的方式，则查找上班交班时的所有支付方式
              		->field('handovers_detail_paymentmode as paymentmode')
              		->where(['handovers_id'=>$handovers_last['handovers_id']])
              		->group('paymentmode')
              		->order('paymentmode asc')
              		->select();
              } else {
              $handovers_last_detail = $handoversdetail   //如果是交财务的方式，则查找上班交班时的押金支付方式
              		->field('handovers_detail_paymentmode as paymentmode')
              		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>['like','%押%']])
              		->group('paymentmode')
              		->order('paymentmode asc')
              		->select(); 	
              }
              
              $account_detail = $this->model  //本班交易明细中的支付方式
                ->field('account_paymentmode as paymentmode')   
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->group('account_paymentmode')
                ->order('account_paymentmode asc') 
                ->select(); 
                
              $handovers_paymentmode = $paymentmode  //到支付方式基础表中查询上班的支付方式和本班的支付方式合集
               	->field('paymentmode as account_paymentmode')
               	->where(array('paymentmode'=>array('in',array_column($handovers_last_detail,'paymentmode'))))
               	->whereOr(array('paymentmode'=>array('in',array_column($account_detail,'paymentmode'))))
               	->select(); 
            	
            } else {                             //如果无接班数据，则仅统计本班的支付方式
            	$handovers_paymentmode = $this->model
                ->field('account_paymentmode,count(*) as account_count,sum(account_cost) as account_cost')   
                //->where('account_date','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->group('account_paymentmode')
                ->order('account_paymentmode asc') 
                ->select(); 
            }
            
            
            
       		 
           
                
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $handovers_list=[]; 
            //2、获取上班交班数据（营收和押金） 
            if($handovers_last) {//如果有接上班数据则
              	  if($handovers_last['handovers_type']==0) {
             		$handovers_last_object =$handoversdetail
             			->field('handovers_detail_object as account_object')   
                		->where($where)
                		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>['in','押金合计,营收合计']])
                		->group('account_object')
                		->order('account_object desc') 
                		->select();
          		 } else {
          			$handovers_last_object =$handoversdetail
             			->field('handovers_detail_object as account_object')   
                		->where($where)
                		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>'押金合计'])
                		->group('account_object')
                		->order('account_object asc') 
                		->select();
          		}
          
            		//组装行数据-即接上班方式押金和非押金的名称，总笔数和总额
              foreach ($handovers_last_object as $k => $v) {
               $row = [];
               $row['account_object'] = $v['account_object'].'(接上班)';//支付名称
               //$row['account_count'] = $v['account_count'];//总笔数
               //$row['account_cost'] = round($v['account_cost'],2);//总额
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $handovers_last_object_paymentmode_total = $handoversdetail
                ->field('handovers_detail_object as account_object,handovers_detail_paymentmode as account_paymentmode,handovers_detail_paycount as account_count,handovers_detail_payamount as account_cost')   
                ->where($where)
                ->where(['handovers_id'=>$handovers_last['handovers_id']])
                ->where('handovers_detail_object',$v['account_object'])
                ->group('handovers_detail_object,handovers_detail_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($handovers_last_object_paymentmode_total as $j => $u) {
                 $x =$u['account_paymentmode'].'-count';
                 $row[$x] = $u['account_count'];
                 $z =$u['account_paymentmode'].'-cost';
                 $row[$z] = round($u['account_cost'],2);             
               }
              $handovers_list[] =$row;  	
               } 
       		} 
            //3、计算本班有多少种收支项目，并求出不区分支付的该方式的该项目总数及总额，用于排在表格的最右一列
            $account_object = $this->model
                ->field('account_object as account_object,count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->group('account_object')
                ->order('account_object asc') 
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($account_object as $k => $v) {
               $row = [];
               $row['account_object'] = $v['account_object'].'(本班)';//支付名称
               $row['合计-count'] = $v['account_count'];//总笔数
               $row['合计-cost'] = round($v['account_cost'],2);//总额
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_object,account_paymentmode,count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->where('account_object',$v['account_object'])
                ->group('account_object,account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
                 $x =$u['account_paymentmode'].'-count';
                 $row[$x] = $u['account_count'];
                 $z =$u['account_paymentmode'].'-cost';
                 $row[$z] = round($u['account_cost'],2);             
               }
              $handovers_list[] =$row;  	
               }  
               
               //4、计算本班营收合计 
            $handovers_object_a = $this->model
                ->field('count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->where('account_object','notlike','%押%')
                //->group('account_object')
                //->order('account_object asc') 
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($handovers_object_a as $k => $v) {
               $row = [];
               $row['account_object'] ='营收合计(本班）：';
               $row['合计-count'] = $v['account_count'];//总笔数
               $row['合计-cost'] = round($v['account_cost'],2);//总额
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_paymentmode,count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
               ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->where('account_object','notlike','%押%')
                ->group('account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
                 $x =$u['account_paymentmode'].'-count';
                 $row[$x] = $u['account_count'];
                 $z =$u['account_paymentmode'].'-cost';
                 $row[$z] = round($u['account_cost'],2);             
               }
              $handovers_list[] =$row;  	
               }  
               
               //5、计算本班押金合计 
            $handovers_object_a = $this->model
                ->field('count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->where('account_object','like','%押%')
                //->group('account_object')
                //->order('account_object asc') 
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($handovers_object_a as $k => $v) {
               $row = [];
               $row['account_object'] ='押金合计(本班）：';
               $row['合计-count'] = $v['account_count'];//总笔数
               $row['合计-cost'] = round($v['account_cost'],2);//总额
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_paymentmode,count(*) as account_count,sum(account_cost) as account_cost')   
                ->where($where)
               ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->where('account_object','like','%押%')
                ->group('account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
                 $x =$u['account_paymentmode'].'-count';
                 $row[$x] = $u['account_count'];
                 $z =$u['account_paymentmode'].'-cost';
                 $row[$z] = round($u['account_cost'],2);             
               }
              $handovers_list[] =$row;  	
               }  
               
               //6、添加尾部营收合计
               $row = [];
               $row['account_object'] ='营收合计：';
               $total_count = 0;
               $total_cost = 0;
               foreach ($handovers_paymentmode as $j => $u) {
               	$count =0;
                  $cost = 0; 
                  $total_count = 0;
                  $total_cost = 0;
               	foreach($handovers_list as $k =>$v){  
               		if(stripos($v['account_object'], '营收合计')!==false) {           
               			$x =$u['account_paymentmode'].'-count';
               			$z =$u['account_paymentmode'].'-cost';
               			if(isset($v[$x])) {
               			$count = $count +$v[$x];
               			$cost = $cost + $v[$z];
               			}
               			$total_count = $total_count+$v['合计-count'];
                        $total_cost = $total_cost+$v['合计-cost'];
               		}
               	$x =$u['account_paymentmode'].'-count';
                  $row[$x] = $count;
                  $z =$u['account_paymentmode'].'-cost';
                  $row[$z] = round($cost,2);
               	}   
                }
               $row['合计-count'] = $total_count;
               $row['合计-cost'] = round($total_cost,2); 
               $handovers_list[] =$row;
               
               //7、添加押金尾部合计
               $row = [];
               $row['account_object'] ='押金合计：';
               $total_count = 0;
               $total_cost = 0;
               foreach ($handovers_paymentmode as $j => $u) {
               	$count =0;
                  $cost = 0; 
                  $total_count = 0;
                  $total_cost = 0;
               	foreach($handovers_list as $k =>$v){  
               		if(stripos($v['account_object'], '押金合计')!==false) {           
               			$x =$u['account_paymentmode'].'-count';
               			$z =$u['account_paymentmode'].'-cost';
               			if(isset($v[$x])) {
               			$count = $count +$v[$x];
               			$cost = $cost + $v[$z];
               			}
               			$total_count = $total_count+$v['合计-count'];
                        $total_cost = $total_cost+$v['合计-cost'];
               		}
               	$x =$u['account_paymentmode'].'-count';
                  $row[$x] = $count;
                  $z =$u['account_paymentmode'].'-cost';
                  $row[$z] = round($cost,2);
               	}   
                }
               $row['合计-count'] = $total_count;
               $row['合计-cost'] = round($total_cost,2); 
               $handovers_list[] =$row;
                
            
       		           
            $result = array("total" => $account_object->total(), "rows" => $handovers_list);
            
            return json($result);
            
        }
        $this->assign("handovers_operator",$this->auth->nickname);
        $this->assign("handovers_begintime",$handovers_begintime);
        $this->assign("handovers_endtime",$handovers_endtime);
        $this->assignconfig("item",$handovers_paymentmode ); 
        return $this->view->fetch();
    }
    /**
     * 交接班
     */
    public function handovers()
    {
        if ($this->request->isPost()) {
        	list($where, $sort, $order, $offset, $limit) = $this->buildparams();
         	$params = $this->request->post("row/a");
        	   $handovers = new financial\Handovers();//定义数据模型
        		$handoversdetail = new financial\Handoversdetail();//定义数据模型
        		$paymentmode = new base\Paymentmode();//支付方式
        	   $admin = new model\Admin(); 
        	   
        	   //先进行接班人密码验证(留着以后更新吧)
        	   
        	   
        	   //完成接班人密码验证
        	   
        	   $account_count = $this->model
                ->field('count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->paginate($limit);  
                
            $admin_info = $admin
             	->where($where)
             	->where(['nickname'=>$params['handovers_successor'],'company_id'=>$this->auth->company_id])
             	->find();
            $params['handovers_successor_id'] = $admin_info['id'];
            $params['handovers_count'] =  $account_count[0]['handovers_detail_paycount'];
            $params['handovers_amount'] = $account_count[0]['handovers_detail_payamount'];
            $params['handovers_operator_id'] = $this->auth->id;//交班人ID
            if ($params) {
                $params = $this->preExcludeFields($params);

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
                    $result = $handovers->allowField(true)->save($params);//交接记录主表添加
                    $handovers_id = $handovers->handovers_id;//交接单主表ID
                    //加入交班明细的数据处理流程
            $handovers_detail = [];
            //0、先查一下有没有需要接上一班的数据
        	    $handovers_last = $handovers
        	    	->where(['handovers_successor'=>$this->auth->nickname,'handovers_status'=>0,'company_id'=>$this->auth->company_id])
        	    	->find();

            //1、先获取总共有几种支付方式,包含接上班的以及各种支付方式汇总笔数和金额，用于排在表格最下面一行
            
            if($handovers_last) {//如果有接上班数据则
              if($handovers_last['handovers_type']==0) {
              	$handovers_last_detail = $handoversdetail  //如果是全交下班的方式，则查找上班交班时的所有支付方式
              		->field('handovers_detail_paymentmode as paymentmode')
              		->where(['handovers_id'=>$handovers_last['handovers_id']])
              		->group('paymentmode')
              		->order('paymentmode asc')
              		->select();
              } else {
              $handovers_last_detail = $handoversdetail   //如果是交财务的方式，则查找上班交班时的押金支付方式
              		->field('handovers_detail_paymentmode as paymentmode')
              		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>['like','%押%']])
              		->group('paymentmode')
              		->order('paymentmode asc')
              		->select(); 	
              }
              
              $account_detail = $this->model  //本班交易明细中的支付方式
                ->field('account_paymentmode as paymentmode')   
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->group('account_paymentmode')
                ->order('account_paymentmode asc') 
                ->select(); 
                
              $handovers_paymentmode = $paymentmode  //到支付方式基础表中查询上班的支付方式和本班的支付方式合集
               	->field('paymentmode as account_paymentmode')
               	->where(array('paymentmode'=>array('in',array_column($handovers_last_detail,'paymentmode'))))
               	->whereOr(array('paymentmode'=>array('in',array_column($account_detail,'paymentmode'))))
               	->select(); 
            	
            } else {                             //如果无接班数据，则仅统计本班的支付方式
            	$handovers_paymentmode = $this->model
                ->field('account_paymentmode,count(*) as account_count,sum(account_cost) as account_cost')   
                //->where('account_date','between',[strtotime(mb_substr($params['statement_date'],0,19)),strtotime(mb_substr($params['statement_date'],22,19))])
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->group('account_paymentmode')
                ->order('account_paymentmode asc') 
                ->select(); 
            }
            //2、获取上班交班数据（营收和押金） 
            if($handovers_last) {//如果有接上班数据则
              	  if($handovers_last['handovers_type']==0) {
             		$handovers_last_object =$handoversdetail
             			->field('handovers_detail_object,handovers_detail_paymentmode,handovers_detail_paycount,handovers_detail_payamount')   
                		->where($where)
                		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>['in','押金合计,营收合计']])
                		//->group('handovers_detail_object,handovers_detail_paymentmode')
                		->order('handovers_detail_object desc') 
                		->select();
          		 } else {
          			$handovers_last_object =$handoversdetail
             			->field('handovers_detail_object,handovers_detail_paymentmode,handovers_detail_paycount,handovers_detail_payamount')   
                		->where($where)
                		->where(['handovers_id'=>$handovers_last['handovers_id'],'handovers_detail_object'=>'押金合计'])
                		//->group('handovers_detail_object,handovers_detail_paymentmode')
                		->order('handovers_detail_object asc') 
                		->select();
          		}
          
            	//转存数据
              foreach ($handovers_last_object as $k => $v) {
               $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = $v['handovers_detail_object'].'(接上班)';
               $row['handovers_detail_paymentmode'] = $v['handovers_detail_paymentmode'];
               $row['handovers_detail_paycount'] = $v['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($v['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id;  
               $handovers_detail[] =$row;        
               }
       		} 
       		//3、计算本班有多少种收支项目，并求出不区分支付的该方式的该项目总数及总额，用于排在表格的最右一列
            $account_object = $this->model
                ->field('account_object as handovers_detail_object,count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->group('account_object')
                ->order('account_object asc') 
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($account_object as $k => $v) {
               $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = $v['handovers_detail_object'].'(本班)';
               $row['handovers_detail_paymentmode'] = '合计';
               $row['handovers_detail_paycount'] = $v['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($v['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row; 
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_object as handovers_detail_object,account_paymentmode as handovers_detail_paymentmode,count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->where('account_object',$v['handovers_detail_object'])
                ->group('account_object,account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
                 	$row = [];
               	$row['handovers_id'] = $handovers_id;
               	$row['handovers_detail_object'] = $u['handovers_detail_object'].'(本班)';
               	$row['handovers_detail_paymentmode'] = $u['handovers_detail_paymentmode'];
               	$row['handovers_detail_paycount'] = $u['handovers_detail_paycount'];
               	$row['handovers_detail_payamount'] = round($u['handovers_detail_payamount'],2);
               	$row['company_id'] = $this->auth->company_id; 
               	$handovers_detail[] =$row;           
               }
               }  
               //4、计算本班营收合计 
              $handovers_object_a = $this->model
                ->field('count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->where('account_object','notlike','%押%')
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($handovers_object_a as $k => $v) {
               $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '营收合计(本班)';
               $row['handovers_detail_paymentmode'] = '合计';
               $row['handovers_detail_paycount'] = $v['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($v['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row; 
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_paymentmode as handovers_detail_paymentmode,count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
               ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->where('account_object','notlike','%押%')
                ->group('account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
                 $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '营收合计(本班)';
               $row['handovers_detail_paymentmode'] = $u['handovers_detail_paymentmode'];
               $row['handovers_detail_paycount'] = $u['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($u['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row;              
               }
            
               }  
               
               //5、计算本班押金合计 
            $handovers_object_a = $this->model
                ->field('count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->where('account_object','like','%押%')
                ->paginate($limit);  
             
            
            //组装行数据-即每种支付的名称，总笔数和总额
            foreach ($handovers_object_a as $k => $v) {
               $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '押金合计(本班)';
               $row['handovers_detail_paymentmode'] = '合计';
               $row['handovers_detail_paycount'] = $v['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($v['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row; 
            	//获取每个节点的数据，即每种支付项目各种支付方式的汇总笔数和金额
               $account_object_paymentmode_total = $this->model
                ->field('account_paymentmode as handovers_detail_paymentmode,count(*) as handovers_detail_paycount,sum(account_cost) as handovers_detail_payamount')   
                ->where($where)
               ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0])
                ->where('account_object','like','%押%')
                ->group('account_paymentmode')
                ->order($sort, $order) 
                ->paginate($limit);
               
               foreach ($account_object_paymentmode_total as $j => $u) {
               $row = [];
               $row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '押金合计(本班)';
               $row['handovers_detail_paymentmode'] = $u['handovers_detail_paymentmode'];
               $row['handovers_detail_paycount'] = $u['handovers_detail_paycount'];
               $row['handovers_detail_payamount'] = round($u['handovers_detail_payamount'],2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row;              
               }
            
               }  
               
               //6、添加尾部营收合计
               $total_count = 0;
               $total_cost = 0;
               foreach ($handovers_paymentmode as $j => $u) {
               	
               	$count =0;
                  $cost = 0; 
               	foreach($handovers_detail as $k =>$v){  
               		if(stripos($v['handovers_detail_object'], '营收合计')!==false) { 
               			$x =$u['account_paymentmode'];
               			if(stripos($v['handovers_detail_paymentmode'], $x)!==false) {            
               			$count = $count +$v['handovers_detail_paycount'];
               			$cost = $cost + $v['handovers_detail_payamount'];
               			
               			$total_count = $total_count+$v['handovers_detail_paycount'];
                        $total_cost = $total_cost+$v['handovers_detail_payamount'];
                     }
               		}    
               	} 
               	$row = [];
              		$row['handovers_id'] = $handovers_id;
               	$row['handovers_detail_object'] = '营收合计';
               	$row['handovers_detail_paymentmode'] = $u['account_paymentmode'];
               	$row['handovers_detail_paycount'] = $count;
               	$row['handovers_detail_payamount'] = round($cost,2);
               	$row['company_id'] = $this->auth->company_id; 
               	$handovers_detail[] =$row;    
                }
               $row = [];
              	$row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '营收合计';
               $row['handovers_detail_paymentmode'] = '合计';
               $row['handovers_detail_paycount'] = $total_count;
               $row['handovers_detail_payamount'] = round($total_cost,2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row;  
               
               //7、添加押金尾部合计
               $total_count = 0;
               $total_cost = 0;
               foreach ($handovers_paymentmode as $j => $u) {
               	
               	$count =0;
                  $cost = 0; 
               	foreach($handovers_detail as $k =>$v){  
               		if(stripos($v['handovers_detail_object'], '押金合计')!==false) {           
               			$x =$u['account_paymentmode'];
               			if(stripos($v['handovers_detail_paymentmode'], $x)!==false) {            
               			$count = $count +$v['handovers_detail_paycount'];
               			$cost = $cost + $v['handovers_detail_payamount'];
               			
               			$total_count = $total_count+$v['handovers_detail_paycount'];
                        $total_cost = $total_cost+$v['handovers_detail_payamount'];
                     }
               		}    
               	}  
               	$row = [];
              		$row['handovers_id'] = $handovers_id;
               	$row['handovers_detail_object'] = '押金合计';
               	$row['handovers_detail_paymentmode'] = $u['account_paymentmode'];
               	$row['handovers_detail_paycount'] = $count;
               	$row['handovers_detail_payamount'] = round($cost,2);
               	$row['company_id'] = $this->auth->company_id; 
               	$handovers_detail[] =$row;    
                }
               $row = [];
              	$row['handovers_id'] = $handovers_id;
               $row['handovers_detail_object'] = '押金合计';
               $row['handovers_detail_paymentmode'] = '合计';
               $row['handovers_detail_paycount'] = $total_count;
               $row['handovers_detail_payamount'] = round($total_cost,2);
               $row['company_id'] = $this->auth->company_id; 
               $handovers_detail[] =$row;  
               //以上代码完成明细表添加
               $result1 = $handoversdetail->saveall($handovers_detail);//交接记录明细表写库
               //对原始数据变更状，防止重复交班
               //0、将financial_account表列account_handovers（是否交班）改为1
               $handovers_result = $this->model
                ->where(['account_operator'=>$this->auth->nickname,'account_handovers'=>0,'company_id'=>$this->auth->company_id])
                ->update(['account_handovers'=>1]);
                //1、对上班的交接表状态变更为已接班financial_handovers表上班记录的handovers_status变更为1，即已接班
               $handovers_last_result = $handovers
        	    	->where(['handovers_successor'=>$this->auth->nickname,'handovers_status'=>0,'company_id'=>$this->auth->company_id])
        	    	->update(['handovers_status'=>1]); 
                    
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
                    $this->success('完成交班，正在打印',null,$handovers_id); //返回进场单号给
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


}
