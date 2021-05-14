define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'report/accountpaymode/index' + location.search,
                   
                    table: 'financial_account',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                searchFormVisible:true,
					 search: false, //快速搜索
                searchFormTemplate: 'customformtpl',
                pk: 'account_type',
                sortName: 'account_type',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'account_id', title: __('Account_id')},
                        //{field: 'account_code', title: __('Account_code'), operate: 'LIKE'},
                        //{field: 'account_date', title: __('Account_date')},
                        {field: 'account_type', title: __('Account_type'), searchList: {"0":__('Account_type 0'),"1":__('Account_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'account_paymentmode', title: __('Account_paymentmode'), operate: 'LIKE'},
                        
                        //{field: 'account_object', title: __('Account_object'), operate: 'LIKE'},
                        //{field: 'account_custom_id', title: __('Account_custom_id')},
                        {field: 'account_amount', title: __('Account_amount'), operate:'BETWEEN'},
                        {field: 'account_number', title: __('笔数')},
                        //{field: 'account_operator', title: __('Account_operator'), operate: 'LIKE'},
                        //{field: 'account_statement_code', title: __('Account_statement_code'), operate: 'LIKE'},
                        //{field: 'account_remark', title: __('Account_remark')},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});