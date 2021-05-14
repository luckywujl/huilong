define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/paymentmode/index' + location.search,
                    add_url: 'base/paymentmode/add',
                    edit_url: 'base/paymentmode/edit',
                    del_url: 'base/paymentmode/del',
                    multi_url: 'base/paymentmode/multi',
                    import_url: 'base/paymentmode/import',
                    table: 'base_paymentmode',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'paymentmode_id',
                sortName: 'paymentmode_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'paymentmode_id', title: __('Paymentmode_id')},
                        {field: 'paymentmode', title: __('Paymentmode'), operate: 'LIKE'},
                        //{field: 'company_id', title: __('Company_id')},
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