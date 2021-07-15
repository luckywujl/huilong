<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:119:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/public/../application/admin/view/financial/handovers/detail.html";i:1626111183;s:97:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1624286200;s:94:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1624286200;s:96:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1624286200;}*/ ?>
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
                                <div class="panel panel-default panel-intro">
    <?php echo build_heading(); ?>

    <div class="panel-body">
    <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    
    <table width="95%"  class="gridtable">
	 <thead>
		<center><h3>交班表</h3></center>
		
		<tr>
			<td><?php echo __('Handovers_operator'); ?>:</td>
			<td ><?php echo htmlentities($row['handovers_operator']); ?></td>
			<td><?php echo __('Handovers_begintime'); ?>:</td>
			<td><?php echo !empty($row['handovers_begintime'])?datetime($row['handovers_begintime']):''; ?></td>
			<td><?php echo __('Handovers_endtime'); ?>:</td>
			<td colspan='2'><?php echo !empty($row['handovers_endtime'])?datetime($row['handovers_endtime']):''; ?></td>
		</tr>
		<tr>
			<td><?php echo __('Handovers_successor'); ?>:<?php echo htmlentities($row['handovers_successor']); ?></td>
			<td><?php echo __('交班类型'); ?>:</td>
			<td colspan='5'>
			<div class="col-xs-12 col-sm-2">
			<div class="radio">
            <label><input id="row[handovers_type]-0" name="row[handovers_type]" type="radio" value="0"  <?php if(in_array((0), is_array($row['handovers_type'])?$row['handovers_type']:explode(',',$row['handovers_type']))): ?>checked<?php endif; ?> />交下班 </label>
				<label><input id="row[handovers_type]-1" name="row[handovers_type]" type="radio" value="1" <?php if(in_array((1), is_array($row['handovers_type'])?$row['handovers_type']:explode(',',$row['handovers_type']))): ?>checked<?php endif; ?>  />交财务 </label>
   
            </div>
            </div>
            </td>
			
		
			<td colspan='2'><button type="button" class="btn btn-success btn-embossed btn-print"><?php echo __('打印'); ?></button></td>
		</tr>

	</table>	
      
 </form>  
   <div class="form-group">
         <div class="col-xs-12 col-sm-11">
           <div class="widget-body no-padding">
    			
   			<table id="table1" class="table table-striped table-bordered table-hover table-nowrap"
            	data-operate-edit="<?php echo $auth->check('financial/payment/edit'); ?>" 
               data-operate-del="<?php echo $auth->check('financial/payment/del'); ?>" 
           	  	width="100%">
         	</table>
         </div>
    		</div>
    </div>
    
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
