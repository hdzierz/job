<?

if($action=="manage_price_template"){
	if($submit == "Save"){
		
		$qry = "UPDATE client
				SET net_costs = '$net_costs',
					base_price = '$base_price',
					linehaul = '$linehaul',
					notes = '".addslashes($notes)."',
					u_nw_1 = '$u_nw_1',
					u_nw_2 = '$u_nw_2',
					u_nw_3 = '$u_nw_3',
					u_nw_4 = '$u_nw_4',
					u_nw_5 = '$u_nw_5',
					u_nw_6 = '$u_nw_6'
				WHERE client_id='$client_id'";	
		query($qry,0);
		
		$qry = "DELETE FROM client_price WHERE client_id='$client_id'";
		query($qry);
		$line_count=0;
		foreach($weight as $w){
			$qry = "INSERT INTO client_price
					SET client_id = '".$client_id."',
						weight = '".$w."',
						pa_dist = '".$pa_dist[$line_count]."'+0,
						pa_sdist = '".$pa_sdist[$line_count]."'+0,
						pa_cont = '".$pa_cont[$line_count]."'+0,
						pr_u_1 = '".$pr_u_1[$line_count]."'+0,
						pr_u_2 = '".$pr_u_2[$line_count]."'+0,
						pr_u_3 = '".$pr_u_3[$line_count]."'+0,
						pr_u_4 = '".$pr_u_4[$line_count]."'+0,
						pr_u_5 = '".$pr_u_5[$line_count]."'+0,
						pr_u_6 = '".$pr_u_6[$line_count]."'+0";
			query($qry);
			$line_count++;
		}
		
		$qry = "DELETE FROM client_pub WHERE client_id='$client_id'";
		query($qry);
		
		$line_count=0;
		foreach($pub_weekly as $p){
			if(!$pub_weekly_ff[$line_count]) $pub_weekly_ff[$line_count]=0;
			if(!$pub_fortnightly_ff[$line_count]) $pub_fortnightly_ff[$line_count]=0;
			if(!$pub_monthly_ff[$line_count]) $pub_monthly_ff[$line_count]=0;
			if(!$pub_other_ff[$line_count]) $pub_other_ff[$line_count]=0;
			if(!$pub_weekly_lh[$line_count]) $pub_weekly_lh[$line_count]=0;
			if(!$pub_fortnightly_lh[$line_count]) $pub_fortnightly_lh[$line_count]=0;
			if(!$pub_monthly_lh[$line_count]) $pub_monthly_lh[$line_count]=0;
			if(!$pub_other_lh[$line_count]) $pub_other_lh[$line_count]=0;
			
			$qry = "INSERT INTO client_pub
					SET client_id = '".$client_id."',
						client_pub_num = '".$line_count."',
						pub_weekly = '".$p."',
						pub_fortnightly = '".$pub_fortnightly[$line_count]."',
						pub_monthly = '".$pub_monthly[$line_count]."',
						pub_other = '".$pub_other[$line_count]."',
						pub_weekly_ff = '".$pub_weekly_ff[$line_count]."',
						pub_fortnightly_ff = '".$pub_fortnightly_ff[$line_count]."',
						pub_monthly_ff = '".$pub_monthly_ff[$line_count]."',
						pub_other_ff = '".$pub_other_ff[$line_count]."',
						pub_weekly_lh = '".$pub_weekly_lh[$line_count]."',
						pub_fortnightly_lh = '".$pub_fortnightly_lh[$line_count]."',
						pub_monthly_lh = '".$pub_monthly_lh[$line_count]."',
						pub_other_lh = '".$pub_other_lh[$line_count]."'";
			query($qry);
			$line_count++;
		}
		
		$MESSAGE = "Price Template Changed.";
	}

}

if($action=="delete_branch"){
	$qry = "DELETE FROM client_branch WHERE client_branch_id='$record'";
	query($qry);
	$action = "show_branches";
}
if($action=="save_branch"){
	if($submit!="Cancel"){
		if($client_branch_id>0) {
			$qry_start = "UPDATE client_branch\n";
			$qry_where = "WHERE client_branch_id='$client_branch_id'\n";
		}
		else{
			$qry_start = "INSERT INTO client_branch\n";
			$qry_where = "";
		}
		$qry = $qry_start." SET client_id='$client_id',address = '".addslashes($address)."' ".$qry_where;
		query($qry);
	}

	$action = "show_branches";
}


//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="save"){
	if(!$discount) $discount=0;
	if($dest=="add"){
		if(!$is_parcel_courier)  $is_parcel_courier=0;
		if(!$has_discount)  $has_discount=0;
		if(!$is_hauler)  $is_hauler=0;
		if(!$is_linehaul)  $is_linehaul=0;
		if(!$name){
			$ERROR="Please give name of client.";
			$action=$dest;
		}
		else{
			$sql = "INSERT INTO client(name,email,card_id,contact_details,is_parcel_courier,is_hauler,is_linehaul,has_discount,invoice_details,discount)
					VALUES('$name','$email','$card_id','$contact_details','$is_parcel_courier','$is_hauler','$is_linehaul','$has_discount','$invoice_details','$discount')";		
			query($sql);	
		}
		$MESSAGE="Client successfully added.";
		$action="";				
	}
	else{
		if(!$is_parcel_courier)  $is_parcel_courier=0;
		if(!$has_discount)  $has_discount=0;
		if(!$is_hauler)  $is_hauler=0;
		if(!$is_linehaul)  $is_linehaul=0;
		$sql = "UPDATE client 
				SET name ='$name',
					card_id ='$card_id',
					contact_details ='$contact_details',
					email ='$email',
					invoice_details ='$invoice_details',
					is_parcel_courier ='$is_parcel_courier',
					is_hauler ='$is_hauler',
					is_linehaul ='$is_linehaul',
					has_discount ='$has_discount',
					discount = '$discount'
				WHERE client_id='$client_id'";
		query($sql);
		$MESSAGE="Client successfully changed.";
		$action="";		
	}
	
	
}

//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: Erases record									//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$qry = "SELECT COUNT(*) AS ct FROM client_pub WHERE client_id='$client_id'";
	$res=query($qry);
	$num = mysql_fetch_object($res);
	if($num->ct==0){
		$sql = "DELETE FROM client WHERE client_id='$record'";
		query($sql);
	}	
	$ERROR="Client successfully deleted.";
	$action="";
}


	
?>