define(['jquery', 'bootstrap', 'backend', 'table', 'form','printing'], function ($, undefined, Backend, Table, Form,Printing) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/charge/index' + location.search,
                    add_url: 'financial/charge/add',
                    edit_url: 'financial/charge/edit',
                    del_url: 'financial/charge/del',
                    multi_url: 'financial/charge/multi',
                    import_url: 'financial/charge/import',
                    table: 'financial_charge',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'charge_id',
                sortName: 'charge_id',
                columns: [
                    [
                        {checkbox: true},
                       // {field: 'charge_id', title: __('Charge_id')},
                        {field: 'charge_code', title: __('Charge_code'), operate: 'LIKE'},
                        {field: 'charge_date', title: __('Charge_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'charge_type', title: __('Charge_type'), searchList: {"0":__('Charge_type 0'),"1":__('Charge_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'charge_object', title: __('Charge_object'), operate: 'LIKE'},
                        //{field: 'charge_custom_id', title: __('Charge_custom_id')},
                        {field: 'charge_amount', title: __('Charge_amount'), operate:'BETWEEN'},
                        {field: 'charge_cost', title: __('Charge_cost')},
                        {field: 'charge_paymentmode', title: __('Charge_paymentmode'), operate: 'LIKE'},
                        {field: 'charge_operator', title: __('Charge_operator'), operate: 'LIKE'},
                        //{field: 'charge_statement_code', title: __('Charge_statement_code'), operate: 'LIKE'},
                        {field: 'charge_remark', title: __('Charge_remark')},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea'), operate: 'LIKE'},
                       // {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                       // {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_IDentity', title: __('Customcustom.custom_identity'), operate: 'LIKE'},
                       // {field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                       // {field: 'customcustom.custom_remark', title: __('Customcustom.custom_remark')},
                        {field: 'charge_custom_account', title: __('Charge_custom_account'), operate:'BETWEEN'},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            //打印
				
				$(document).on("click", ".btn-print", function(index){
					var ids = Table.api.selectedids(table);
				    $.ajax({
                        url: "financial/charge/print?charge_id="+ids,
                        type: 'post',
                        dataType: 'json',
                       
                        success: function (ret) {
                            var options ={
                                templateCode:'rhnpaccount',
                                data:ret.data,
                            };
                            Printing.api.printTemplate(options);
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });		
				});
            
            Controller.api.bindevent();
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
		
					$("#c-charge_remark").val('');
					$("#c-charge_amount").val('');//清空金额和备注信息
					
					
					//打印单据
					$.ajax({
                        url: "financial/charge/print",
                        type: 'post',
                        dataType: 'json',
                        data: {charge_id:data.charge_id},
                        success: function (ret) {
                            var options ={
                                templateCode:'rhnpaccount',
                                data:ret.data,
                            };
                            Printing.api.printTemplate(options);
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });	
                
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