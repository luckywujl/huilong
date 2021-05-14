define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/mototype/index' + location.search,
                    add_url: 'base/mototype/add',
                    edit_url: 'base/mototype/edit',
                    del_url: 'base/mototype/del',
                    multi_url: 'base/mototype/multi',
                    import_url: 'base/mototype/import',
                    table: 'base_mototype',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'mototype_id',
                sortName: 'mototype_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'mototype_id', title: __('Mototype_id')},
                        {field: 'mototype', title: __('Mototype'), operate: 'LIKE'},
                        {field: 'mototype_limitweight', title: __('Mototype_limitweight')},
                        //{field: 'company_id', title: __('Company_id'), operate: 'LIKE'},
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