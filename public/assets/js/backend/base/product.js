define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/product/index' + location.search,
                    add_url: 'base/product/add',
                    edit_url: 'base/product/edit',
                    del_url: 'base/product/del',
                    multi_url: 'base/product/multi',
                    import_url: 'base/product/import',
                    table: 'base_product',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'product_ID',
                sortName: 'product_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'product_ID', title: __('Product_id')},
                        {field: 'product_code', title: __('Product_code'), operate: 'LIKE'},
                        {field: 'product_name', title: __('Product_name'), operate: 'LIKE'},
                        {field: 'product_unit', title: __('Product_unit'), operate: 'LIKE'},
                        //{field: 'product_producttype_ID', title: __('Product_producttype_id')},
                       // {field: 'company_id', title: __('Company_id'), operate: 'LIKE'},
                        //{field: 'baseproducttype.producttype_id', title: __('Baseproducttype.producttype_id')},
                        {field: 'baseproducttype.producttype', title: __('Baseproducttype.producttype'), operate: 'LIKE'},
                       // {field: 'baseproducttype.company_id', title: __('Baseproducttype.company_id'), operate: 'LIKE'},
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