define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'work/motoinfo/index' + location.search,
                    add_url: 'work/motoinfo/add',
                    edit_url: 'work/motoinfo/edit',
                    del_url: 'work/motoinfo/del',
                    multi_url: 'work/motoinfo/multi',
                    import_url: 'work/motoinfo/import',
                    table: 'work_motoinfo',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'moto_id',
                sortName: 'moto_id',
                columns: [
                    [
                        {checkbox: true},
                       // {field: 'moto_id', title: __('Moto_id')},
                        {field: 'moto_platenumber', title: __('Moto_platenumber'), operate: 'LIKE'},
                        {field: 'moto_type', title: __('Moto_type'), operate: 'LIKE'},
                        {field: 'moto_tare', title: __('Moto_tare'), operate:'BETWEEN'},
                        {field: 'moto_date', title: __('Moto_date'),operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'moto_tarecode', title: __('Moto_tarecode'), operate: 'LIKE'},
                        {field: 'moto_operator', title: __('Moto_operator'), operate: 'LIKE'},
                       // {field: 'company_id', title: __('Company_id')},
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