define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/productprice/index' + location.search,
                    add_url: 'base/productprice/add',
                    edit_url: 'base/productprice/edit',
                    del_url: 'base/productprice/del',
                    multi_url: 'base/productprice/multi',
                    import_url: 'base/productprice/import',
                    table: 'base_productprice',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'productprice_ID',
                sortName: 'productprice_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'productprice_ID', title: __('Productprice_id')},
                        //{field: 'baseproduct.product_ID', title: __('Baseproduct.product_id')},
                        {field: 'baseproduct.product_code', title: __('Baseproduct.product_code'),sortable:true, operate: 'LIKE'},
                        {field: 'baseproduct.product_name', title: __('Baseproduct.product_name'), sortable:true,operate: 'LIKE'},
                       
                        //{field: 'productprice_product_id', title: __('Productprice_product_id')},
                       // {field: 'productprice_producttype_id', title: __('Productprice_producttype_id')},
                        {field: 'productprice_unit', title: __('Productprice_unit'), operate: 'LIKE'},
                        {field: 'customtype.customtype', title: __('customtype.customtype'),  sortable:true,operate: 'LIKE'},
								                       
                        {field: 'productprice_begin_time', title: __('Productprice_begin_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"HH:mm:ss"},
                        {field: 'productprice_end_time', title: __('Productprice_end_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"HH:mm:ss"},
                        {field: 'productprice_price', title: __('Productprice_price'), operate:'BETWEEN'},
                         
								//{field: 'company_id', title: __('Company_id')},
                        // {field: 'baseproduct.product_unit', title: __('Baseproduct.product_unit'), operate: 'LIKE'},
                       // {field: 'baseproduct.product_producttype_ID', title: __('Baseproduct.product_producttype_id')},
                       // {field: 'baseproduct.company_id', title: __('Baseproduct.company_id'), operate: 'LIKE'},
                        //{field: 'baseproducttype.producttype_id', title: __('Baseproducttype.producttype_id')},
                        {field: 'baseproducttype.producttype', title: __('Baseproducttype.producttype'),  sortable:true,operate: 'LIKE'},
                        //{field: 'baseproducttype.company_id', title: __('Baseproducttype.company_id'), operate: 'LIKE'},
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