<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:102:"/home/www/admin/localhost_9008/wwwroot/public/../application/admin/view/financial/statement/index.html";i:1621005368;s:81:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1621005368;s:78:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1621005368;s:80:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1621005368;}*/ ?>
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
    
    <div class="panel-heading">
        <?php echo build_heading(null,FALSE); ?>
    </div>


    <div class="panel-body">
    <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('客户信息'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-custom_code"  class="form-control" placeholder="请输入客户名称、卡号或刷卡" name="custom_code" type="text">
        </div>
  
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('结算单号'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-statement_code"  class="form-control" placeholder="请输入结算单号" name="statement_code" type="text">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Statement_outtime'); ?>:</label>
        <div class="col-xs-12 col-sm-3">
            <input id="c-statement_outtime" type="text"  class="form-control datetimerange" name="statement_outtime" value="<?php echo date("Y-m-d 00:00:00"); ?> - <?php echo date("Y-m-d 23:59:59"); ?>"/>
            
        </div>
    </div>
   
    <div class="form-group layer-footer" hidden="hidden">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="button" class="btn btn-info btn-embossed btn-accept"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('financial/statement/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                        <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('financial/statement/edit')?'':'hide'; ?>" title="<?php echo __('支付'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('支付'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('financial/statement/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('financial/statement/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>
                        <a href="javascript:;" class="btn btn-info btn-repay btn-disabled disabled <?php echo $auth->check('financial/statement/repay')?'':'hide'; ?>" title="<?php echo __('反结算'); ?>" ><i class="fa fa-leaf"></i> <?php echo __('反结算'); ?></a>
                        
                        <div class="dropdown btn-group <?php echo $auth->check('financial/statement/multi')?'':'hide'; ?>">
                            <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=normal"><i class="fa fa-eye"></i> <?php echo __('Set to normal'); ?></a></li>
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=hidden"><i class="fa fa-eye-slash"></i> <?php echo __('Set to hidden'); ?></a></li>
                            </ul>
                            
                        </div>
                     
                    </div>
                  
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('financial/statement/edit'); ?>" 
                           data-operate-del="<?php echo $auth->check('financial/statement/del'); ?>" 
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
