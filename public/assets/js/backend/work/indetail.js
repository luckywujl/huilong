define(['jquery', 'bootstrap', 'backend', 'table', 'form','printing','selectpage'], function ($, undefined, Backend, Table,Form,Printing) {


    
    var Controller = {
        index: function () {
        	
        	
        	
        
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'work/indetail/index' + location.search,
                    add_url: 'work/indetail/add',
                    edit_url: 'work/indetail/edit',
                    del_url: 'work/indetail/del',
                    multi_url: 'work/indetail/multi',
                    import_url: 'work/indetail/import',
                    table: 'work_iodetail',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'iodetail_ID',
                sortName: 'iodetail_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'iodetail_ID', title: __('Iodetail_id')},
                        {field: 'iodetail_code', title: __('Iodetail_code')},
                        {field: 'iodetail_plate_number', title: __('Iodetail_plate_number'), operate: 'LIKE'},
                        {field: 'iodetail_mototype', title: __('Iodetail_mototype'), operate: 'LIKE'},
                        {field: 'iodetail_iotype', title: __('Iodetail_iotype'), searchList: {"0":__('Iodetail_iotype 0'),"1":__('Iodetail_iotype 1')}, formatter: Table.api.formatter.normal},
                        {field: 'iodetail_iotime', title: __('Iodetail_iotime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'iodetail_channel', title: __('Iodetail_channel'), operate: 'LIKE'},
                        {field: 'baseproduct.product_code', title: __('Baseproduct.product_code'), operate: 'LIKE'},
                        {field: 'baseproduct.product_name', title: __('Baseproduct.product_name'), operate: 'LIKE'},
                        {field: 'baseproduct.product_unit', title: __('Baseproduct.product_unit'), operate: 'LIKE'},
                        
                        //{field: 'iodetail_card_id', title: __('Iodetail_card_id'), operate: 'LIKE'},
                        
                        //{field: 'iodetail_custom_id', title: __('Iodetail_custom_id')},
                        //{field: 'iodetail_product_id', title: __('Iodetail_product_id')},
                        {field: 'iodetail_weight', title: __('Iodetail_weight'), operate:'BETWEEN'},
                        {field: 'iodetail_checker', title: __('Iodetail_checker'), operate: 'LIKE'},
                        {field: 'iodetail_operator', title: __('Iodetail_operator'), operate: 'LIKE'},
                        {field: 'iodetail_remark', title: __('Iodetail_remark'), operate: 'LIKE'},
                        {field: 'iodetail_status', title: __('Iodetail_status'), searchList: {"0":__('Iodetail_status 0'),"1":__('Iodetail_status 1'),"2":__('Iodetail_status 2')}, formatter: Table.api.formatter.status},
                        //{field: 'iodetail_statement_ID', title: __('Iodetail_statement_id')},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'iodetail_card_code', title: __('Iodetail_card_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype')},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea')},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        //{field: 'baseproduct.product_ID', title: __('Baseproduct.product_id')},
                        //{field: 'baseproduct.product_producttype_ID', title: __('Baseproduct.product_producttype_id')},
                        //{field: 'baseproduct.company_id', title: __('Baseproduct.company_id'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
            
            //获取信息
				$(document).on("click",".btn-getinfo",function () {
					getchannelinfo();
 		   	 
				});
            // 离场收费
				$(document).on("click", ".btn-accept", function(){
				    $("#add-form").attr("action","work/indetail/index").submit(); 
				});
				
				
				//进场收款
				$(document).on("click",".btn-statement",function () {
					//
					if ($("#c-iodetail_cost").val()!=="")  {
						if ($("#c-iodetail_custom_id").val()!=="") {
				    //弹窗显示支付方式
         	  Fast.api.open('base/pay/pay?amount='+$("#c-iodetail_cost").val(),'付款',{//?card_code=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype, __('Choose'), {
	           area:['60%', '70%'],
		           callback: function (data) {	
		           //alert(data);
		           	url = "work/indetail/add?payment="+data;
		           	$("#add-form").attr("action",url).submit(); 
	       	    },function (data) {
	       	    	
	       	    }
	            });
	         }
	         }
	         });

				
				//打印
				
				$(document).on("click", ".btn-print", function(index){
					var ids = Table.api.selectedids(table);
				    $.ajax({
                        url: "work/indetail/print?iodetail_id="+ids,
                        type: 'post',
                        dataType: 'json',
                       // data: {statement_id:5},
                        success: function (ret) {
                            var options ={
                                templateCode:'rhnpjc',
                                data:ret.data,
                            };
                            Printing.api.printTemplate(options);
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });		
				});
				
				//选择车型 
				$("#c-iodetail_mototype").data("eSelect", function(){
   			 //后续操作
   			 Fast.api.ajax({
        			url:'base/mototype/getmototypetare',        													     
             	data:{mototype:$("#c-iodetail_mototype").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_tare").val(data.mototype_tare);
         	   count();
         		console.info(data);     													      
               return false;    															
           	},function(data){
               //失败的回调 
           		//return false;	
               }											  		 		  
 			   	);
				});
				
				//选择货品名称
				$("#c-iodetail_product_id").data("eSelect", function(){
   			 //后续操作
   			 if ($("#c-iodetail_custom_customtype").val()=="") {
   			   Toastr.error('请先选择客户');
   			   exit;
   			 }
   			 
   			 Fast.api.ajax({
        			url:'base/productprice/getproductprice',        													     
             	data:{product_id:$("#c-iodetail_product_id").val(),custom_type:$("#c-iodetail_custom_customtype").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_price").val(data.productprice_price);
         	   count();
         		console.info(data);     													      
               //return false;    															
           	},function(data){
               //失败的回调 
           		//return false;	
               }											  		 		  
 			   	);
   			 
				});
				
				
		
				
				
				//输入卡号或内码，按回车键，到库里查找卡信息
				$("#c-iodetail_card_code").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
				//获取车牌识别结果和称重结果
  				 getchannelinfo();
 			   	//再更新客户信息
				Fast.api.ajax({
        			url:'custom/card/getcardinfo',        													     
             	data:{card_info:$("#c-iodetail_card_code").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_custom_id").val(data.custom_id);
         	   $("#c-iodetail_custom_name").val(data.custom_name);
					$("#c-iodetail_custom_address").val(data.custom_address);
					$("#c-iodetail_custom_customtype").val(data.custom_customtype);
					$("#c-iodetail_card_code").val(data.card_code);
					$("#c-iodetail_card_id").val(data.card_id);
         	   
         		console.info(data);     													      
               return false;    															
           	},function(data){
               //失败的回调 
					$("#c-iodetail_custom_id").val('');
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_card_code").val('');
           		//return false;	
            }											  		 		  
 				);
				}
				});
				
				$("#c-iodetail_plate_number").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
					
				}
			  });
			  
			  $("#c-iodetail_tare").bind("keyup",function (event) {
				count();
			  });
			  $("#c-iodetail_price").bind("keyup",function (event) {
				count();
			  });
			  
			  $("#c-iodetail_NW").bind("keyup",function (event) {
				$("#c-iodetail_cost").val(($("#c-iodetail_NW").val()*$("#c-iodetail_price").val()).toFixed(0));
			  });
			  
				Controller.api.bindevent();
				
				//获取通道信息
				function getchannelinfo() {
					//通道信息为空则提示错误并退出
					if ($("#c-iodetail_channel").val()=="") {
					 Toastr.error("通道信息为空");
					 exit;
					}
				 //获取车牌识别结果和称重结果
  				 Fast.api.ajax({
        			url:'base/channel/getinchannelinfo',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_plate_number").val(data.channel_plate_number);
         	   $("#c-iodetail_GW").val(data.channel_weight);
         	   $("#c-iodetail_tare").val(data.moto_tare); 
         	   $("#c-iodetail_mototype").val(data.moto_type);
         	   $("#c-iodetail_mototype").selectPageClear();
         	   $("#c-iodetail_mototype").val(data.moto_type);
         	   $("#c-iodetail_mototype").selectPageRefresh();
         	   count();
         		console.info(data);     													      
               return false;    															
           	},function(data){	
               }											  		 		  
 			   	);
				}
				
				//计算净重及实际费用
				function count() {
					//计算净重
					$("#c-iodetail_NW").val($("#c-iodetail_GW").val()-$("#c-iodetail_tare").val());
					$("#c-iodetail_cost").val(($("#c-iodetail_NW").val()*$("#c-iodetail_price").val()).toFixed(0));
				}
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        list: function () {
        	 Controller.api.bindevent();
        	// 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'work/indetail/list' + location.search,
                   
                    table: 'work_iodetail',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'iodetail_ID',
                sortName: 'iodetail_ID',
                singleSelect: true, //是否启用单选
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'iodetail_ID', title: __('Iodetail_id')},
                        {field: 'iodetail_code', title: __('Iodetail_code')},
                        {field: 'iodetail_plate_number', title: __('Iodetail_plate_number'), operate: 'LIKE'},
                        {field: 'iodetail_mototype', title: __('Iodetail_mototype'), operate: 'LIKE'},
                        //{field: 'iodetail_iotype', title: __('Iodetail_iotype'), searchList: {"0":__('Iodetail_iotype 0'),"1":__('Iodetail_iotype 1')}, formatter: Table.api.formatter.normal},
                        {field: 'iodetail_iotime', title: __('Iodetail_iotime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        //{field: 'iodetail_channel', title: __('Iodetail_channel'), operate: 'LIKE'},
                        {field: 'iodetail_card_code', title: __('Iodetail_card_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},                   
                        //{field: 'baseproduct.product_unit', title: __('Baseproduct.product_unit'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype')},
                        {field: 'baseproduct.product_code', title: __('Baseproduct.product_code'), operate: 'LIKE'},
                        {field: 'baseproduct.product_name', title: __('Baseproduct.product_name'), operate: 'LIKE'},
                        
                        //{field: 'iodetail_card_id', title: __('Iodetail_card_id'), operate: 'LIKE'},
                        
                        //{field: 'iodetail_custom_id', title: __('Iodetail_custom_id')},
                        //{field: 'iodetail_product_id', title: __('Iodetail_product_id')},
                        {field: 'iodetail_weight', title: __('Iodetail_weight'), operate:'BETWEEN'},
                        {field: 'iodetail_checker', title: __('Iodetail_checker'), operate: 'LIKE'},
                        {field: 'iodetail_operator', title: __('Iodetail_operator'), operate: 'LIKE'},
                        {field: 'iodetail_remark', title: __('Iodetail_remark'), operate: 'LIKE'},
                        {field: 'iodetail_status', title: __('Iodetail_status'), searchList: {"0":__('Iodetail_status 0'),"1":__('Iodetail_status 1'),"2":__('Iodetail_status 2')}, formatter: Table.api.formatter.status},
                        //{field: 'iodetail_statement_ID', title: __('Iodetail_statement_id')},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea')},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        //{field: 'baseproduct.product_ID', title: __('Baseproduct.product_id')},
                        //{field: 'baseproduct.product_producttype_ID', title: __('Baseproduct.product_producttype_id')},
                        //{field: 'baseproduct.company_id', title: __('Baseproduct.company_id'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
            //关闭时执行
            parent.window.$(".layui-layer-iframe").find(".layui-layer-close").on('click',function () {
                    var ids = Table.api.selectedids(table);   //获取选中的id，获取到的是个数组
                    Fast.api.close(ids); //往父窗口回调参数 
                  
             });
            
        },
        api: {
           bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function(data, ret){
					//如果我们需要在提交表单成功后做跳转，可以在此使用location.href="链接";进行跳转
					//Toastr.success("成功");
					//$("#c-iodetail_product_id").selectPageClear();
					$("#c-iodetail_remark").selectPageClear();
					$("#c-iodetail_plate_number").val('');//清空车牌信息
					$("#c-iodetail_card_code").val('');//清空卡号输入框
					$("#c-iodetail_custom_id").val('');//清空会员信息
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_weight").val('');
					$("#c-iodetail_remark").val('');
					$("#c-iodetail_mototype").val('');
         	   $("#c-iodetail_mototype").selectPageClear();
         	   $("#c-iodetail_tare").val('');
         	   $("#c-iodetail_GW").val('');
         	   $("#c-iodetail_cost").val('');
         	   $("#c-iodetail_price").val('');
         	   $("#c-iodetail_NW").val('');
         	   $("#c-iodetail_product_id").val('');
         	   $("#c-iodetail_product_id").selectPageClear();
         	   $("#c-iodetail_password").val('');
         	   
					//$("#c-iodetail_product_id").val('');
					//加入判断语句，打印不同的报表
					if (data.type) {
					//发送短信
					//send_sms('[汇隆果品]尊敬的'+data.n+'，您本次缴费'+data.a+'元，账户余额为'+data.b+'元。',data.m);
					//Fast.api.ajax('http://api.smsbao.com/sms?u=luckywujl&p=635fcbe5a0f9a1d9bb83ca8392d0c827&m='+data.m+'&c=[汇隆果品]尊敬的'+data.n+'，您本次缴费'+data.a+'元，账户余额为'+data.b+'元。');
					//打印单据
					$.ajax({
                        url: "work/statement/print",
                        type: 'post',
                        dataType: 'json',
                        data: {statement_id:data.statement_id},
                        success: function (ret) {
                            var options ={
                                templateCode:'rhnp',
                                data:ret.data,
                            };
                            Printing.api.printTemplate(options);
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });
					}else {
					//打印单据
					$.ajax({
                        url: "work/indetail/print",
                        type: 'post',
                        dataType: 'json',
                        data: {iodetail_id:data.iodetail_id},
                        success: function (ret) {
                            var options ={
                                templateCode:'rhnpjc',
                                data:ret.data,
                            };
                            Printing.api.printTemplate(options);
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });
                }
                //清空车牌信息
					Fast.api.ajax({
        			url:'base/channel/clearplate',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
            	 }, 
              	 function () { //success  
             		//console.info(data);     													      
                  return false;    															
               	},function(){
                 //失败的回调 
           		  return false;	
               }	);    
					//刷新表格
   				$("#table").bootstrapTable('refresh');
					}, function(data, ret){
  						Toastr.success("失败");
					}, function(success, error){

					//bindevent的第三个参数为提交前的回调
					//如果我们需要在表单提交前做一些数据处理，则可以在此方法处理
					//注意如果我们需要阻止表单，可以在此使用return false;即可
					//如果我们处理完成需要再次提交表单则可以使用submit提交,如下
					//Form.api.submit(this, success, error);
					//return false;
					});
         
       	 }
       	 }
    };
    return Controller;
});