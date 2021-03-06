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
        		function countRow(){ //对表格数据汇总，计算
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
 				//添加收款方式
          	$(document).on("click", ".btn-pay", function(){
          	 if ($("#c-paymentmode").val()!=="") {
          	   var custom_id = '';	
          	   if ($("#c-amount").val()!==0) {
          	 	if ($("#c-paymentmode").val()=="储值卡") {
          	 		 //弹窗显示支付方式
         	  		Fast.api.open('base/pay/cardpay?amount='+$("#c-amount").val(),'储值卡支付',{//?card_code=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype, __('Choose'), {
	          			 area:['80%', '90%'],
		          		 callback: function (data) {	
		          		    //alert(data);
		          		 	 custom_id = data;//接受传回来的custom_id
		          		 	 var table1 =document.getElementById('table1');
          			var newTr=table1.insertRow(table1.rows.length);
    					//添加两列
    					var newTd0=newTr.insertCell();
    					var newTd1=newTr.insertCell();
    					var newTd2=newTr.insertCell();
    					var newTd3=newTr.insertCell();
    					//设置列内容和属性
    					newTr.bgcolor="#909090";
    					newTd0.innerText=table1.rows.length-1;
    					newTd0.height = 30;
    					newTd1.innerText=$("#c-paymentmode").val();
    					newTd2.innerText=$("#c-amount").val();
    					newTd3.innerText=custom_id;
    					countRow();
		         			// alert(custom_id); 
	       	   		 },function (data) {	
	       	    		}
	            	});
          	 	} else {
					 custom_id = '';	
					 var table1 =document.getElementById('table1');
          			var newTr=table1.insertRow(table1.rows.length);
    					//添加两列
    					var newTd0=newTr.insertCell();
    					var newTd1=newTr.insertCell();
    					var newTd2=newTr.insertCell();
    					var newTd3=newTr.insertCell();
    					//设置列内容和属性
    					newTr.bgcolor="#909090";
    					newTd0.innerText=table1.rows.length-1;
    					newTd0.height = 30;
    					newTd1.innerText=$("#c-paymentmode").val();
    					newTd2.innerText=$("#c-amount").val();
    					newTd3.innerText=custom_id;
    					countRow();
    			 }
    			 
          			
    			   }
    			}
          	});
          	//删除支付列表行
          	$(document).on("click", ".btn-del", function(){
          		var table1 =document.getElementById('table1');
          		if(table1.rows.length>1){     				
        				table1.deleteRow(table1.rows.length-1);   
    				}
   				countRow();
          	});
 				//确定收款
          	$(document).on("click", ".btn-accept", function(){
					 var arr = new Array();
            		 var jsonstr='[';
            		 var amount = 0;
            		 var t =document.getElementById('table1');
            	
            		 if(t.rows.length>0){
   		    			for (var i=1;i<t.rows.length;i++) {
   		    				amount +=parseFloat(t.rows[i].cells[2].innerText);
   		    				jsonstr+='{"paymentmode":"'+t.rows[i].cells[1].innerText+'","payamount":"'+t.rows[i].cells[2].innerText+'","payremark":"'+t.rows[i].cells[3].innerText+'"},';
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
     		function countRow(){
    			amount = 0
    			if(table1.rows.length>1){
    				var t =document.getElementById('table1')
       			for (var i=1;i<t.rows.length;i++) {
       	 			amount = amount+parseFloat(t.rows[i].cells[2].innerText);
       			} 
   		 	}
   		   $("#c-amount").val(($("#c-amount_c").val()-amount).toFixed(2)); 
			}
			
            Controller.api.bindevent();
        },
        cardpay: function () { //卡付
           //确定收款
          	$(document).on("click", ".btn-cardaccept", function(){
          		Fast.api.ajax({
        				url:'base/pay/cardpay',        													     
             		data:{card_code:$("#c-cardcode").val(),
             				 card_password:$("#c-cardpassword").val(),
             				 card_amount:$("#c-amount_c").val(),//待付金额
             		} //再将收到的create_code用POST方式发给主表控制器的total
          		 }, 
         	 	function (data,ret) { //success 用于接收主表控制器发过来的数据
         	  		//alert(data.custom_id);
         	  		Fast.api.close(data.custom_id); //往父窗口回调参数   
         	   	//console.info(data);     													      
               	return false;    															
            	},function(data){
               //失败的回调 
					
           		//return false;	
               }											  		 		  
 			   	);	 
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