<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:113:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/public/../application/admin/view/work/statement/index.html";i:1608655278;s:97:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/layout/default.html";i:1602168706;s:94:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/meta.html";i:1602168706;s:96:"/media/luckywujl/data/www/admin/localhost_9008/wwwroot/application/admin/view/common/script.html";i:1602168706;}*/ ?>
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
    </div>
    <div class="form-group">
        <label class="control-label col-xs-4 col-sm-1"><?php echo __('Iodetail_plate_number'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_plate_number" data-rule="required"  class="form-control" name="row[iodetail_plate_number]" type="text">
        </div>
    
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_mototype'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_mototype"  class="form-control " readonly="readonly" name="row[iodetail_mototype]" type="text">
        </div>
    
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_iotime'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-iodetail_iotime" readonly="readonly" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[iodetail_iotime]" type="text" value="<?php echo date('Y-m-d H:i:s'); ?>">
        </div>
    
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_channel'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_channel" data-rule="required" data-source="base/channel/index" data-field = "channel" data-primary-key="channel" class="form-control selectpage" name="row[iodetail_channel]" type="text">
        </div>
        <label class="control-label col-xs-4 col-sm-1"><?php echo __('Iodetail_plate_number_a'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_plate_number_a"   class="form-control" name="row[iodetail_plate_number_a]" type="text">
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_card_code'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_card_code" class="form-control" name="row[iodetail_card_code]" type="text">
        </div>
   
        <div class="col-xs-12 col-sm-1" hidden="hidden" >
            <input id="c-iodetail_card_id" data-rule="required"  class="form-control " name="row[iodetail_card_id]" type="text" value="">
        </div>
        
        <div class="col-xs-12 col-sm-1" hidden="hidden"  >
            <input id="c-iodetail_custom_id" data-rule="required"  class="form-control " name="row[iodetail_custom_id]" type="text" value="">
        </div>
        
        <div class="col-xs-12 col-sm-1" hidden="hidden" >
            <input id="c-iodetail_custom_customtype_attribute" data-rule="required"  class="form-control " name="row[iodetail_custom_customtype_attribute]" type="text" value="">
        </div>
        
        <div class="col-xs-12 col-sm-1" hidden="hidden"  >
            <input id="c-iodetail_in_id" data-rule="required"  class="form-control " name="row[iodetail_in_id]" type="text" value="">
        </div>
        
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_custom_name'); ?>:</label>
        <div class="col-xs-12 col-sm-1" >
            <input id="c-iodetail_custom_name" readonly="readonly"  class="form-control "  type="text" value="">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_custom_address'); ?>:</label>
        <div class="col-xs-12 col-sm-2" >
            <input id="c-iodetail_custom_address" readonly="readonly"  class="form-control "  type="text" value="">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_custom_customtype'); ?>:</label>
        <div class="col-xs-12 col-sm-1" >
            <input id="c-iodetail_custom_customtype" readonly="readonly"  class="form-control "  type="text" value="">
        </div>
        
        
    </div>
    <div class="form-group">
   
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_product_id'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_product_id"  data-rule="required" data-source="base/product/index" data-field = "product_name" data-primary-key="product_ID" class="form-control selectpage" name="row[iodetail_product_id]" type="text" value="">
        </div>
    
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_weight'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_weight" class="form-control" name="row[iodetail_weight]" type="number">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_intime'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-iodetail_intime" readonly="readonly" class="form-control "  data-use-current="true" name="row[iodetail_intime]" type="text" value="">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_inweight'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_inweight" readonly="readonly" class="form-control" name="row[iodetail_inweight]" type="number">
        </div>
        
       
        
    </div>
    
    
    
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Statement_nw'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_NW" readonly="readonly" class="form-control" name="row[iodetail_NW]" type="number">
        </div>
    
    	   <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_product_price'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_product_price"  class="form-control" name="row[iodetail_product_price]" type="number">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_discount'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_discount"  class="form-control" name="row[iodetail_discount]" type="number" value="100">
        </div>
        
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_cost'); ?>:</label>
        <div class="col-xs-12 col-sm-2">
            <input id="c-iodetail_cost" class="form-control" name="row[iodetail_cost]" type="number">
        </div>
        
       
        
        
    
    </div>
    
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_checker'); ?>:</label>
        <div class="col-xs-12 col-sm-1">
            <input id="c-iodetail_checker"  data-source="auth/admin/index" data-field=nickname data-primary-key="nickname" class="form-control selectpage" name="row[iodetail_checker]" type="text">
        </div>
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('Iodetail_remark'); ?>:</label>
        <div class="col-xs-12 col-sm-6">
            <input id="c-iodetail_remark" data-source="base/remark/index" data-field="remark" data-primary-key="remark" class="form-control selectpage" name="row[iodetail_remark]" type="text">
        </div>
    	   
       
    </div>
        

  
    
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-3"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="button" class="btn btn-info btn-embossed btn-getinfo"><?php echo __('获取信息'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
            <button type="button" class="btn btn-info btn-embossed btn-accept"><?php echo __('确定'); ?></button>
            
        </div>
    </div>
   <hr align="left" size="1" width="90%" >
</form>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('work/statement/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                        <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('work/statement/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('work/statement/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('work/statement/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>
								<a href="javascript:;" class="btn btn-info btn-print btn-disabled disabled <?php echo $auth->check('work/statement/print')?'':'hide'; ?>" title="<?php echo __('补打票据'); ?>" ><i class="fa fa-leaf"></i> <?php echo __('补打票据'); ?></a>
                        <div class="dropdown btn-group <?php echo $auth->check('work/statement/multi')?'':'hide'; ?>">
                            <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=normal"><i class="fa fa-eye"></i> <?php echo __('Set to normal'); ?></a></li>
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=hidden"><i class="fa fa-eye-slash"></i> <?php echo __('Set to hidden'); ?></a></li>
                            </ul>
                        </div>

                        
                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('work/statement/edit'); ?>" 
                           data-operate-del="<?php echo $auth->check('work/statement/del'); ?>" 
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
