define(['jquery', 'bootstrap', 'backend', 'table', 'form','printing'], function ($, undefined, Backend, Table, Form,Printing) {

    var Controller = {
        index: function () {
        	Controller.api.bindevent();
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'work/statement/index' + location.search,
                    add_url: 'work/statement/add',
                    edit_url: 'work/statement/edit',
                    del_url: 'work/statement/del',
                    multi_url: 'work/statement/multi',
                    import_url: 'work/statement/import',
                    table: 'financial_statement',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'statement_id',
                sortName: 'statement_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'statement_id', title: __('Statement_id')},
                        {field: 'operate', title: __('Operate'), table: table,events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'statement_code', title: __('Statement_code')},
                        {field: 'statement_date', title: __('Statement_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statement_plate_number', title: __('Statement_plate_number'), operate: 'LIKE'},
                        {field: 'statement_mototype', title: __('Statement_mototype'), operate: 'LIKE'},
                        //{field: 'statement_product_id', title: __('Statement_product_id')},
                        //{field: 'statement_custom_id', title: __('Statement_custom_id')},
                        {field: 'statement_customtype', title: __('Statement_customtype'), operate: 'LIKE'},
                        {field: 'statement_GW', title: __('Statement_gw'), operate:'BETWEEN'},
                        {field: 'statement_tare', title: __('Statement_tare'), operate:'BETWEEN'},
                        {field: 'statement_NW', title: __('Statement_nw'), operate:'BETWEEN'},
                        {field: 'baseproduct.product_unit', title: __('Baseproduct.product_unit'), operate: 'LIKE'},
                        {field: 'statement_product_price', title: __('Statement_product_price'), operate:'BETWEEN'},
                        {field: 'statement_discount', title: __('Statement_discount'), operate:'BETWEEN'},
                        {field: 'statement_cost', title: __('Statement_cost'), operate:'BETWEEN'},
                        {field: 'statement_intime', title: __('Statement_intime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statement_outtime', title: __('Statement_outtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statement_remark', title: __('Statement_remark'), operate: 'LIKE'},
                        //{field: 'statement_indetail_id', title: __('Statement_indetail_id')},
                        //{field: 'statement_outdetail_id', title: __('Statement_outdetail_id')},
                        {field: 'statement_checker', title: __('Statement_checker'), operate: 'LIKE'},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype'), operate: 'LIKE'},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea'), operate: 'LIKE'},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        //{field: 'baseproduct.product_ID', title: __('Baseproduct.product_id')},
                        {field: 'baseproduct.product_code', title: __('Baseproduct.product_code'), operate: 'LIKE'},
                        {field: 'baseproduct.product_name', title: __('Baseproduct.product_name'), operate: 'LIKE'},
                        //{field: 'baseproduct.product_producttype_ID', title: __('Baseproduct.product_producttype_id')},
                        //{field: 'baseproduct.company_id', title: __('Baseproduct.company_id'), operate: 'LIKE'},
                        
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            //提交
				$(document).on("click", ".btn-accept", function(){
				    $("#add-form").attr("action","work/outdetail/index").submit(); 
				});
				//打印
				
				$(document).on("click", ".btn-print", function(){
					var ids = Table.api.selectedids(table);
				    $.ajax({
                        url: "work/statement/print?statement_id="+ids,
                        type: 'post',
                        dataType: 'json',
                       // data: {statement_id:5},
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
				});
				
				//获取信息
				$(document).on("click",".btn-getinfo",function () {
				 //获取车牌识别结果和称重结果
  				 Fast.api.ajax({
        			url:'base/channel/getinchannelinfo',        //仅获取车牌号及重量信息，不再去查找进场记录													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-iodetail_plate_number_a").val(data.channel_plate_number);
         	   $("#c-iodetail_weight").val(data.channel_weight);
         	   //$("#c-iodetail_inweight").val(data.iodetail_weight);
         	   ///$("#c-iodetail_mototype").val(data.iodetail_mototype);
         	   //$("#c-iodetail_product_id").val(data.iodetail_product_id);
         	   //$("#c-iodetail_product_id").selectPageRefresh();
         	   //$("#c-iodetail_custom_id").val(data.iodetail_custom_id);
         	   //$("#c-iodetail_custom_customtype_attribute").val(data.iodetail_custom_customtype_attribute);
         	   //$("#c-iodetail_custom_name").val(data.iodetail_custom_name);
					//$("#c-iodetail_custom_address").val(data.iodetail_custom_address);
					//$("#c-iodetail_custom_customtype").val(data.iodetail_custom_customtype);
					//$("#c-iodetail_card_code").val(data.iodetail_card_code);  
					//$("#c-iodetail_card_id").val(data.iodetail_card_id); 
					//$("#c-iodetail_in_id").val(data.iodetail_ID);
					//$("#c-iodetail_checker").val(data.iodetail_checker);
					//$("#c-iodetail_checker").selectPageRefresh();
					
					//var inDate = new Date(data.iodetail_iotime*1000);
         	   //$("#c-iodetail_intime").val(data.iodetail_iotime);
         	   //$("#c-iodetail_intime").val(inDate.getFullYear()+'-'+(inDate.getMonth()+1)+'-'+inDate.getDate()+" "+inDate.getHours()+':'+inDate.getMinutes()+':'+inDate.getSeconds());
         		console.info(data);  
         		count(); //计算净重及金额													      
               return false;    															
           	},function(data){
               //失败的回调 
					$("#c-iodetail_plate_number_a").val('');
         	   $("#c-iodetail_weight").val('');
         	   //$("#c-iodetail_inweight").val('');
         	   //$("#c-iodetail_inweight").val('');
         	   //$("#c-iodetail_mototype").val('');
         	   //$("#c-iodetail_product_id").val('');
         	   //$("#c-iodetail_product_id").selectPageRefresh();
         	   
         	   //$("#c-iodetail_custom_name").val('');
					//$("#c-iodetail_custom_address").val('');
					//$("#c-iodetail_custom_customtype").val('');
					//$("#c-iodetail_card_code").val('');
					//$("#c-iodetail_card_id").val(''); 
					//$("#c-iodetail_in_id").val('');
					//$("#c-iodetail_custom_id").val('');
         	   //$("#c-iodetail_custom_name").val('');
					//$("#c-iodetail_custom_address").val('');
					//$("#c-iodetail_custom_customtype").val('');
           		//return false;	
               }											  		 		  
 			   	);
 		   	 
				});
				
				//定时更新时间
				setInterval(function(){
  				  //$(".btn-refresh").trigger("click");
  				 //更新页面时间
  				 var myDate = new Date();
  				 $("#c-iodetail_iotime").val(myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+" "+myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds());
			     }, 1000);
			     
			   //手动输入车牌号码，从入场记录中读取入场信息
			   $("#c-iodetail_plate_number").bind("keypress",function (event) {
			   if (event.keyCode == '13')
			   {
			   	//获取车牌识别结果和称重结果
  				 Fast.api.ajax({
        			url:'base/channel/getinchannelinfo',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	  // $("#c-iodetail_plate_number").val(data.channel_plate_number);
         	   $("#c-iodetail_weight").val(data.channel_weight);
         		console.info(data);     													      
               return false;    															
           	},function(data){
               //失败的回调 
					
           		//return false;	
               }											  		 		  
 			   	);
 			   	//再获取进场记录
			   /**	
			    Fast.api.ajax({
			    	url:'work/indetail/getindetailinfobyplate',
			    	data:{iodetail_plate_number:$("#c-iodetail_plate_number").val()}//将手动输入的车牌号码以POST的方式发送到work/indetail中查找入场记录
			    },
			    function (data,ret) {//success成功后接收发回的数据，并赋值给相应的页面控件
			      $("#c-iodetail_plate_number").val(data.iodetail_plate_number);
         	   $("#c-iodetail_inweight").val(data.iodetail_weight);
         	   $("#c-iodetail_mototype").val(data.iodetail_mototype);
         	   $("#c-iodetail_product_id").val(data.iodetail_product_id);
         	   $("#c-iodetail_product_id").selectPageRefresh();
         	   $("#c-iodetail_custom_id").val(data.iodetail_custom_id);
         	   $("#c-iodetail_custom_customtype_attribute").val(data.iodetail_custom_customtype_attribute);
         	   $("#c-iodetail_custom_name").val(data.iodetail_custom_name);
					$("#c-iodetail_custom_address").val(data.iodetail_custom_address);
					$("#c-iodetail_custom_customtype").val(data.iodetail_custom_customtype);
					$("#c-iodetail_card_code").val(data.iodetail_card_code);  
					$("#c-iodetail_card_id").val(data.iodetail_card_id); 
					$("#c-iodetail_in_id").val(data.iodetail_ID);
					$("#c-iodetail_checker").val(data.iodetail_checker);
					$("#c-iodetail_checker").selectPageRefresh();
					var inDate = new Date(data.iodetail_iotime*1000);
         	   //$("#c-iodetail_intime").val(data.iodetail_iotime);
         	   $("#c-iodetail_intime").val(inDate.getFullYear()+'-'+(inDate.getMonth()+1)+'-'+inDate.getDate()+" "+inDate.getHours()+':'+inDate.getMinutes()+':'+inDate.getSeconds());
         		console.info(data); 
         		count(); //计算净重及金额		  													      
               return false;    															
           	},function(data){
               //失败的回调 
					$("#c-iodetail_plate_number").val('');
         	   $("#c-iodetail_inweight").val('');
         	   $("#c-iodetail_mototype").val('');
         	   $("#c-iodetail_product_id").val('');
         	   $("#c-iodetail_product_id").selectPageRefresh();
         	   
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_card_code").val('');
					$("#c-iodetail_card_id").val(''); 
					$("#c-iodetail_in_id").val('');
					$("#c-iodetail_custom_customtype_attribute").val('');
					
					$("#c-iodetail_custom_id").val('');
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
           		//return false;	
			    }
			    );
			    */
			   }
			   });  
				
				//输入卡号或内码，按回车键，到库里查找卡信息
				$("#c-iodetail_card_code").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
				//获取车牌识别结果和称重结果
  				 Fast.api.ajax({
        			url:'base/channel/getinchannelinfo',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   //$("#c-iodetail_plate_number").val(data.channel_plate_number);
         	   $("#c-iodetail_plate_number_a").val(data.channel_plate_number);
         	   $("#c-iodetail_weight").val(data.channel_weight);
         		console.info(data);     													      
               return false;    															
           	},function(data){
               //失败的回调 
					alert("取通道信息失败");
           		return false;	
               }											  		 		  
 			   	);
 			   	//再获取进场记录
				Fast.api.ajax({
        			url:'work/indetail/getindetailinfobycard',        													     
             	data:{card_info:$("#c-iodetail_card_code").val()} //再将收到的card_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	 if (data.recordnumber  <'2') {
         	   $("#c-iodetail_plate_number").val(data.data[0].iodetail_plate_number);
         	   $("#c-iodetail_inweight").val(data.data[0].iodetail_weight);
         	   $("#c-iodetail_mototype").val(data.data[0].iodetail_mototype);
         	   $("#c-iodetail_checker").val(data.data[0].iodetail_checker);
					$("#c-iodetail_checker").selectPageRefresh();
         	   $("#c-iodetail_product_id").val(data.data[0].iodetail_product_id);
         	   
         	   $("#c-iodetail_product_id").selectPageRefresh();
         	   $("#c-iodetail_custom_id").val(data.data[0].iodetail_custom_id);
         	   $("#c-iodetail_custom_customtype_attribute").val(data.customtype.customtype_attribute);
         	   $("#c-iodetail_custom_name").val(data.custom.custom_name);
					$("#c-iodetail_custom_address").val(data.custom.custom_address);
					$("#c-iodetail_custom_customtype").val(data.custom.custom_customtype);
					$("#c-iodetail_card_code").val(data.data[0].iodetail_card_code);  
					$("#c-iodetail_card_id").val(data.data[0].iodetail_card_id); 
					$("#c-iodetail_in_id").val(data.data[0].iodetail_ID);
					
					var inDate = new Date(data.data[0].iodetail_iotime*1000);
         	   //$("#c-iodetail_intime").val(data.iodetail_iotime);
         	   $("#c-iodetail_intime").val(inDate.getFullYear()+'-'+(inDate.getMonth()+1)+'-'+inDate.getDate()+" "+inDate.getHours()+':'+inDate.getMinutes()+':'+inDate.getSeconds());
         		
         	   
         		console.info(data); 
         		count(); //计算净重及金额		
         	 } else { //如果一卡多车，弹窗提示
         	   //alert("该卡有多辆车入场信息");
         	   //弹窗显示该客户的所在在场车辆信息
         	  Fast.api.open('work/indetail/list?card_info='+$("#c-iodetail_card_code").val(),'入场记录',{//?card_code=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype, __('Choose'), {
	           area:['80%', '80%'],
		           callback: function (data) {
		           //	alert(data);//获得到返回所选记录
		           	var iodetail_id = data;
		           //再根据返回所选数据获取进场记录
						Fast.api.ajax({
        					url:'work/indetail/getindetailinfobyid',        													     
             			data:{iodetail_id:data} //再将收到的card_code用POST方式发给主表控制器的total
        			 	 }, 
         			 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	 
         			   $("#c-iodetail_plate_number").val(data.data[0].iodetail_plate_number);
         			   $("#c-iodetail_inweight").val(data.data[0].iodetail_weight);
         	 		  $("#c-iodetail_mototype").val(data.data[0].iodetail_mototype);
         	 		  $("#c-iodetail_checker").val(data.data[0].iodetail_checker);
							$("#c-iodetail_checker").selectPageRefresh();
         			   $("#c-iodetail_product_id").val(data.data[0].iodetail_product_id);
         	   
         			   $("#c-iodetail_product_id").selectPageRefresh();
         			   $("#c-iodetail_custom_id").val(data.data[0].iodetail_custom_id);
         			   $("#c-iodetail_custom_customtype_attribute").val(data.customtype.customtype_attribute);
         			   $("#c-iodetail_custom_name").val(data.custom.custom_name);
							$("#c-iodetail_custom_address").val(data.custom.custom_address);
							$("#c-iodetail_custom_customtype").val(data.custom.custom_customtype);
							$("#c-iodetail_card_code").val(data.data[0].iodetail_card_code);  
							$("#c-iodetail_card_id").val(data.data[0].iodetail_card_id); 
							$("#c-iodetail_in_id").val(data.data[0].iodetail_ID);
					
							var inDate = new Date(data.data[0].iodetail_iotime*1000);
         			   //$("#c-iodetail_intime").val(data.iodetail_iotime);
         			   $("#c-iodetail_intime").val(inDate.getFullYear()+'-'+(inDate.getMonth()+1)+'-'+inDate.getDate()+" "+inDate.getHours()+':'+inDate.getMinutes()+':'+inDate.getSeconds());
         		
         	   
         				console.info(data); 
         				count(); //计算净重及金额		
         	
         	    													      
          		     //return false;    															
         		  	},function(data){
          		     //失败的回调 
           		    $("#c-iodetail_plate_number").val('');
         			 $("#c-iodetail_inweight").val('');
         			 $("#c-iodetail_mototype").val('');
         	  		 $("#c-iodetail_product_id").val('');
         	   	 $("#c-iodetail_product_id").selectPageRefresh();
         		    $("#c-iodetail_custom_customtype_attribute").val('');
         		    $("#c-iodetail_custom_name").val('');
				 	    $("#c-iodetail_custom_address").val('');
						 $("#c-iodetail_custom_customtype").val('');
						 $("#c-iodetail_card_code").val('');
						 $("#c-iodetail_card_id").val(''); 
					 	 $("#c-iodetail_in_id").val('');
					
						 $("#c-iodetail_custom_id").val('');
         	    	 $("#c-iodetail_custom_name").val('');
						 $("#c-iodetail_custom_address").val('');
						 $("#c-iodetail_custom_customtype").val('');
						 $("#c-iodetail_intime").val('');
           		   //return false;	
                  }											  		 		  
 				     ); 	
	       	    }
	            });

 					}
         	    													      
               return false;    															
           	},function(data){
               //失败的回调 
               $("#c-iodetail_plate_number").val('');
         	   $("#c-iodetail_inweight").val('');
         	   $("#c-iodetail_mototype").val('');
         	   $("#c-iodetail_product_id").val('');
         	   $("#c-iodetail_product_id").selectPageRefresh();
         	   $("#c-iodetail_custom_customtype_attribute").val('');
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_card_code").val('');
					$("#c-iodetail_card_id").val(''); 
					$("#c-iodetail_in_id").val('');
					
					$("#c-iodetail_custom_id").val('');
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_intime").val('');
           		//return false;	
            }											  		 		  
 				);
				}
				});
				
				//输入单价，计算金额
				$("#c-iodetail_product_price").bind("keyup",function (event) {
					count(); //计算净重及金额		
					});
					
				//输入折扣，计算金额
				$("#c-iodetail_discount").bind("keyup",function (event) {
					count(); //计算净重及金额		
					});
					
				//输入重量，计算金额
				$("#c-iodetail_weight").bind("keyup",function (event) {
					count(); //计算净重及金额		
					});	
				//计算净重及实际费用
				function count() {
					if ($("#c-iodetail_custom_customtype_attribute").val()==1) {
						$("#c-iodetail_NW").val($("#c-iodetail_weight").val()-$("#c-iodetail_inweight").val());
						
					} else {
						$("#c-iodetail_NW").val($("#c-iodetail_inweight").val()-$("#c-iodetail_weight").val());
						
					}
					$("#c-iodetail_cost").val(($("#c-iodetail_product_price").val()*$("#c-iodetail_NW").val()*$("#c-iodetail_discount").val()/100).toFixed(2));
				
				}
				
				
				
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
					$("#c-iodetail_plate_number_a").val('');//清空车牌参照信息
					$("#c-iodetail_card_code").val('');//清空卡号输入框
					$("#c-iodetail_card_id").val(''); 
					$("#c-iodetail_in_id").val('');
					$("#c-iodetail_custom_id").val('');//清空会员信息
         	   $("#c-iodetail_custom_name").val('');
					$("#c-iodetail_custom_address").val('');
					$("#c-iodetail_custom_customtype").val('');
					$("#c-iodetail_weight").val('');
					$("#c-iodetail_inweight").val('');
					$("#c-iodetail_remark").val('');
					$("#c-iodetail_product_id").val('');
					$("#c-iodetail_product_id").selectPageRefresh();
					//$("#c-iodetail_product_id").selectPageClear();
					$("#c-iodetail_intime").val('');
					$("#c-iodetail_cost").val('');
					$("#c-iodetail_custom_customtype_attribute").val('');
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
                    
                //清空车牌信息
					Fast.api.ajax({
        			url:'base/channel/clearplate',        													     
             	data:{channel_info:$("#c-iodetail_channel").val()} //再将收到的create_code用POST方式发给主表控制器的total
            	 }, 
              	 function (data,ret) { //success  
             		console.info(data);     													      
                  return false;    															
               	},function(data){
                 //失败的回调 
           		  return false;	
               }											  		 		  
 			   	); 
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