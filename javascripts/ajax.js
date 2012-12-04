
   var http_request = false;
   function makeRequest(url, parameters, tgt, mode) {
	  //alert (url);
      http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
         	// set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
	  
	  // Need to pass the target into alertContents even though http_request.onreadystatechange only acceps pointer to function
	  window.tgt=tgt;
	  encodeURI(parameters);
	  // parameters = 'island='+encodeURI('NI');
	  if(mode=="innerHTML"){
	       http_request.onreadystatechange = alertContents;
	  }
	  else{
		  http_request.onreadystatechange = alertContentsToValue;	
	  }
      http_request.open('POST', url, true);
	  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");	  
      http_request.send(parameters);
   }

   function alertContents() {
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            //alert(http_request.responseText);
            result = http_request.responseText;
            document.getElementById(window.tgt).innerHTML = result;            
         } else {
            alert('There was a problem with the request. State' + http_request.readyState + ' Status: '+http_request.status);
         }
      }
   }
   
   function alertContentsToValue() {
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            //alert(http_request.responseText);
            result = http_request.responseText;
            document.getElementById(window.tgt).value = result;    
			document.getElementById("submit").disabled = false;
			document.getElementById("return_message").innerHTML = "";
			
         } else {
            alert('There was a problem with the request. State' + http_request.readyState + ' Status: '+http_request.status);
         }
      }
   }
   
   function get(obj,tgt,req_tgt) {
	//alert(obj);
	 while (obj.tagName.toLowerCase() != 'form') {
          obj = obj.parentNode;
      }	
     //window.getstr= "?";
	 
	 var qm_pos = req_tgt.indexOf("?")+1;
	 window.getstr = req_tgt.substring(qm_pos)+"&";
	 //alert(window.getstr);
	 //window.getstr='';
	  get_values(obj);
	  //alert(window.getstr);
      makeRequest(req_tgt, getstr, tgt, 'innerHTML');
   }
   
   function getToValue(obj,tgt,req_tgt) {
	 //alert(obj);
	 while (obj.tagName.toLowerCase() != 'form') {
          obj = obj.parentNode;
      }	
     //window.getstr= "?";
	 
	 var qm_pos = req_tgt.indexOf("?")+1;
	 window.getstr = req_tgt.substring(qm_pos)+"&";
	 //alert(window.getstr);
	 //window.getstr='';
	  get_values(obj);
	  //alert(window.getstr);
      makeRequest(req_tgt, getstr, tgt, 'value');
   }
   
   	 function get_values(obj){
		
		  for (i=0; i<obj.length; i++) {
			
			//alert(obj.childNodes[i].tagName);
			 if (obj[i].tagName.toLowerCase() == "input") {
				//alert(obj.childNodes[i].type);	
				if (obj[i].type.toLowerCase() == "text"||obj[i].type.toLowerCase() == "hidden") {
				   window.getstr += obj[i].name + "=" + obj[i].value + "&";
				}
				if (obj[i].type.toLowerCase() == "checkbox") {
				   if (obj[i].checked) {
					  window.getstr += obj[i].name + "=" + obj[i].value + "&";
				   } else {
					  window.getstr += obj[i].name + "=&";
				   }
				}
				if (obj[i].type.toLowerCase() == "radio") {
				   if (obj[i].checked) {
					  window.getstr += obj[i].name + "=" + obj[i].value + "&";
				   }
				}
			 }   
			 if (obj[i].tagName.toLowerCase() == "select") {
	             var sel = obj[i];
				for(j = 0; j < sel.options.length; j++) {
				
				if (sel.options[j].selected && sel.options[j].value != "") {
					getstr += sel.name + "=" + sel.options[j].value + "&";
				}
			}						 }
			 
		  }
      
   }

