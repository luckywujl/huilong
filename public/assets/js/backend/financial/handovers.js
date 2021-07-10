define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/handovers/index' + location.search,
                    add_url: 'financial/handovers/add',
                    edit_url: 'financial/handovers/edit',
                    del_url: 'financial/handovers/del',
                    multi_url: 'financial/handovers/multi',
                    import_url: 'financial/handovers/import',
                    table: 'finanical_handovers',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'handovers_id',
                sortName: 'handovers_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'handovers_id', title: __('Handovers_id')},
                        {field: 'handovers_operator', title: __('Handovers_operator'), operate: 'LIKE'},
                        {field: 'handovers_begintime', title: __('Handovers_begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'handovers_endtime', title: __('Handovers_endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'handovers_count', title: __('Handovers_count')},
                        {field: 'handovers_amount', title: __('Handovers_amount'), operate:'BETWEEN'},
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