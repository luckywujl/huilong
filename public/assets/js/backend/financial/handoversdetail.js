define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/handoversdetail/index' + location.search,
                    add_url: 'financial/handoversdetail/add',
                    edit_url: 'financial/handoversdetail/edit',
                    del_url: 'financial/handoversdetail/del',
                    multi_url: 'financial/handoversdetail/multi',
                    import_url: 'financial/handoversdetail/import',
                    table: 'finanical_handovers_detail',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'handovers_detail_id',
                sortName: 'handovers_detail_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'handovers_detail_id', title: __('Handovers_detail_id')},
                        {field: 'handovers_id', title: __('Handovers_id')},
                        {field: 'handovers_detail_object', title: __('Handovers_detail_object'), operate: 'LIKE'},
                        {field: 'handovers_detail_paymentmode', title: __('Handovers_detail_paymentmode'), operate: 'LIKE'},
                        {field: 'handovers_detail_paycount', title: __('Handovers_detail_paycount')},
                        {field: 'hanvovers_detail_payamount', title: __('Hanvovers_detail_payamount'), operate:'BETWEEN'},
                        {field: 'company_id', title: __('Company_id'), operate: 'LIKE'},
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