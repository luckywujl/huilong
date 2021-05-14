define(['jquery', 'bootstrap', 'backend', 'table', 'form','printing'], function ($, undefined, Backend, Table, Form,Printing) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/account/index' + location.search,
                    add_url: 'financial/account/add',
                    edit_url: 'financial/account/edit',
                    del_url: 'financial/account/del',
                    multi_url: 'financial/account/multi',
                    import_url: 'financial/account/import',
                    table: 'financial_account',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'account_id',
                sortName: 'account_id',
                columns: [
                    [
                        {checkbox: true},
                       // {field: 'account_id', title: __('Account_id')},
                        {field: 'account_code', title: __('Account_code'), operate: 'LIKE'},
                        {field: 'account_date', title: __('Account_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'account_type', title: __('Account_type'), searchList: {"0":__('Account_type 0'),"1":__('Account_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'account_object', title: __('Account_object'), operate: 'LIKE'},
                        //{field: 'account_custom_id', title: __('Account_custom_id')},
                        {field: 'account_amount', title: __('Account_amount'), operate:'BETWEEN'},
                        {field: 'account_paymentmode', title: __('Account_paymentmode'), operate: 'LIKE'},
                        {field: 'account_operator', title: __('Account_operator'), operate: 'LIKE'},
                        {field: 'account_statement_code', title: __('Account_statement_code'), operate: 'LIKE'},
                        {field: 'account_remark', title: __('Account_remark')},
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
                        {field: 'customcustom.custom_account', title: __('Customcustom.custom_account'), operate:'BETWEEN'},
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
                        url: "financial/account/print?account_id="+ids,
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
		
					$("#c-account_remark").val('');
					$("#c-account_amount").val('');//清空金额和备注信息
					
					
					//打印单据
					$.ajax({
                        url: "financial/account/print",
                        type: 'post',
                        dataType: 'json',
                        data: {account_id:data.account_id},
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