<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use app\admin\model\base as base;

/**
 * 示例接口
 */
class Balance extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['test', 'test1','upweight','getplate','receivedeviceinfo'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];

    /**
     * 测试方法
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/demo/test/id/{id}/name/{name})
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     */
    public function test()
    {
        $this->success('返回成功', $this->request->param());
    }

    /**
     * 无需登录的接口
     *
     */
    public function test1()
    {
        $this->success('返回成功', ['action' => 'test1']);
    }

    /**
     * 需要登录的接口
     *
     */
    public function test2()
    {
        $this->success('返回成功', ['action' => 'test2']);
    }

    /**
     * 需要登录且需要验证有相应组的权限
     *
     */
    public function test3()
    {
        $this->success('返回成功', ['action' => 'test3']);
    }
    /**
    *无需登陆，上传称重数据
    */
    public function upweight() {
    //	if ($this->request->isPost()) {
    	//接收数据
    	$params = $this->request->param();
    	$channel = new base\Channel();
    	Db::startTrans();
    	if($params['channel_weight']<=10) {
    	    $channel
    			->where(['channel'=>['like','1号通道%']])
            ->update(['channel_weight'=>$params['channel_weight'],'channel_plate_number'=>'_无_']);//将明细表状态更改为审核
         } else {
          $channel
    			->where(['channel'=>['like','1号通道%']])
            ->update(['channel_weight'=>$params['channel_weight']]);
         }
            Db::commit();
            	 $this->success($params['channel']);
       }
       /**
       *接收车牌信息
       */
      public function getplate() {
       $doc = file_get_contents("php://input");//接收参数JSON
       $jsondecode = json_decode($doc,true); //转码
		 $license = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['license'];//车牌号
		 $ipaddr = $jsondecode['AlarmInfoPlate']['ipaddr'];//摄像机IP地址
       $channel = new base\Channel();
    	  Db::startTrans();
    	  $channel
    			->where(['channel_ipnc'=>$ipaddr])
            ->update(['channel_plate_number'=>$license]);//根据传过来的IP填入车牌号码
        Db::commit();
          
         // 发送开闸命令
          //echo '{"Response_AlarmInfoPlate":{"info":"ok","content":"...","is_pay":"true"}}';
       
       }
      /**
      *接收轮询
      */
      public function receivedeviceinfo() {
    	 $doc = file_get_contents("php://input");
       $jsondecode = json_decode($doc,true);
		 //$license = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['license'];
		 $ipaddr = $jsondecode['AlarmInfoPlate']['ipaddr'];
		 $channel = new base\Channel();
		 $channel_info = $channel
		        ->where(['channel_ipnc'=>$ipaddr])
		        ->find();
		 if ($channel_info['channel_status']==1) {
		 	Db::startTrans();
		 	// 发送开闸命令
		 	$channel
    			->where(['channel_ipnc'=>$ipaddr])
            ->update(['channel_status'=>0]);//根据传过来的IP填入车牌号码
            
         // echo '{"Response_AlarmInfoPlate":{"info":"ok","content":"...","is_pay":"true"}}';
          Db::commit();
		 } else {
		 //echo '{"Response_AlarmInfoPlate":{"info":"ok","content":"...","is_pay":"true"}}';
		 }
}

}
