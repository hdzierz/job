<?
// regions and areas do not have their own table. Thus, if somebody renames a region it has to be renamed for all routes with that name.
if($action=="save_region"){
	if($cancel!="Cancel"){
		if($region_name){
			$qry = "UPDATE route 
					SET region='$region_name',
						seq_region =  '$seq_region'
					WHERE region='$region'";
			query($qry);
			$MESSAGE="Region successfully changed.";
			$action="";
		}
		else{
			$ERROR = "The region name should not be empty";
			$action="change_region";
		}
	}
	else{
		$MESSAGE = "Action cancelled";
		$action="";
	}
}

if($action=="delete_region"){
	$qry="DELETE FROM route WHERE region='$region'";
	query($qry);
	$ERROR="Whole region deleted.";
}

if($action=="delete_area"){
	$qry="DELETE FROM route WHERE area='$area'";
	query($qry);
	$ERROR="Whole area deleted.";
}


if($action=="save_area"){
	if($cancel!="Cancel"){
		if($area_name){
			$qry = "UPDATE route 
					SET area='$area_name',
						seq_area =  '$seq_area' 
					WHERE area='$area'";
			query($qry);
			$MESSAGE="Area successfully changed.";
			$action="";
		}
		else{
			$ERROR = "The region name should not be empty";
			$action="change_region";
		}
	}
	else{
		$MESSAGE = "Action cancelled";
		$action="";
	}
	
}



//////////////////////////////////////////////////////////
// ACTION BACKUP                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.route								//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////

if($action=="backup"){
	$today = date("Y-m-d");
	$qry = "INSERT INTO route_old_num(route_id,
										backup_date,
										island,
										region,
										area,
										code,
										description,
										pmp_areacode,
										pmp_runcode,
										num_lifestyle,
										num_farmers,
										num_dairies,
										num_sheep,
										num_beef,
										num_sheepbeef,
										num_hort,
										num_nzfw,
										rmt,rm_rr,rm_f,rm_d,
										seq_region,
										seq_area,
										seq_code,
										is_hidden,
										external) 
				SELECT route_id,
						'$today',
						island,
						region,
						area,
						code,
						description,
						pmp_areacode,
						pmp_runcode,
						num_lifestyle,
						num_farmers,
						num_dairies,
						num_sheep,
						num_beef,
						num_sheepbeef,
						num_hort,
						num_nzfw,
						rmt,rm_rr,rm_f,rm_d,									
						seq_region,
						seq_area,
						seq_code,
						is_hidden,
						external
				 FROM route";
	query($qry);
	$qry = "UPDATE route_old_num SET backup_date=$today WHERE backup_date IS NULL";
	query($qry);
	$action="";
	$MESSAGE = "Routes successfully backuped!";
}


// Delete an affiliation. OBS! That may cause inconsistencies!
if($action=="delete_aff"){
	if($submit!="Cancel"){
		$route_id = get("route_aff","route_id","WHERE route_aff_id='$record'");
		$qry = "DELETE FROM route_aff WHERE route_aff_id='$record'";
		query($qry);
		$ERROR = "Affiliation Deleted. Please check date consistency!";
		$action="show_aff";
	}
}

// Manage an affiliation. 
if($action=="add_aff"||$action=="change_aff"){
	
	if($submit!="Cancel"){

		if($env_dist_id==0) 		$env_dist_id=$dist_id;
		if($env_contractor_id==0)	$env_contractor_id=$contractor_id;
		
		if($env_dist_id==0) 		$env_dist_id=$dist_id;
		if($env_subdist_id==0) 		$env_subdist_id=$subdist_id;
		if($env_contractor_id==0)	$env_contractor_id=$contractor_id;
		if($env_dropoff_id==0) 		$env_dropoff_id=$dropoff_id;
		
		if(!$dist_id || !$subdist_id || !$contractor_id || !$dropoff_id){
			$ERROR = "Plesae specify distributor,S/distributor,contractor, and dropoff.";
			$record = $route_aff_id;
			$action=$dest;
		}
		else{
			
			if($action=="add_aff"){
				$sql   = "SELECT * FROM route_aff 
							WHERE route_aff.route_aff_id='$route_aff_id'
							ORDER BY app_date DESC
							LIMIT 1";
				$route = mysql_fetch_object(query($sql));	
				
				if(!$stop_date)
					$stop_date = date("Y-m-d",strtotime("2037-12-31"));
					
				$qry = "INSERT INTO route_aff(route_id,dist_id,subdist_id,contractor_id,dropoff_id,env_dist_id,env_subdist_id,env_contractor_id,env_dropoff_id,app_date,stop_date)
						VALUES($route_id,$dist_id,$subdist_id,$contractor_id,$dropoff_id,$env_dist_id,$env_subdist_id,$env_contractor_id,$env_dropoff_id,'$app_date','$stop_date')";
						
				$app_date = date("Y-m-d",strtotime("-1 day",strtotime($app_date)));
				$qry2 = "UPDATE route_aff
						SET stop_date		= '$app_date'
						WHERE route_aff_id='$route->route_aff_id'";
						
				//echo nl2br($qry2);
				query($qry);
				query($qry2);
				
				$MESSAGE = "Affiliation Added";
				$action="show_aff";
			}
			else{
				$route_id = get("route_aff","route_id","WHERE route_aff_id='$route_aff_id'");
				$qry = "SELECT * FROM route_aff WHERE route_id='$route_id'";
				$res = query($qry);
				$error=false;
				
				if(!$error){
					$qry = "UPDATE route_aff
							SET route_id		= '$route_id',
								dist_id			= '$dist_id',
								subdist_id		= '$subdist_id',
								contractor_id	= '$contractor_id',
								dropoff_id		= '$dropoff_id',
								env_subdist_id		= '$env_subdist_id',
								env_dropoff_id		= '$env_dropoff_id',						
								env_dist_id   		= '$env_dist_id',
								env_contractor_id 	= '$env_contractor_id',
								app_date		= '$app_date',
								stop_date		= '$stop_date'
							WHERE route_aff_id='$route_aff_id'";
					$MESSAGE = "Affiliation Changed. Please check date consistency!";
					query($qry);
	
				}
				$MESSAGE = "Affiliation Updated";
				
				$action="show_aff";
			}
		}
	}
	else{
		$MESSAGE = "Action Cancelled";
	}
}

