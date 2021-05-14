define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
           var statement = $("input[statement_date]").val();
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'financial/statementcount/index' + location.search,
                   
                    table: 'financial_statement',
                }
            });
            
            var table = $("#table");
           
           

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'statement_customtype',
                sortName: 'statement_customtype',
                searchFormVisible:false,
                searchFormTemplate: 'customformtpl',
               
                columns: [
                    [
                        {checkbox: true},
                        {field: 'statement_date', title: __('Statement_date'), operate:'RANGE', addclass:'datetimerange',visible: false, autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD"},
                        {field: 'statement_customtype', title: __('Statement_customtype'), operate: 'LIKE'},
                        {field: 'statement_number', title: __('Statement_number'), operate: 'LIKE'},
                        {field: 'statement_NW', title: __('Statement_nw'), operate:'BETWEEN'},
                        {field: 'statement_cost', title: __('Statement_cost'), operate:'BETWEEN'},
                        {field: 'statement_pay', title: __('Statement_pay'), operate:'BETWEEN'},
                        {field: 'statement_avg', title: __('Statement_avg'), operate:'BETWEEN'},
                        {field: 'statement_status', title: __('Statement_status'), searchList: {"0":__('Statement_status 0'),"1":__('Statement_status 1')},visible:false,formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, 
													buttons: [
 												  		 {
 												  		 	name: 'detail', 
 												  		 	text: '查看明细', 
 												  		 	title: '查看明细', 
 												  		 	icon: 'fa fa-list', 
 												  		 	extend: 'data-area=\'["100%","100%"]\'',    //设置最大化
 												  		 	classname: 'btn btn-xs btn-primary btn-dialog',  												  
 												  		 	url: 'financial/statement/index?statement_customtype={statement_customtype}&statement_status={statement_status}&statement_date=',
 												  		 	
 												  		 	}
														],formatter: Table.api.formatter.operate  } 
                    ]
                ],
                

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