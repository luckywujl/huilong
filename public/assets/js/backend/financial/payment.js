define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
           
           Table.api.init();

            var table1 = $("#table1");
            var column = [];
            var row;
                 row = {
                         checkbox: true
                        };          
                 column.push(row);
                 row = {
                         "field": 'account_object',
                         "title": '收支项目',
                         operate: false
                        };
                 column.push(row);
            Config.item.forEach(function (item, index, obj){
             var row;
                  row = {
                          "field": item.account_paymentmode+'-count',
                          "title": item.account_paymentmode+'-笔数',
                          operate: false
                        };
              column.push(row);

              var row;
                  row = {
                          "field": item.account_paymentmode+'-cost',
                          "title": item.account_paymentmode+'-金额',
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
               url: 'financial/payment/index', //+ location.search+'&custom_id='+Config.row['custom_id'],
					extend: {
                    index_url: 'financial/payment/index' + location.search,
                    
                    table: 'financial_account',
                },
                toolbar: '#toolbar1',
                commonSearch: false,
					 visible: false,
					 showToggle: false,
					 showColumns: false,
					 search:false,
					// showExport: false,
                pk: 'account_object',
                sortName: 'account_object',
                columns: column
            });

            // 为表格绑定事件
            Table.api.bindevent(table1);
            
        	
            // 2、客户提醒-初始化表格参数配置
           Table.api.init();

            var table2 = $("#table2");

            // 初始化表格
            table2.bootstrapTable({
               url: 'financial/payment/handoversindex', //+ location.search+'&custom_id='+Config.row['custom_id'],
					extend: {
                    index_url: 'financial/payment/handoversindex' + location.search,
                    //add_url: 'financial/payment/add',
                    //edit_url: 'financial/payment/edit',
                    //del_url: 'financial/payment/del',
                    //multi_url: 'financial/payment/multi',
                    //import_url: 'financial/payment/import',
                    table: 'financial_account',
                },
                toolbar: '#toolbar2',
                pk: 'account_id',
                sortName: 'account_id',
                columns: [
                    [
                        {checkbox: true},
                        //{field: 'account_id', title: __('Account_id')},
                        {field: 'account_code', title: __('Account_code'), operate: 'LIKE'},
                        {field: 'account_date', title: __('Account_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'account_type', title: __('Account_type'), searchList: {"0":__('Account_type 0'),"1":__('Account_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'account_object', title: __('Account_object'), operate: 'LIKE'},
                        //{field: 'account_custom_id', title: __('Account_custom_id')},
                        {field: 'account_amount', title: __('Account_amount'), operate:'BETWEEN'},
                        {field: 'account_cost', title: __('Account_cost'), operate:'BETWEEN'},
                        {field: 'account_paymentmode', title: __('Account_paymentmode'), operate: 'LIKE'},
                        {field: 'account_operator', title: __('Account_operator'), operate: 'LIKE'},
                        {field: 'account_statement_code', title: __('Account_statement_code'), operate: 'LIKE'},
                        {field: 'account_remark', title: __('Account_remark')},
                        //{field: 'account_handovers', title: __('Account_handovers')},
                        //{field: 'company_id', title: __('Company_id')},
                        //{field: 'customcustom.custom_id', title: __('Customcustom.custom_id')},
                        {field: 'customcustom.custom_code', title: __('Customcustom.custom_code'), operate: 'LIKE'},
                        {field: 'customcustom.custom_name', title: __('Customcustom.custom_name'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_password', title: __('Customcustom.custom_password'), operate: 'LIKE'},
                        {field: 'customcustom.custom_customtype', title: __('Customcustom.custom_customtype'), operate: 'LIKE'},
                        {field: 'customcustom.custom_businessarea', title: __('Customcustom.custom_businessarea'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_address', title: __('Customcustom.custom_address'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_tel', title: __('Customcustom.custom_tel'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_conact', title: __('Customcustom.custom_conact'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_IDentity', title: __('Customcustom.custom_identity'), operate: 'LIKE'},
                        //{field: 'customcustom.custom_status', title: __('Customcustom.custom_status'), formatter: Table.api.formatter.status},
                        //{field: 'customcustom.custom_remark', title: __('Customcustom.custom_remark')},
                        //{field: 'customcustom.custom_account', title: __('Customcustom.custom_account'), operate:'BETWEEN'},
                        //{field: 'customcustom.company_id', title: __('Customcustom.company_id')},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table2);
            $(document).on("click", ".btn-accept", function(){
				    $("#add-form").attr("action","financial/payment/handovers").submit(); 
				});
            
            Controller.api.bindevent();
            
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