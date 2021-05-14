define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/producttype/index' + location.search,
                    add_url: 'base/producttype/add',
                    edit_url: 'base/producttype/edit',
                    del_url: 'base/producttype/del',
                    multi_url: 'base/producttype/multi',
                    import_url: 'base/producttype/import',
                    table: 'base_producttype',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'producttype_id',
                sortName: 'producttype_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'producttype_id', title: __('Producttype_id')},
                        {field: 'producttype', title: __('Producttype'), operate: 'LIKE'},
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