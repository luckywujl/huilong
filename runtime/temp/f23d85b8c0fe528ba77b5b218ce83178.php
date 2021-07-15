<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:117:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/public/../application/admin/view/financial/recharge/index.html";i:1626355279;s:97:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1624286200;s:94:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1624286200;s:96:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1624286200;}*/ ?>
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
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Card_encode'); ?>:</label>
       
        <div class="col-xs-12 col-sm-2" >   
            <input id="c-card_encode"  class="form-control " placeholder="输入卡号或刷卡" name="card_encode" value="">              
            
        </div>
        
        
        <div class="col-xs-12 col-sm-2" hidden="hidden">
            <input id="c-charge_object"  class="form-control" name="row[charge_object]" type="text" value="客户充值">
        </div>
  
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Charge_custom_id'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-charge_custom_id" data-rule="required" data-source="custom/custom/index" data-field="custom_name" data-primary-key="custom_id" class="form-control selectpage" name="row[charge_custom_id]" type="text" value="">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Custom_customtype'); ?>:</label>  
        <div class="col-xs-12 col-sm-2" >   
            <input id="c-custom_customtype"  class="form-control "  name="row[custom_customtype]" value="">              
            
        </div>
        
        
     </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Custom_account'); ?>:</label>  
        <div class="col-xs-12 col-sm-1" >   
            <input id="c-custom_account" readonly="readonly"  class="form-control "  name="row[custom_account]" type="number" value="">              
            
        </div>
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Charge_principal'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-charge_principal" data-rule="required" class="form-control" name="row[charge_principal]" type="number">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Charge_subsidy'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-charge_subsidy" data-rule="required" class="form-control" name="row[charge_subsidy]" type="number" value="0">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Charge_remark'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-charge_remark"  class="form-control" name="row[charge_remark]" type="text">
        </div>
        </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"></label>
        <div class="col-xs-12 col-sm-3">
            
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
            <label class="control-label col-xs-12 col-sm-1"></label>
            <button type="button" class="btn btn-info btn-embossed btn-pay"><?php echo __('PAY'); ?></button>
        </div>
    </div>    
</form>
    
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <a href="javascript:;" class="btn btn-info btn-print btn-disabled disabled <?php echo $auth->check('financial/account/print')?'':'hide'; ?>" title="<?php echo __('补打票据'); ?>" ><i class="fa fa-leaf"></i> <?php echo __('补打票据'); ?></a>
                        

                        
                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('financial/account/edit'); ?>" 
                           data-operate-del="<?php echo $auth->check('financial/account/del'); ?>" 
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
