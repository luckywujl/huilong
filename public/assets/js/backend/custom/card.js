define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'custom/card/index' + location.search,
                    add_url: 'custom/card/add',
                    edit_url: 'custom/card/edit',
                    del_url: 'custom/card/del',
                    multi_url: 'custom/card/multi',
                    import_url: 'custom/card/import',
                    table: 'custom_card',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'card_id',
                sortName: 'card_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'card_id', title: __('Card_id')},
                        {field: 'card_code', title: __('Card_code'), operate: 'LIKE'},
                        {field: 'card_encode', title: __('Card_encode'), operate: 'LIKE'},
                        //{field: 'card_custom_id', title: __('Card_custom_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        //{field: 'company_id', title: __('Company_id')},
                        {field: 'card_status', title: __('Card_status'), searchList: {"0":__('Card_status 0'),"1":__('Card_status 1'),"2":__('Card_status 2')}, formatter: Table.api.formatter.status},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_customtype_id', title: __('Customcustom.custom_customtype_id')},
                        //{field: 'customcustom.custom_businessarea_id', title: __('Customcustom.custom_businessarea_id')},
                        {field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        {field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        {field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        {field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), searchList: {"0":__('Custom_status 0'),"1":__('Custom_status 1'),"2":__('Custom_status 2')}, formatter: Table.api.formatter.flag},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
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