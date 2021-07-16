define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/pay/index' + location.search,
                    add_url: 'base/pay/add',
                    edit_url: 'base/pay/edit',
                    del_url: 'base/pay/del',
                    multi_url: 'base/pay/multi',
                    import_url: 'base/pay/import',
                    table: 'base_paymentmode',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'paymentmode_id',
                sortName: 'paymentmode_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'paymentmode_id', title: __('Paymentmode_id')},
                        {field: 'paymentmode', title: __('Paymentmode'), operate: 'LIKE'},
                        {field: 'company_id', title: __('Company_id')},
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
        pay: function () {
        		function countRow(){
        			var amount=0;
   				var t =document.getElementById('table1');
   				var pay =[];
   				var dataff = {};
 			   	if(t.rows.length>0){
   		    		for (var i=1;i<t.rows.length;i++) {
   		    			dataff.paymentmode = t.rows[i],cells[1].innerText;
   		    			dataff.payamount = t.rows[i],cells[2].innerText;
   	    	 			pay.push(dataff);
   	    	 			dataff = {};
        		  		} 
        		  		return pay;
   				}
 				}
 				//确定收款
          	$(document).on("click", ".btn-accept", function(){
					 var arr = new Array();
            		 var jsonstr='[';
            		 var amount = 0;
            		 var t =document.getElementById('table1');
            	
            		 if(t.rows.length>0){
   		    			for (var i=1;i<t.rows.length;i++) {
   		    				amount +=parseFloat(t.rows[i].cells[2].innerText);
   		    				jsonstr+='{"paymentmode":"'+t.rows[i].cells[1].innerText+'","payamount":"'+t.rows[i].cells[2].innerText+'"},';
							}
							jsonstr=jsonstr.substring(0,jsonstr.length-1);
  							jsonstr+=']';
   					}
   					  if (amount==parseFloat($("#c-amount_c").val())) {
                    Fast.api.close(jsonstr); //往父窗口回调参数   
                 } else {
                  confirm("支付金额小于应付金额！");
                  return false;
                 }   	
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