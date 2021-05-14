define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/payobject/index' + location.search,
                    add_url: 'base/payobject/add',
                    edit_url: 'base/payobject/edit',
                    del_url: 'base/payobject/del',
                    multi_url: 'base/payobject/multi',
                    import_url: 'base/payobject/import',
                    table: 'base_payobject',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'payobject_id',
                sortName: 'payobject_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'payobject_id', title: __('Payobject_id')},
                        {field: 'payobject', title: __('Payobject'), operate: 'LIKE'},
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