//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.route								//
//////////////////////////////////////////////////////////

if($action=="save"){
	if($is_hidden!='Y') $is_hidden='N';
	$description = nl2br($description);
	
	$rmt = $rm_rr+$rm_f;
	
	if($region_new){
		 $region=$region_new;
		 $mess=true;
	}
	if($area_new){ 
		$area=$area_new;
		$mess=true;
	}		
	if($code_new){ 
		$code=$code_new;
		$mess=true;
	}		
	if($mess)
		$MESSAGE = "You added a new region or area. Make sure that the corresponding sorting sequence is set properly (currently 0).<br />";
	
	if(!$island){
		$error=1;
		$ERROR="No island specified";
		$action="edit";
	}
	else if(!$region){
		$error=1;
		$ERROR="No region specified";
		$action="edit";		
	}
	else if(!$area){
		$error=1;
		$ERROR="No area specified";
		$action="edit";		
	}
	else{
		$seq_region = get("route","seq_region","WHERE region='$region'");
		if(!$seq_region) $seq_region=0;
		$seq_area = get("route","seq_area","WHERE area='$area'");
		if(!$seq_area) $seq_area=0;
		
		if(!$seq_code) $seq_code=0;
		if(!$pmp_areacode) $pmp_areacode=0;
		if(!$pmp_runcode) $pmp_runcode=0;
		if(!$pmp_areacode) $pmp_areacode=0;
		if(!$num_lifestyle) $num_lifestyle=0;
		if(!$num_farmers) $num_farmers=0;
		if(!$num_dairies) $num_dairies=0;
		if(!$num_sheep) $num_sheep=0;
		if(!$num_beef) $num_beef=0;
		if(!$num_sheepbeef) $num_sheepbeef=0;
		if(!$num_dairybeef) $num_dairybeef=0;
		if(!$num_hort) $num_hort=0;
		if(!$num_nzfw) $num_nzfw=0;
		if($record==-1){
		$description = addslashes($description);
		
		
			$sql = "INSERT INTO route(island,
									  region,
									  area,
									  code,
									  description,
									  no_ticket_header,
									  pmp_areacode,
									  pmp_runcode,
									  num_lifestyle,
									  num_farmers,
									  num_dairies,
									  num_sheep,
									  num_beef,
									  num_sheepbeef,
									  num_dairybeef,
									  num_hort,
									  num_nzfw,
									  rmt,rm_rr,rm_f,rm_d,												  
									  seq_region,
									  seq_area,
									  seq_code,
									  is_hidden
						) 
					VALUES(	'$island',
							'$region',
							'$area',
							'$code',
							'$description',
							'$no_ticket_header',
							'$pmp_areacode',
							'$pmp_runcode',
							'$num_lifestyle',
							'$num_farmers',
							'$num_dairies',
							'$num_sheep',
							'$num_beef',
							'$num_sheepbeef',
							'$num_dairybeef',
							'$num_hort',
							'$num_nzfw',
							'$rmt'+0,'$rm_rr'+0,'$rm_f'+0,'$rm_d'+0,	
							'$seq_region',
							'$seq_area',
							'$seq_code',
							'$is_hidden')";

			query($sql);
			$route_id=mysql_insert_id();
			$qry = "INSERT INTO route_aff(route_id) VALUES('$route_id')";
			query($qry);
			
			
			$MESSAGE .= "Route successfully added";
		}
		else{
			$description = addslashes($description);
			
			$sql = "UPDATE route
					SET		island		='$island',
							region		='$region',
							area		='$area',
							code		='$code',
							description	='$description',
							no_ticket_header	='$no_ticket_header',
							pmp_areacode='$pmp_areacode'+0,
							pmp_runcode	='$pmp_runcode'+0,
							num_lifestyle='$num_lifestyle'+0,
							num_farmers	='$num_farmers'+0,
							num_dairies ='$num_dairies'+0,
							num_sheep 	='$num_sheep'+0,
							num_beef 	='$num_beef'+0,
							num_sheepbeef ='$num_sheepbeef'+0,
							num_dairybeef ='$num_dairybeef'+0,
							num_hort 	 ='$num_hort'+0,
							num_nzfw 	 ='$num_nzfw'+0,
							rmt = '$rmt'+0,
							rm_rr = '$rm_rr'+0,
							rm_f='$rm_f'+0,
							rm_d='$rm_d'+0,	
							seq_region 	 ='$seq_region'+0,
							seq_area 	 ='$seq_area'+0,
							seq_code 	 ='$seq_code'+0,
							is_hidden	 = '$is_hidden'
					WHERE route_id='$record'";
			query($sql);
			$MESSAGE .= "Route successfully changed";
		}
		
		$action="";	
	}
}


