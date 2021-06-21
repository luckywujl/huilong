<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:104:"/home/www/admin/localhost_9008/wwwroot/public/../application/admin/view/report/statementcount/index.html";i:1621005368;s:81:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1621005368;s:78:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1621005368;s:80:"/home/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1621005368;}*/ ?>
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
        <ul class="nav nav-tabs" data-field="statement_status">
            <li class="<?php echo \think\Request::instance()->get('statement_status') === null ? 'active' : ''; ?>"><a href="#t-all" data-value="" data-toggle="tab"><?php echo __('All'); ?></a></li>
            <?php if(is_array($statementStatusList) || $statementStatusList instanceof \think\Collection || $statementStatusList instanceof \think\Paginator): if( count($statementStatusList)==0 ) : echo "" ;else: foreach($statementStatusList as $key=>$vo): ?>
            <li class="<?php echo \think\Request::instance()->get('statement_status') === (string)$key ? 'active' : ''; ?>"><a href="#t-<?php echo $key; ?>" data-value="<?php echo $key; ?>" data-toggle="tab"><?php echo $vo; ?></a></li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>


    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        

                        
                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('financial/statementcount/edit'); ?>" 
                           data-operate-del="<?php echo $auth->check('financial/statementcount/del'); ?>" 
                           width="100%">
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script id="customformtpl" type="text/html">
    <!--form表单必须添加form-commsearch这个类-->
    <form action="" class="form-commonsearch">
        <div style="border-radius:2px;margin-bottom:10px;background:#f5f5f5;padding:15px 20px;">
           
            <div class="row">
                
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label">结算时间</label>
                        <input type="hidden" class="operate" data-name="statement_date" value="RANGE"/>
                        <div>
                        <input id="statement_date" type="text"  class="form-control datetimerange" name="statement_date" value="<?php echo date("Y-m-d 00:00:00"); ?> - <?php echo date("Y-m-d 23:59:59"); ?>"/>
                        </div>
                        <input type="hidden" class="operate" data-name="statement_status" value="="/>
                        <div>
                        <input id = "statement_status" type="hidden"  class="form-control " name="statement_status" value=""/>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"></label>
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="submit" class="btn btn-success btn-block" value="提交"/>
                            </div>
                            <div class="col-xs-6">
                                <input type="reset" class="btn btn-primary btn-block" value="重置"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
