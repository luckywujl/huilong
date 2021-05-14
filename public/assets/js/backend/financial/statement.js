define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
        	Controller.api.bindevent();
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/statement/index' + location.search,
                    add_url: 'financial/statement/add',
                    edit_url: 'financial/statement/edit',
                    del_url: 'financial/statement/del',
                    multi_url: 'financial/statement/multi',
                    import_url: 'financial/statement/import',
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
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'statement_code', title: __('Statement_code'), operate: 'LIKE'},
								{field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype'), operate: 'LIKE'},
                        {field: 'statement_status', title: __('Statement_status'), searchList: {"0":__('Statement_status 0'),"1":__('Statement_status 1')}, formatter: Table.api.formatter.status},
                        {field: 'statement_operator', title: __('Statement_operator'), operate: 'LIKE'},                        
                        {field: 'statement_date', title: __('Statement_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statement_plate_number', title: __('Statement_plate_number'), operate: 'LIKE'},
                        {field: 'statement_mototype', title: __('Statement_mototype'), operate: 'LIKE'},
                        //{field: 'statement_product_id', title: __('Statement_product_id')},
                        //{field: 'statement_custom_id', title: __('Statement_custom_id')},
                        {field: 'statement_customtype', title: __('Statement_customtype'), operate: 'LIKE'},
                        {field: 'statement_GW', title: __('Statement_gw'), operate:'BETWEEN'},
                        {field: 'statement_tare', title: __('Statement_tare'), operate:'BETWEEN'},
                        {field: 'statement_NW', title: __('Statement_nw'), operate:'BETWEEN'},
                        {field: 'statement_product_price', title: __('Statement_product_price'), operate:'BETWEEN'},
                        {field: 'statement_discount', title: __('Statement_discount'), operate:'BETWEEN'},
                        {field: 'statement_cost', title: __('Statement_cost'), operate:'BETWEEN'},
                        {field: 'statement_pay', title: __('Statement_pay'), operate:'BETWEEN'},
                        {field: 'statement_intime', title: __('Statement_intime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statement_outtime', title: __('Statement_outtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        //{field: 'statement_remark', title: __('Statement_remark'), operate: 'LIKE'},
                        //{field: 'statement_indetail_id', title: __('Statement_indetail_id')},
                        //{field: 'statement_outdetail_id', title: __('Statement_outdetail_id'), operate: 'LIKE'},
                        {field: 'statement_checker', title: __('Statement_checker'), operate: 'LIKE'},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'baseproduct.product_ID', title: __('Baseproduct.product_id')},
                        {field: 'baseproduct.product_code', title: __('Baseproduct.product_code'), operate: 'LIKE'},
                        {field: 'baseproduct.product_name', title: __('Baseproduct.product_name'), operate: 'LIKE'},
                        {field: 'baseproduct.product_unit', title: __('Baseproduct.product_unit'), operate: 'LIKE'},
                        {field: 'baseproduct.product_producttype_ID', title: __('Baseproduct.product_producttype_id')},
                        //{field: 'baseproduct.company_id', title: __('Baseproduct.company_id'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea'), operate: 'LIKE'},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        {field: 'customcustom.custom_IDentity', title: __('Customcustom.custom_identity'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                        {field: 'customcustom.custom_remark', title: __('Customcustom.custom_remark')},
                        {field: 'customcustom.custom_account', title: __('Customcustom.custom_account'), operate:'BETWEEN'},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(document).on("click", ".btn-repay", function () {
                //在table外不可以使用添加.btn-change的方法
                //只能自己调用Table.api.multi实现
                var ids = Table.api.selectedids(table);
    								layer.confirm('确定要反结算吗?', {btn: ['是','否'] },
       							 function(index){
        					    layer.close(index);
          						  $.post("financial/statement/repay", {ids:ids , action:'success', reply:''},function(response){
             				   if(response.code == 1){
                 	          Toastr.success(response.msg)
                            $(".btn-refresh").trigger('click');
                          }else{
                          Toastr.error(response.msg)
                          }
                      }, 'json')
                    },
                 function(index){
                 layer.close(index);
                 }
                 );
            });
            //在客户信息栏回车
            $("#c-custom_code").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
					if ($("#c-custom_code").val()) {
				//发送客户信息
				Fast.api.ajax({
        			url:'custom/card/getcardinfo',        													     
             	data:{card_info:$("#c-custom_code").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                return {
                    search: params.search,
                    sort: params.sort,
                    order: params.order,
                    offset: params.offset,
                    limit: params.limit,
                    filter: JSON.stringify({'statement_custom_id':data.custom_id,'statement_outtime':$("#c-statement_outtime").val()}),
                    op: JSON.stringify({'statement_custom_id': '=','statement_outtime':'RANGE'}),
                  };
                };
               table.bootstrapTable('refresh', {});
               Toastr.info("查询成功");
         	   $("#c-custom_code").val(data.custom_name);
         	   $("#c-statement_code").val('');
         	   console.info(data);     													      
               return false;    															
            	},function(data){
               //失败的回调 
					//$("#c-account_custom_id").val('');
         	   //$("#c-account_custom_id").selectPageRefresh();
					//$("#c-custom_customtype").val('');
					//$("#c-card_encode").val('');
					//$("#c-custom_account").val('');
           		//return false;	
               }											  		 		  
 			   	);	
 			   } else {
 			       var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                return {
                    search: params.search,
                    sort: params.sort,
                    order: params.order,
                    offset: params.offset,
                    limit: params.limit,
                    filter: JSON.stringify({'statement_outtime':$("#c-statement_outtime").val()}),
                    op: JSON.stringify({'statement_outtime':'RANGE'}),
                    };
                };
               table.bootstrapTable('refresh', {});
               Toastr.info("查询成功");
         	   $("#c-custom_code").val('');
         	   $("#c-statement_code").val('');
 			   
 			   }
			   	}
				});
   
            //在结算单号栏回车
             $("#c-statement_code").bind("keypress",function (event) {
				if (event.keyCode =='13')
				{
				 if($("#c-statement_code").val()){
				//发送结算单信息
				Fast.api.ajax({
        			url:'financial/statement/getcustominfobystatementcode',        													     
             	data:{statement_info:$("#c-statement_code").val()} //再将收到的create_code用POST方式发给主表控制器的total
         	 }, 
         	 function (data,ret) { //success 用于接收主表控制器发过来的数据
         	   $("#c-custom_code").val(data.custom_name);
         	   var myDate = new Date(data.statement_outtime*1000);
               $("#c-statement_outtime").val(myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' 00:00:00 - '+myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' 23:59:59');
          	   
         	   var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                return {
                    search: params.search,
                    sort: params.sort,
                    order: params.order,
                    offset: params.offset,
                    limit: params.limit,
                    filter: JSON.stringify({'statement_custom_id':data.custom_id,'statement_outtime':$("#c-statement_outtime").val()}),
                    op: JSON.stringify({'statement_custom_id': '=','statement_outtime':'RANGE'}),
                  };
                };
               table.bootstrapTable('refresh', {});
               Toastr.info("查询成功");
         	   //$("#c-statement_code").val('');
         	   console.info(data);     													      
               return false;    															
            	},function(data){
               //失败的回调 
					//$("#c-account_custom_id").val('');
         	   //$("#c-account_custom_id").selectPageRefresh();
					//$("#c-custom_customtype").val('');
					//$("#c-card_encode").val('');
					//$("#c-custom_account").val('');
           		//return false;	
               }											  		 		  
 			   	);
 			   } else {
 			       var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                return {
                    search: params.search,
                    sort: params.sort,
                    order: params.order,
                    offset: params.offset,
                    limit: params.limit,
                    filter: JSON.stringify({'statement_outtime':$("#c-statement_outtime").val()}),
                    op: JSON.stringify({'statement_outtime':'RANGE'}),
                    };
                };
               table.bootstrapTable('refresh', {});
               Toastr.info("查询成功");
         	   $("#c-custom_code").val('');
         	   $("#c-statement_code").val('');
 			   }	
			   	}
				});
            
            //确定按钮
            $(document).on("click", ".btn-accept", function () {
            	 var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                return {
                    search: params.search,
                    sort: params.sort,
                    order: params.order,
                    offset: params.offset,
                    limit: params.limit,
                    filter: JSON.stringify({'statement_code':'S202012230005'}),
                    op: JSON.stringify({'statement_code': 'like'}),
                };
            };
            table.bootstrapTable('refresh', {});
            Toastr.info("查询成功");
            return false;

             });
            
            
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
        	
            Controller.api.bindevent();
            //输入单价，计算金额
				$("#c-statement_product_price").bind("keyup",function (event) {
					count(); //计算净重及金额		
					});
					
				//输入折扣，计算金额
				$("#c-statement_discount").bind("keyup",function (event) {
					count(); //计算净重及金额		
					});
				
				//计算净重及实际费用
				function count() {
					
					$("#c-statement_cost").val(($("#c-statement_product_price").val()*$("#c-statement_NW").val()*$("#c-statement_discount").val()/100).toFixed(2));
				
				}
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});