//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.route								//
//////////////////////////////////////////////////////////

if($Save=="Save Seq."){
	$qry = "SELECT route_id FROM route";
	$res_route = query($qry);
	while($route = mysql_fetch_object($res_route)){
		if($efield[$route->route_id]!=$route->sequence)
			$value = intval($efield[$route->route_id]);
			$qry = "UPDATE route SET sequence = '".$value."' WHERE route_id=$route->route_id";
			//echo "<br />";
		query($qry);
	}
	
	$qry = "SELECT route_id,sequence FROM route ORDER BY sequence,route_id";
	$res_route = query($qry);
	$counter=10;
	while($route = mysql_fetch_object($res_route)){
		$qry = "UPDATE route SET sequence = $counter WHERE route_id=$route->route_id";
		//echo "<br />";
		query($qry);
		$counter+=10;
	}
	
	$MESSAGE = "Sequence successfully changed";
	$action="maintain_sequence";	
}

if($action=="save_numbers"){
	if(!$cancel=="Cancel"){
		foreach($id as $route){
			if($route>0){
				/*if(!$pmp_areacode) $pmp_areacode=0;
				if(!$pmp_runcode) $pmp_runcode=0;*/
				if(!$pmp_areacode[$route]) $pmp_areacode[$route]=0;
				if(!$num_lifestyle[$route]) $num_lifestyle[$route]=0;
				if(!$num_farmers[$route]) $num_farmers[$route]=0;
				if(!$num_dairies[$route]) $num_dairies[$route]=0;
				if(!$num_shee[$route]) $num_sheep[$route]=0;
				if(!$num_beef[$route]) $num_beef[$route]=0;
				if(!$num_sheepbeef[$route]) $num_sheepbeef[$route]=0;
				if(!$num_spare2[$route]) $num_spare2[$route]=0;
				if(!$num_hort[$route]) $num_hort[$route]=0;
				if(!$num_nzfw[$route]) $num_nzfw[$route]=0;
				if(!$num_spare[$route]) $num_spare[$route]=0;			
				$qry = "UPDATE route 
						SET num_farmers='".$num_farmers[$route]."', 
							num_lifestyle='".$num_lifestyle[$route]."', 
							num_dairies='".$num_dairies[$route]."', 
							num_sheep='".$num_sheep[$route]."', 
							num_beef='".$num_beef[$route]."', 
							num_sheepbeef='".$num_sheepbeef[$route]."', 
							num_spare2='".$num_spare2[$route]."', 
							num_hort='".$num_hort[$route]."',
							num_nzfw='".$num_nzfw[$route]."',
							num_spare='".$num_spare[$route]."'
						WHERE route_id=$route";
				//echo nl2br($qry);
				query($qry);
			}
		}
		$MESSAGE = "Numbers for $region successfully changed";
	}
	else{
		$region="";
		$area="";
		$code="";
		$MESSAGE="Operation cancelled.";
	}
	$action="maintain_numbers";	
}


//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: Erases record									//
// USES: coural.route									//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$qry = "SELECT route_id FROM job_route WHERE route_id='$record'";
	$res = query($qry);
	if(mysql_num_rows($res)>0){
		$ERROR = "Route could not be deleted as it has been used in the booking process!";
	}
	else{
	
		$sql = "DELETE FROM route WHERE route_id='$record'";
		query($sql);
		$sql = "DELETE FROM route_aff WHERE route_id='$record'";
		query($sql);
		$ERROR = "Route successfully deleted!";
	}
	$action="";	
}





?>