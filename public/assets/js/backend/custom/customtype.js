define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'custom/customtype/index' + location.search,
                    add_url: 'custom/customtype/add',
                    edit_url: 'custom/customtype/edit',
                    del_url: 'custom/customtype/del',
                    multi_url: 'custom/customtype/multi',
                    import_url: 'custom/customtype/import',
                    table: 'custom_customtype',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'customtype_ID',
                sortName: 'customtype_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'customtype_ID', title: __('Customtype_id')},
                        {field: 'customtype', title: __('Customtype'), operate: 'LIKE'},
                        {field: 'customtype_attribute', title: __('Customtype_attribute'),searchList: {"0":__('Customtype_attribute 0'),"1":__('Customtype_attribute 1')}, formatter: Table.api.formatter.normal},
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