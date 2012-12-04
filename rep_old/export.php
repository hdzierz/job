<?
include "../includes/mysql.php";
include "../includes/MysqlTable.php";
include "../includes/MysqlSelect.php";
include "../includes/mysql_aid_functions.php";
include "functions.php";


$report = $_POST["report"];

$show_total= $_POST["show_total"];
$show_farmers= $_POST["show_farmers"];
$show_lstyle= $_POST["show_lstyle"];
$show_dairy= $_POST["show_dairy"];
$show_sheep= $_POST["show_sheep"];
$show_beef= $_POST["show_beef"];
$show_sb= $_POST["show_sb"];
$show_db= $_POST["show_db"];
$show_hort= $_POST["show_hort"];
$show_nzfw= $_POST["show_nzfw"];
$choice= $_POST["choice"];


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="export.xls"');

if($report=="pmp_updated"){
?>
		<div class="weekly_head">
			<h3 class="weekly_head_h2">RURAL DELIVERY NUMBERS (<? echo date("j F Y");?>)</h3>
		</div>							
<?	 
	if($choice){
		$qry = "SELECT DISTINCT island,region,area FROM route ORDER BY island,seq_region,seq_area";
		$res = query($qry);
		
		if($submit=="Export")
			$tab  = new MySQLExport("export.html",$qry);
		else
			$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->hiddenFields["TT"]=1;
		$tab->colWidth["Description"]=100;
		$tab->startTable();
		
		if(!$show_total) $tab->hiddenFields["Total"]=1;
		if(!$show_farmers) $tab->hiddenFields["Farmers"]=1;
		if(!$show_lstyle) $tab->hiddenFields["L/style"]=1;
		if(!$show_dairy) $tab->hiddenFields["Dairy"]=1;
		if(!$show_sheep) $tab->hiddenFields["Sheep"]=1;
		if(!$show_beef) $tab->hiddenFields["Beef"]=1;
		if(!$show_sb) $tab->hiddenFields["S/B"]=1;
		if(!$show_db) $tab->hiddenFields["D/B"]=1;
		if(!$show_hort) $tab->hiddenFields["Hort"]=1;
		if(!$show_nzfw) $tab->hiddenFields["F@90%"]=1;
		
		while($area = mysql_fetch_object($res)){
			$region = get("route","region","WHERE area='$area->area'");
			$island = get("route","island","WHERE area='$area->area'");
			
			if(!$show_boxes) $add_where = " AND route.dist_id<>'590'";		
			if($choice==1){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								pmp_areacode AS PMP_AREA,
								pmp_runcode AS PMP_RUN,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								1 AS TT
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							$add_where
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==2){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								1 AS TT
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							$add_where
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==3){
				if($submit=="Export") $region_q = "region	AS Region";
				else $region_q = "IF(LENGTH(region)>10,CONCAT(LEFT(region,10),'...'),region) 	AS Region";
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#$region_q,		
								#area AS Area,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								1 AS TT
								description AS Description
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							$add_where
						ORDER BY island,seq_region,seq_area,seq_code";
			}	
			$res_tt = query($qry);
			if(mysql_num_rows($res_tt)>0){
				$tab->startNewLine();
					$tab->addLineWithStyle("Island: $island Region: ".$region." Area: ".$area->area,"sql_extra_head_big_left");
				$tab->stopNewLine();		
				$tab->writeSQLTableElement($qry,1);
				$tab->startNewLine();
					$num_total 		= get_sum_as("route","num_farmers+num_lifestyle","num_total","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_farmers 	= get_sum("route","num_farmers","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_lifestyle 	= get_sum("route","num_lifestyle","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_dairies 	= get_sum("route","num_dairies","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_sheep 		= get_sum("route","num_sheep","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_beef 		= get_sum("route","num_beef","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_sheepbeef 	= get_sum("route","num_sheepbeef","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_dairybeef 	= get_sum("route","num_dairybeef","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_hort 		= get_sum("route","num_hort","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					$num_nzfw 	= get_sum("route","num_nzfw","WHERE region='$area->region' AND area='$area->area'","GROUP BY island,region,area");
					if($choice==1){
						$tab->addLine("");
						$tab->addLine("");
					}
					else if($choice==2){
						//$tab->addLine("");
					}
					else if($choice==3){
						//$tab->addLine("");
					}
					$tab->addLine("Total:");
					if($show_total)
						$tab->addLine("$num_total");	
					if($show_farmers)
						$tab->addLine("$num_farmers");				
					if($show_lstyle)
						$tab->addLine("$num_lifestyle");				
					if($show_dairy)
						$tab->addLine("$num_dairies");				
					if($show_sheep)
						$tab->addLine("$num_sheep");				
					if($show_beef)
						$tab->addLine("$num_beef");				
					if($show_sb)
						$tab->addLine("$num_sheepbeef");				
					if($show_db)
						$tab->addLine("$num_dairybeef");				
					if($show_hort)
						$tab->addLine("$num_hort");				
					if($show_nzfw)
						$tab->addLine("$num_nzfw");				
				$tab->stopNewLine();
			}
		}

		$tab->startNewLine();
			$num_total 		= get_sum_as("route","num_farmers+num_lifestyle","num_total","","GROUP BY IF(1,1,1)");
			$num_farmers 	= get_sum("route","num_farmers","","GROUP BY IF(1,1,1)");
			$num_lifestyle 	= get_sum("route","num_lifestyle","","GROUP BY IF(1,1,1)");
			$num_dairies 	= get_sum("route","num_dairies","","GROUP BY IF(1,1,1)");
			$num_sheep 		= get_sum("route","num_sheep","","GROUP BY IF(1,1,1)");
			$num_beef 		= get_sum("route","num_beef","","GROUP BY IF(1,1,1)");
			$num_sheepbeef 	= get_sum("route","num_sheepbeef","","GROUP BY IF(1,1,1)");
			$num_dairybeef 	= get_sum("route","num_dairybeef","","GROUP BY IF(1,1,1)");
			$num_hort 		= get_sum("route","num_hort","","GROUP BY IF(1,1,1)");
			$num_nzfw 		= get_sum("route","num_nzfw","","GROUP BY IF(1,1,1)");
			if($choice==1){
				$tab->addLine("");
				$tab->addLine("");
			}
			else if($choice==2){
				$tab->addLine("");
			}
			else if($choice==3){
				$tab->addLine("");
			}
			$tab->addLine("Total:");
			if($show_total)
				$tab->addLine("$num_total");	
			if($show_farmers)
				$tab->addLine("$num_farmers");				
			if($show_lstyle)
				$tab->addLine("$num_lifestyle");				
			if($show_dairy)
				$tab->addLine("$num_dairies");				
			if($show_sheep)
				$tab->addLine("$num_sheep");				
			if($show_beef)
				$tab->addLine("$num_beef");				
			if($show_sb)
				$tab->addLine("$num_sheepbeef");				
			if($show_db)
				$tab->addLine("$num_dairybeef");				
			if($show_hort)
				$tab->addLine("$num_hort");				
			if($show_nzfw)
				$tab->addLine("$num_nzfw");				
		$tab->stopNewLine();		
		$tab->stopTable();			
		if($submit=="Export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}
	}
}


if($report=="pc_dropoff"){
	split_rds();
	if($mode=="geo"){
		$routes = $_POST["route_id"];
		$island = $_POST["island"];
		$region = $_POST["region"];
		$area = $_POST["area"];
		$target = "pc";
		if($routes)
			write_geo_table($routes,$target,"print");				
		
	}
	else{
		$dist_id = $_POST["dist_id"];
		
		if($dist_id)
			write_dist_table($dist_id,"print");
	}
	die();
}

if($report=="dropoff_details"){
	$date=date("Y-m-d");
	if($date && $region && $type){
		if($type=="num_total") $descr_type="Total";
		if($type=="num_farmers") $descr_type="Farmer";
		if($type=="num_lifestyle") $descr_type="Lifestyle";
		if($type=="num_dairies") $descr_type="Dairy";
		if($type=="num_sheep") $descr_type="Sheep";
		if($type=="num_beef") $descr_type="Beef";
		if($type=="num_sheepbeef") $descr_type="Sheep/Beef";
		if($type=="num_dairybeef") $descr_type="Dairy/Beef";
		if($type=="num_hort") $descr_type="Hort";
		if($type=="num_nzfw") $descr_type="F@90%";
		
		if($type=="num_total") $type="(num_lifestyle+num_farmers)";		
		if(!$region[0]){
			$region=array();
			if($island[1]) $where_add="AND island='".$islands[1]."'";
			 $qry = "SELECT DISTINCT region from route WHERE 
					island='".$island[0]."' $where_add 
					AND region<>'MAILINGS'
					AND region<>'BAGS BOXES COUNTER'
					ORDER BY island,seq_region";
			$res = query($qry);
			while($reg = mysql_fetch_object($res)){
				$region[] = $reg->region;
			}
		}
		$in = "IN('0'";
		
		foreach($region as $reg){
			$in.=",'$reg'";
		}
		$in.=")";
		
		$qry = "SELECT 	route_aff.dropoff_id AS Record,
						city	AS 'Delivery point',
						SUM($type) AS $descr_type

				FROM route
				LEFT JOIN route_aff
				ON route_aff.route_id=route.route_id
				LEFT JOIN operator
				ON operator.operator_id=route_aff.dropoff_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE app_date<='$date'
					AND stop_date>='$date'
					AND route.region $in
				/*GROUP BY route_aff.dropoff_id*/
					%s
				ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
		//echo nl2br($qry);		
		$qry_n = sprintf($qry,"GROUP BY route_aff.dropoff_id");
		$qry_sum = sprintf($qry,"GROUP BY IF(1,1,1)");
		$res_sum = query($qry_sum);
		$sum = mysql_fetch_object($res_sum);
		
		$tab = new MySQLTable("report.php",$qry_n,$nameI="report");
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		//$tab->cssSQLTable = "sqltable_8";
		//$tab->cssSQLTable = "sqltable_8";
		$tab->showRec=0;
		$tab->startTable();
		$tab->writeTable();
		$tab->startNewLine();
				$tab->addLine("Grand Total:");
				$tab->addLine($sum->$descr_type);		
			$tab->stopNewLine();
		$tab->stopTable();
		
		//$tab->colWidth["Description"]=180;
		//$tab->colWidth["Phone"]=120;
		//$tab->colWidth["RD"]=120;
		//$tab->colWidth["Contractor"]=120;
		//$tab->colWidth["Address"]=120;
		//$tab->colWidth["City"]=120;
		//$tab->colWidth["Mobile"]=80;
		//$tab->cssSQLUnEvenCol="sqltabunevencol_white";
		//$tab->hasLineSeperator=true;
	}//if date
	die();
}



?>