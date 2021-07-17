define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'custom/check/index' + location.search,
                    add_url: 'custom/check/add',
                    edit_url: 'custom/check/edit',
                    del_url: 'custom/check/del',
                    multi_url: 'custom/check/multi',
                    import_url: 'custom/check/import',
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
                        //{field: 'account_id', title: __('Account_id')},
                        {field: 'account_code', title: __('Account_code'), operate: 'LIKE'},
                        {field: 'account_date', title: __('Account_date'),operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'account_type', title: __('Account_type'), searchList: {"0":__('Account_type 0'),"1":__('Account_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'account_object', title: __('Account_object'), operate: 'LIKE'},
                        //{field: 'account_custom_id', title: __('Account_custom_id')},
                        {field: 'account_amount', title: __('Account_amount'), operate:'BETWEEN'},
                        {field: 'account_cost', title: __('Account_cost'), operate:'BETWEEN'},
                        {field: 'account_paymentmode', title: __('Account_paymentmode'), operate: 'LIKE'},
                        {field: 'account_custom_account', title: __('Account_custom_account'), operate:'BETWEEN'},
                        {field: 'account_operator', title: __('Account_operator'), operate: 'LIKE'},
                        {field: 'account_statement_code', title: __('Account_statement_code'), operate: 'LIKE'},
                        {field: 'account_remark', title: __('Account_remark')},
                       
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_password', title: __('Customcustom.custom_password'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype'), operate: 'LIKE'},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea'), operate: 'LIKE'},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        {field: 'customcustom.custom_IDentity', title: __('Customcustom.custom_identity'), operate: 'LIKE'},
                       // {field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                       // {field: 'customcustom.custom_remark', title: __('Customcustom.custom_remark')},
                       // {field: 'customcustom.custom_account', title: __('Customcustom.custom_account'), operate:'BETWEEN'},
                       // {field: 'customcustom.custom_principal', title: __('Customcustom.custom_principal'), operate:'BETWEEN'},
                       // {field: 'customcustom.custom_subsidy', title: __('Customcustom.custom_subsidy'), operate:'BETWEEN'},
                       // {field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                      //  {field: 'financialstatement.statement_id', title: __('Financialstatement.statement_id')},
                       // {field: 'financialstatement.statement_code', title: __('Financialstatement.statement_code'), operate: 'LIKE'},
                      //  {field: 'financialstatement.statement_date', title: __('Financialstatement.statement_date')},
                        {field: 'financialstatement.statement_plate_number', title: __('Financialstatement.statement_plate_number'), operate: 'LIKE'},
                        {field: 'financialstatement.statement_mototype', title: __('Financialstatement.statement_mototype'), operate: 'LIKE'},
                       // {field: 'baseproduct.statement_product_id', title: __('Financialstatement.statement_product_id')},
                       // {field: 'financialstatement.statement_custom_id', title: __('Financialstatement.statement_custom_id')},
                        {field: 'financialstatement.statement_customtype', title: __('Financialstatement.statement_customtype'), operate: 'LIKE'},
                        {field: 'financialstatement.statement_GW', title: __('Financialstatement.statement_gw'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_tare', title: __('Financialstatement.statement_tare'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_NW', title: __('Financialstatement.statement_nw'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_product_price', title: __('Financialstatement.statement_product_price'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_discount', title: __('Financialstatement.statement_discount'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_cost', title: __('Financialstatement.statement_cost'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_paymentmode', title: __('Financialstatement.statement_paymentmode'), operate: 'LIKE'},
                       // {field: 'financialstatement.statement_pay', title: __('Financialstatement.statement_pay'), operate:'BETWEEN'},
                        {field: 'financialstatement.statement_intime', title: __('Financialstatement.statement_intime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'financialstatement.statement_outtime', title: __('Financialstatement.statement_outtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'financialstatement.statement_remark', title: __('Financialstatement.statement_remark'), operate: 'LIKE'},
                       // {field: 'financialstatement.statement_indetail_id', title: __('Financialstatement.statement_indetail_id')},
                       // {field: 'financialstatement.statement_outdetail_id', title: __('Financialstatement.statement_outdetail_id'), operate: 'LIKE'},
                        {field: 'financialstatement.statement_checker', title: __('Financialstatement.statement_checker'), operate:false,visible:false},
                        {field: 'financialstatement.statement_operator', title: __('Financialstatement.statement_operator'), operate:false,visible:false},
                       // {field: 'financialstatement.statement_custom_account', title: __('Financialstatement.statement_custom_account'), operate:'BETWEEN'},
                       // {field: 'financialstatement.statement_status', title: __('Financialstatement.statement_status'), formatter: Table.api.formatter.status},
                       // {field: 'financialstatement.company_id', title: __('Financialstatement.company_id')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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