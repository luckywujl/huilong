define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
        	Controller.api.bindevent();
            // 初始化表格参数配置
            Table.api.init();

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'work/outdetail/refresh' + location.search+'&iodetail_custom_id='+2,
                extend: {
                    index_url: 'work/outdetail/refresh' + location.search+'&iodetail_custom_id='+2,
                    //add_url: 'work/outdetail/add',
                   // edit_url: 'work/outdetail/edit',
                   // del_url: 'work/outdetail/del',
                   // multi_url: 'work/outdetail/multi',
                   // import_url: 'work/outdetail/import',
                    table: 'work_iodetail',
                },
                toolbar: '#toolbar',
                pk: 'iodetail_ID',
                sortName: 'iodetail_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'iodetail_ID', title: __('Iodetail_id')},
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
            
            // 初始化表格参数配置
            Table.api.init();

            var table2 = $("#table2");

            // 初始化表格
            table2.bootstrapTable({
                url: 'work/outdetail/index' + location.search+'&iodetail_custom_id=2',
                extend: {
                    index_url: 'work/outdetail/index' + location.search+'&iodetail_custom_id=2',
                    //add_url: 'work/outdetail/add',
                   // edit_url: 'work/outdetail/edit',
                   // del_url: 'work/outdetail/del',
                   // multi_url: 'work/outdetail/multi',
                   // import_url: 'work/outdetail/import',
                    table: 'work_iodetail',
                },
                toolbar: '#toolbar2',
                pk: 'iodetail_ID',
                sortName: 'iodetail_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'iodetail_ID', title: __('Iodetail_id')},
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
            Table.api.bindevent(table2);
            //提交
				$(document).on("click", ".btn-accept", function(){
				    $("#add-form").attr("action","work/outdetail/index").submit(); 
				});
				
				//获取信息
				$(document).on("click",".btn-getinfo",function () {
				 //获取车牌识别结果和称重结果
  				 Fast.api.ajax({
        			url:'base/channel/getchannelinfo',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_plate_number").val(data.channel_plate_number);
         	   $("#c-iodetail_weight").val(data.channel_weight);
         		console.info(data);     													      
               return false;    															
           	},function(data){
               //失败的回调 
					
           		//return false;	
               }											  		 		  
 			   	);
 		   	 
				});
				
				//定时读取服务器端的重量数据和车牌信息并更新时间
				setInterval(function(){
  				  //$(".btn-refresh").trigger("click");
  				 //更新页面时间
  				 var myDate = new Date();
  				 $("#c-iodetail_iotime").val(myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+" "+myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds());
  				 //获取车牌识别结果和称重结果
  				 //var plate_number = $("#c-iodetail_plate_number").val();
  				 //var weight = $("#c-iodetail_weight").val();
  				 //if (plate_number==''||weight=='') {
  				 //Fast.api.ajax({
        		//	url:'base/channel/getchannelinfo',        													     
             //	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 //}, 
         	 //function (data,ret) { //success 用于接收主表控制器发过来的数据
         	 //  $("#c-iodetail_plate_number").val(data.channel_plate_number);
         	 //  $("#c-iodetail_weight").val(data.channel_weight);
         	//	console.info(data);     													      
             //  return false;    															
           	//},function(data){
               //失败的回调 
					
           		//return false;	
              // }											  		 		  
 			   //	);
 		   	//} 
				}, 1000);
				
				//输入卡号或内码，按回车键，到库里查找卡信息
				$("#c-iodetail_card_code").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
				//alert('您输入的是');
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
            
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
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
					//$("#c-iodetail_product_id").val('');
					
					
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