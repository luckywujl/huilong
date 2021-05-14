define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/channel/index' + location.search,
                    add_url: 'base/channel/add',
                    edit_url: 'base/channel/edit',
                    del_url: 'base/channel/del',
                    multi_url: 'base/channel/multi',
                    import_url: 'base/channel/import',
                    table: 'base_channel',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'channel_ID',
                sortName: 'channel_ID',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'channel_ID', title: __('Channel_id')},
                        {field: 'channel', title: __('Channel'), operate: 'LIKE'},
                        {field: 'channel_ipnc', title: __('Channel_ipnc'), operate: 'LIKE'},
                        {field: 'channel_iotype', title: __('Channel_iotype'), searchList: {"0":__('Channel_iotype 0'),"1":__('Channel_iotype 1')}, formatter: Table.api.formatter.normal},
                        //{field: 'channel_weight', title: __('Channel_weight'), operate:'BETWEEN'},
                        //{field: 'channel_plate_number', title: __('Channel_plate_number'), operate: 'LIKE'},
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