define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage'], function ($, undefined, Backend, Table, Form,selectpage) {

    var Controller = {
        index: function () {
        	//客户类型下拉框 
        	   $("#statement_customtype").selectPage({
        	      showField : 'customtype',
                keyField: 'customtype',
                data : 'custom/customtype/index',
                pageSize:10,
                eAjaxSuccess: function (data) {
                    data.list = typeof data.rows !== 'undefined' ? data.rows : (typeof data.list !== 'undefined' ? data.list : []);
                    data.totalRow = typeof data.total !== 'undefined' ? data.total : (typeof data.totalRow !== 'undefined' ? data.totalRow : data.list.length);
                    return data;
                },
                eSelect: function (data) {
                	  $("#statement_customtype").val(data.customtype);
 												  		  	
                },
        	    });
        	  //客户名称下拉框 
        	   $("#statement_custom_id").selectPage({
        	      showField : 'custom_name',
                keyField: 'custom_id',
                data : 'custom/custom/index',
                pageSize:10,
                eAjaxSuccess: function (data) {
                    data.list = typeof data.rows !== 'undefined' ? data.rows : (typeof data.list !== 'undefined' ? data.list : []);
                    data.totalRow = typeof data.total !== 'undefined' ? data.total : (typeof data.totalRow !== 'undefined' ? data.totalRow : data.list.length);
                    return data;
                },
                eSelect: function (data) {
                	  $("#c-statement_custom_name").val(data.custom_name);
 												  		  	
                },
        	    });
        	
 			   //确定按钮事件，开始检索数据并组织展示数据	
            $(document).on("click", ".btn-accept", function(){
					var statement_date = $("#statement_date").val();
					var statement_customtype = $('#statement_customtype').val();
					var statement_custom_id = $('#statement_custom_id').val();
				   var url = 'report/business/report?statement_date='+statement_date+'&statement_customtype='+statement_customtype+'&statement_custom_id='+statement_custom_id;//弹出窗口 add.html页面的（fastadmin封装layer模态框将以iframe的方式将add输出到index页面的模态框里）
                    Fast.api.open(url, __('客户报表-查询结果:'+$("#c-statement_custom_name").val()+'('+statement_customtype+')/'+statement_date), {
                     	area:['100%', '100%'],
                      callback:function(value){
                         // 在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传数据
                       }
                 });
				});
			Controller.api.bindevent();	
        },
        report: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'report/business/report' + location.search,
                    
                    table: 'financial_statement',
                }
            });

            var table = $("#table");
            var column = [];
            var row;
                 row = {
                         checkbox: true
                        };          
                 column.push(row);
                 row = {
                         "field": 'custom_code',
                         "title": '客户编码',
                         operate: false
                        };
                 column.push(row);
             	  row = {
                         "field": 'custom_name',
                         "title": '客户名称',
                         operate: false
                        };
                 column.push(row);
             Config.item.forEach(function (item, index, obj){
             var row;
                  row = {
                          "field": item.statement_outtime+'-number',
                          "title": item.statement_outtime+'-车次',
                          operate: false
                        };
              column.push(row);
              var row;
                  row = {
                          "field": item.statement_outtime+'-NW',
                          "title": item.statement_outtime+'-重量',
                          operate: false
                        };
              column.push(row);
              var row;
                  row = {
                          "field": item.statement_outtime+'-cost',
                          "title": item.statement_outtime+'-金额',
                          operate: false
                        };
              column.push(row);
			      })
				           row = {
                            "field": 'statement_number',
                            "title": '合计车次',
                            operate: false
                        };
                        column.push(row);
				           row = {
                            "field": 'statement_NW',
                            "title": '合计重量',
                            operate: false
                        };
                        column.push(row);
                         row = {
                            "field": 'statement_cost',
                            "title": '合计金额',
                            operate: false,
                            formatter: function (value, row, index) {
                                  return value.toFixed(2);
                              }
                        };
                        column.push(row);
                        

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'statement_NW',
                sortName: 'statement_NW',
                search: false, //快速搜索
               // commonSearch:false,//关闭通用搜索
                //searchFormVisible:true,
                searchFormTemplate: 'customformtpl',
                columns: column
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