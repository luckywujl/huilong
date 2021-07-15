<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:105:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/public/../application/admin/view/base/pay/pay.html";i:1626355198;s:97:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1624286200;s:94:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1624286200;s:96:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1624286200;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
		<h3>应付金额：</h3>
      <center><h1>￥<?php echo $amount; ?></h1></center>
    <div class="col-xs-12 col-sm-6" hidden="hidden" >
      <input id="c-amount_c"  class="form-control" name="row[amount_c]" type="number" value="<?php echo $amount; ?>">
	 </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-3"><?php echo __('Paymentmode'); ?>:</label>
        <div class="col-xs-12 col-sm-6">
            <input id="c-paymentmode" class="form-control selectpage" data-source="base/pay/paymentmode" data-field="paymentmode" data-primary-key="paymentmode" name="row[paymentmode]" type="text" value="现金">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-3"><?php echo __('Amount'); ?>:</label>
        <div class="col-xs-12 col-sm-6">
            <input id="c-amount" class="form-control" name="row[amount]" type="number" value="0">
        </div>
        <div class="col-xs-12 col-sm-2">
           <button type="button" class="btn btn-success btn-pay " onClick="addRow()"><?php echo __('Add'); ?></button>
           <button type="button" class="btn btn-del btn-del " onClick="delRow()"><?php echo __('Del'); ?></button>
          
       </div>
    </div>
    
    
    <table width="400" cellspacing="2" id="table1" style="font-size:14px; border:1px solid #cad9ea;margin:0 auto;margin-top:20px">
		<tr bgcolor="#90EE90" height=30>
			<td>序号</td>
			<td>支付方式</td>
			<td>支付金额</td>
		</tr>
		<tr  height=30>
			<td>1</td>
			<td>现金</td>
			<td><?php echo $amount; ?></td>
		</tr>
	</table>

<div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-4"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="button" class="btn btn-success btn-accept "><?php echo __('OK'); ?></button>
            <label class="control-label col-xs-12 col-sm-2"></label>
         
        </div>
    </div>
</form>
<script>
var flag=false,number=1,amount=0;
function addRow(){
	if ($("#c-paymentmode").val()!=="") {
	  if ($("#c-amount").val()>$("#c-amount_c").val()) {
				//$("#c-amount").val($("#c-amount_c").val());
			}
		if ($("#c-amount").val()>0) {
			
			//添加一行
    		var newTr=table1.insertRow(table1.rows.length);
    		//添加两列
    		var newTd0=newTr.insertCell();
    		var newTd1=newTr.insertCell();
    		var newTd2=newTr.insertCell();
    		//设置列内容和属性
    
    		newTr.bgcolor="#909090";
    
    		number++;
    		newTd0.innerText=number;
    		newTd0.height = 30;
    		newTd1.innerText=$("#c-paymentmode").val();
    		newTd2.innerText=$("#c-amount").val();
    		//$("#c-amount").val($("#c-amount_c").val()-$("#c-amount").val());
    		countRow();
    		
    		
		}
	}
    
    
}
function delRow(){
    if(number>0){
        
        flag=!flag;
        table1.deleteRow(table1.rows.length-1);
        //$("#c-amount").val($("#c-amount_c").val()-$("#c-amount").val());
        number--;
        
    }
    countRow();
}

function countRow(){
    amount = 0
    if(number>0){
    	var t =document.getElementById('table1')
       for (var i=1;i<t.rows.length;i++) {
       	 amount = amount+parseFloat(t.rows[i].cells[2].innerText);
       } 
       
       //$("#c-amount_c").val(amount);
    }
    $("#c-amount").val($("#c-amount_c").val()-amount);
    
}


</script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
