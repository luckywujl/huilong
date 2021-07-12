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
                    detail_url: 'financial/handovers/detail',
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
                        {field: 'handovers_id', title: __('Handovers_id')},
                        {field: 'handovers_operator', title: __('Handovers_operator'), operate: 'LIKE'},
                        {field: 'handovers_begintime', title: __('Handovers_begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'handovers_endtime', title: __('Handovers_endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'handovers_count', title: __('Handovers_count')},
                        {field: 'handovers_amount', title: __('Handovers_amount'), operate:'BETWEEN'},
                        {field: 'handovers_type', title: __('Handovers_type'), searchList: {"0":__('Handovers_type 0'),"1":__('Handovers_type 1'),"2":__('Handovers_type 2')}, formatter: Table.api.formatter.status},
                        
                        //{field: 'company_id', title: __('Company_id'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, 
                        	buttons: [
 												  		 {
 												  		 	name: 'detail', 
 												  		 	text: '交班明细', 
 												  		 	title: '交班明细', 
 												  		 	icon: 'fa fa-list', 
 												  		 	extend: 'data-area=\'["90%","90%"]\'',    //设置最大化
 												  		 	classname: 'btn btn-xs btn-primary btn-dialog',  												  
 												  		 	url: 'financial/handovers/detail?ids={handovers_id}'
 												  		 	}
														],               
                        events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        detail: function () {
        	Table.api.init();

            var table1 = $("#table1");
            var column = [];
            var row;
                 row = {
                         checkbox: true
                        };          
                 column.push(row);
                 row = {
                         "field": 'handovers_detail_object',
                         "title": '收支项目',
                         operate: false
                        };
                 column.push(row);
            Config.item.forEach(function (item, index, obj){
             var row;
                  row = {
                          "field": item.handovers_detail_paymentmode+'-count',
                          "title": item.handovers_detail_paymentmode+'-笔数',
                          operate: false
                        };
              column.push(row);

              var row;
                  row = {
                          "field": item.handovers_detail_paymentmode+'-cost',
                          "title": item.handovers_detail_paymentmode+'-金额',
                          operate: false
                        };
              column.push(row);
			      })
             
				           row = {
                            "field": '合计-count',
                            "title": '合计笔数',
                            operate: false
                        };
                        column.push(row);
				           row = {
                            "field": '合计-cost',
                            "title": '合计金额',
                            operate: false
                        };
                        column.push(row);
                         

            // 初始化表格
            table1.bootstrapTable({
               url: 'financial/handovers/detail?ids='+Config.ids, //+ location.search+'&custom_id='+Config.row['custom_id'],
					extend: {
                    index_url: 'financial/handovers/detail?ids='+Config.ids + location.search,
                    
                    table: 'financial_handovers_detail',
                },
                toolbar: '#toolbar1',
                commonSearch: false,
					 visible: false,
					 showToggle: false,
					 showColumns: false,
					 search:false,
					 showExport: false,
                pk: 'handovers_object',
                sortName: 'handovers_object',
                columns: column
            });

            // 为表格绑定事件
            Table.api.bindevent(table1);
            $(document).on("click", ".btn-print", function(){
    				window.print();
				});
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