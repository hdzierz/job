	function Right(str, n)
	/***
			IN: str - the string we are RIGHTing
				n - the number of characters we want to return

			RETVAL: n characters from the right side of the string
	***/
	{
			if (n <= 0)     // Invalid bound, return blank string
			   return "";
			else if (n > String(str).length)   // Invalid bound, return
			   return str;                     // entire string
			else { // Valid bound, return appropriate substring
			   var iLen = String(str).length;
			   return String(str).substring(iLen, iLen - n);
			}
	}
	function get_dp_type(ticket){
		var post = ticket.substring(ticket.length-1,ticket.length,10);
		
		if(post!=''){
			return post;
		}
	}
	
	function get_type(ticket){
		return ticket.substring(0,2);
	}

	function restart_scan(inp,clean_c){
		
		if(clean_c)
			document.getElementById("contractor").value="";
		
		var sel= document.getElementById("tickets");
		for(var i=sel.options.length;i>=0;i--){
			sel.options[i] = null;
		}
		
		sel.options.add(new Option("",""));
		var sel= document.getElementById("ticket_dump");
		for(var i=sel.options.length;i>=0;i--){
			sel.options[i] = null;
		}
		
		sel.options.add(new Option("",""));
		
		var t_ticket_counter = document.getElementById("scan_message");
		t_ticket_counter.style.backgroundColor="white";
		
		document.getElementById("submit").disabled = true;
	}

	function validate(){
		if(document.getElementById("contractor").value==''){
			alert("Please give contractor");
			return false;
		}
		else if(document.getElementById("t_ticket_counter").value != document.getElementById("exp_no_tickets").value){
			alert("Number of tickets not as expected.");
			return false;
		}
		return true;
	}


	function IsNumeric(expression) {

		var nums = "0123456789";
		
		if (expression.length==0)return(false);
		
		for (var n=0; n < expression.length; n++){
		
		if(nums.indexOf(expression.charAt(n))==-1)return(false);
		
		
		}
		
		return(true);
	
	}

	function countTickets(area){
					
		var t_ticket_counter = document.getElementById("t_ticket_counter");
		var d_ticket_counter = document.getElementById("d_ticket_counter");
		var p_ticket_counter = document.getElementById("p_ticket_counter");
		//var u_ticket_counter = document.getElementById("u_ticket_counter");
		
		var tickets = document.getElementById("tickets");
		
		var d_count = 0;
		var p_count = 0;
		var u_count = 0;
		for(i=0;i<tickets.options.length;i++){
			if(tickets.options[i].value!=""){
				var post = get_dp_type(tickets.options[i].value);
				if(post=='P') p_count++;
				else if(post=='D') d_count++;
				//else u_count++;
			}
		}
		var t_count = document.getElementById("tickets").options.length-1;

		d_ticket_counter.value = d_count;
		p_ticket_counter.value = p_count;
		t_ticket_counter.value = t_count;
		//u_ticket_counter.value = u_count;
		
		var exp_ticket = document.getElementById("exp_no_tickets").value;
		if(!IsNumeric(exp_ticket)){
			exp_ticket = 60;
		}
		
		if(t_count==exp_ticket) {
			t_ticket_counter.style.backgroundColor="green";
		}
		else if(t_count>exp_ticket) {
			t_ticket_counter.style.backgroundColor="red";
		}
		else{
			t_ticket_counter.style.backgroundColor="white";
		}
		
		return t_count;
	}
	
	function PadDigits(n, totalDigits) 
	{ 
		n = n.toString(); 
		var pd = ''; 
		if (totalDigits > n.length) 
		{ 
			for (i=0; i < (totalDigits-n.length); i++) 
			{ 
				pd += '0'; 
			} 
		} 
		return pd + n.toString(); 
	} 
	
	function generateRandomTicket(area){
		var ticket = area.value;

		var pre = ticket.substring(0,3);
		var post = ticket.substring(ticket.length-1,ticket.length);
		
		var randomnumber=Math.floor(Math.random()*10000000);
		randomnumber = PadDigits(randomnumber, 7);
		
		return pre+randomnumber+post;
	}
	
	function checkDoubleUps(area,sel){
		//alert(area.value);
		for(var i=0;i<sel.options.length;i++){
			if(sel.options[i].value==area.value) return true;
		}
		return false;
	}

	function get_tickets(area,evt){
		var mess = document.getElementById("scan_message");
		
		if(evt.keyCode==13){
			var is_random = false;
			if(area.value.match('!')){
				area.value = generateRandomTicket(area);
				is_random = true;
			}
			if(get_type(area.value)=='CD') mess.style.backgroundColor='red';
			if(get_type(area.value)=='CP') mess.style.backgroundColor='green';
			if(get_type(area.value)=='SR') mess.style.backgroundColor='yellow';
			if(get_type(area.value)=='EX') mess.style.backgroundColor='orange';
			var sel = document.getElementById("tickets");
			var num_tickets = 0;
			if(is_random){
				//var opt = new Option((sel.options.length)+". "+area.value,area.value);
				var opt = new Option(area.value,area.value);

				sel.options[sel.options.length] = opt;
				
				num_tickets = countTickets(area);
				
				mess.value = "Random";
			}
			else if(!checkDoubleUps(area,sel) && validate_redeem_ticket(area.value)){
				var opt = new Option(area.value,area.value);
				//var opt = new Option((sel.options.length)+". "+area.value,area.value);

				sel.options[sel.options.length] = opt;
				
				num_tickets = countTickets(area);
				
				mess.value = "Ticket";
				
				
			}
			else if(!validate_redeem_ticket(area.value)){
				var sel_dump = document.getElementById("ticket_dump");
				var opt = new Option(area.value,area.value);

				sel_dump.options[sel_dump.options.length] = opt;
				
				mess.value = "Invalid";
			}
			else{
				//var sel = document.getElementById("ticket_dump");
				//var opt = new Option(area.value,area.value);

				//sel.options[sel.options.length] = opt;
				
				
				mess.value = "Duplicate";
			}
			
			sel_all();
			
			var form  = document.getElementById("redeem_form");
			
			var exp_tickets  = document.getElementById("exp_no_tickets").value;
			if(exp_tickets == num_tickets ){
				document.getElementById("return_message").innerHTML = "Scanning for notes and redeemed tickets";
				getToValue(form,'wrap_redeemed','parcels/get/get_redeemed.php');
			}
			
			area.value="";
		}
	}
	
	function validate_entered_tickets(){
			
		var form  = document.getElementById("redeem_form");
		
		var exp_tickets  = document.getElementById("exp_no_tickets").value;
		var area  = document.getElementById("tickets");
		num_tickets = countTickets(area);
		
		if(exp_tickets == num_tickets ){
			document.getElementById("return_message").innerHTML = "Scanning for notes and redeemed tickets";
			getToValue(form,'wrap_redeemed','parcels/get/get_redeemed.php');
		}
	}
	
	function no_enter(form,evt){
		return !(evt && evt.keyCode == 13);
	}
	
	function clean_content(id){
		document.getElementById(id).value="";
	}
	
	function get_dist(evt,userid){
		
		var company =  replace_amp("contractor");
		//var nam =  replace_amp("name");
	
		
		var contr  = document.getElementById("contractor");
		//var name  = document.getElementById("name");
		if(contr.value){
			get(contr,'wrap_dist','parcels/get/get_dist.php?user_id='+userid+'&is_name=0&cont='+company);
		}
		
	}
	
	function get_dist_by_name(evt){
		
		var company =  replace_amp("name");
		
		
		var contr  = document.getElementById("name");
		
		get(contr,'wrap_dist','parcels/get/get_dist.php?is_name=1&cont='+company)
	}
	
	function sel_all(){
		var sel= document.getElementById("tickets");
		
		for(var i=0;i<sel.options.length;i++){
			sel.options[i].selected=true;
		}
		return true;
	}
	
	function remove_option_by_value(ob,value){
		
		for(var i=0;i<ob.options.length;i++){
			if(ob.options[i].value==value){
				ob.options[i]=null;
			}
		}
	}
	
	function get_multiple(ob) { 
		
		var arSelected = new Array(); 
		
		while (ob.selectedIndex != -1) { 
			
			if (ob.selectedIndex != 0) {
				var sel = new Array();
				sel[0] = ob.options[ob.selectedIndex].value;
				sel[1] = ob.options[ob.selectedIndex].text;
				sel[2] = ob.selectedIndex;
				arSelected.push(sel); 
				
			}
			ob.options[ob.selectedIndex].selected = false; 
			
			
		} // You can use the arSelected array for further processing. }
		
		return arSelected;
	}
	
	function has_option(ob,option_val){
	
		var op = ob.options;
		
		
		for(var i=0;i<op.length;i++){
			
			var value = op[i].value;
			
			if(value==option_val){
				return true;
			}
		}
		return false;
	}
	
	function put_option(source,target){
		var source_select = document.getElementById(source);
		var target_select = document.getElementById(target);
		var target_num = target_select.options.length;
		
		var sel_source = get_multiple(source_select);
		
		for(i in sel_source){
			if(!has_option(target_select,sel_source[i][0])){
				target_select.options.add(new Option(sel_source[i][1],sel_source[i][0]));
			}
			remove_option_by_value(source_select,sel_source[i][0]);
		}
		var area = document.getElementById("barcode_input");
		countTickets(area);
	}
	
	
	function add_to_opt_value(sel,val){
		for(i=0;i<sel.options.length;i++){
			
			if(sel.options[i].selected==true){
				if(sel.options[i].value.match('/')){
					var value = sel.options[i].value.substring(0,sel.options[i].value.indexOf('/'));
					var text = sel.options[i].text.substring(0,sel.options[i].text.indexOf('/'));
				}
				else{
					var value = sel.options[i].value;
					var text = sel.options[i].text;
				}
				
				sel.options[i].value = value + '/' + val;
				sel.options[i].text = text + ' / ' + val;
			}
		}
	}
	function edit_att(sel_id){
		var sel = document.getElementById(sel_id);
		
		
		window.date ="";
		window.showModalDialog("parcels/edit_att.php",window,"dialogWidth:255px;dialogHeight:250px");
		
		add_to_opt_value(sel,window.date);
	}
	
	
	function replace_amp(target_id){
		var tgt = document.getElementById(target_id);
		if(tgt)
			return tgt.value.replace('&','andamp');
		return "";
	}
	
	
	function validate_redeem(){
		var contr = document.getElementById('contractor');
		if(contr.value==""){
			alert("Please give contractor!");
			return false;
		}
		/*alert(document.getElementById('run_field'));
		var contr = document.getElementById('run');
		if(contr.value==""){
			alert("Please give valid contractor (Page field is empty)!");
			return false;
		}*/
		return true;
	}
	
	function validate_redeem_ticket(ticket){
		var start_dp = ticket.length-1;
		var end_dp = ticket.length;
		var tick = ticket.toUpperCase();
		
		
		if(tick.substring(0,2)!='CD' && tick.substring(0,2)!='CP' && tick.substring(0,2)!='SR' && tick.substring(0,2)!='EX') return false;
		if(tick.substring(0,2)=='CD' || tick.substring(0,2)=='CP'  || tick.substring(0,2)=='EX'  || tick.substring(0,2)=='SR')
			if(tick.substring(start_dp,end_dp)!='P' && tick.substring(start_dp,end_dp)!='D') return false;
		return true;
	}

	function focus_item(evt){
		if(evt.keyCode==9){
			document.getElementById("barcode_input").focus();
		}
	}
	
	function get_ticket_book_qty(contr, i, job_qty){
		var type_control = document.getElementById('type['+i+']');
		var type = type_control.value;
		var start = document.getElementById('start['+i+']').value;

		get(contr,'exp_qty_wrap['+i+']','parcels/get/get_ticket_book_qty.php?type='+type+'&start='+start+'&i='+i+"&job_qty="+job_qty);
	}
	
	function set_tot_book_qty(){
		var tot=0;
		for(var i=0;i<6;i++){
			var val = document.getElementById('tot_book_qty').value;
			tot+=val;
		}
	}
