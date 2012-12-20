<?
?>
<script language="javascript">
	function setCheckboxOn (e,minnum,maxnum) {
		for(i=minnum;i<=maxnum;i++){
			var check = document.getElementById &&
							document.getElementById ('check['+i+']');
			if (check)
				check.checked  = true;
		}
		return true;
	}
	function setCheckboxOff (e,minnum,maxnum) {
		for(i=minnum;i<=maxnum;i++){
			var check = document.getElementById &&
							document.getElementById ('check['+i+']');
			if (check)
				check.checked  = false;
		}
		return true;
	}	
			
</script>
<?

if($action=="close_jobs"){
	$max_line = get_max("job","job_id","WHERE finished<>'Y'	AND invoice_no IS NOT NULL AND invoice_no <> ''","");
	$min_line = get_min("job","job_id","WHERE finished<>'Y'	AND invoice_no IS NOT NULL AND invoice_no <> ''","");
?>
Select:
	<span class="set_button" onClick="return setCheckboxOn(event,<?=$min_line?>,<?=$max_line?>)">All</span>
	<span class="set_button" onClick="return setCheckboxOff(event,<?=$min_line?>,<?=$max_line?>)">None</span>
<?

	$qry = "SELECT 	job_id AS Record,
					job_no AS 'Job #',
					
					client.name AS Client,
					publication AS Publication,
					invoice_no AS 'Invoice #'
 			FROM job 
			LEFT JOIN client
			ON client.client_id=job.client_id
			WHERE finished<>'Y'
				AND cancelled <>'Y'
				AND is_quote<>'Y'
			ORDER BY client.name,invoice_no
			";

	$tab = new MySQLTable("proc_invoice.php",$qry);
	
	$tab->showRec=false;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->checkboxTitle = "Close";
	$tab->hasCheckBoxes = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Close";
	$tab->hasSubmitButton = true;
	$tab->formatLine  = true;
	
	
	$tab->startTable();
		$tab->writeTable();
		$tab->addHiddenInput("action","close_jobs");
	$tab->stopTable();
}

if($action=="download_inv"){
?>
	Right click for <a href="<?=$fn?>">download</a>.  <a href="proc_invoice.php?action=select_jobs">Back</a>.
<?

}
if($action=="select_jobs"){
	
	if($month && $year){
		$max_line = get_max("job","job_id","WHERE YEAR(delivery_date) = '$year'	AND MONTH(delivery_date) = '$month'","");
		$min_line = get_min("job","job_id","WHERE YEAR(delivery_date) = '$year'	AND MONTH(delivery_date) = '$month'","");
	?>
		
		<td>Select:
			<span class="set_button" onClick="return setCheckboxOn(event,<?=$min_line?>,<?=$max_line?>)">All</span>
			<span class="set_button" onClick="return setCheckboxOff(event,<?=$min_line?>,<?=$max_line?>)">None</span>
		</td>			
	<?
		$where_add = "";
		if($unproc_only=='Y'){
			$where_add = " AND (trim(invoice_no) = '' OR invoice_no IS NULL) ";
		}
		$qry = "SELECT job.job_id AS Record,
						job_no AS 'Job #',
						invoice_no AS Invoice,
						job.purchase_no AS 'Purchase Order #',
						client.name AS Client,
						job.publication AS 'Publication',
						IF(is_ioa='Y', 'IOA', delivery_date) AS 'D/Date',
						groups.G
				FROM job
				LEFT JOIN client
				ON client.client_id=job.client_id
				LEFT JOIN
				(
					SELECT 	job.job_id AS G,
							publication,
							purchase_no
					FROM job
					GROUP BY purchase_no,job.publication
				) AS groups
				ON groups.publication=job.publication
					AND groups.purchase_no = job.purchase_no
				WHERE YEAR(delivery_date) = '$year'
					AND MONTH(delivery_date) = '$month'
					AND is_att='N'
					AND is_quote='N'
					AND cancelled <> 'Y'
					$where_add
				ORDER BY client.name,job.publication,delivery_date;";
		$tab = new MySQLTable("proc_invoice.php",$qry);
		
		$tab->showRec=0;
		
		$tab->hasAddButton = false;
		$tab->hasEditButton = false;
		$tab->hasDeleteButton = false;
	
		$tab->editableField= "G";
		$tab->colWidth["G"] = 10;
		//$tab->hasSubmitButton = true;
		$tab->submitButtonValue	= "Create Invoices";
		$tab->submitButtonName	= "action";
		
		//$tab->submitOnClick = "return getDateGST();";
		
		$tab->hasCheckBoxes  = true;
		//$tab->checkDefaultOn = false;
		
		$tab->startTable();
			$tab->startNewLine();
				$tab->writeSubmitButton("action","Create Invoices");
				$tab->addInput("fuel_surcharge_show",$fuel_surcharge,"Fuel Surcharge: ",4,true);
				$tab->addCheckbox("is_null_job",$is_null_job,"Null Invoice: ",false);
				$tab->addInput("date_show",date("d M Y",strtotime($date)),"Invoice Date: ",8,true);
			$tab->stopNewLine();
			$tab->writeTable();
			$tab->addHiddenInput("year",$year);
			$tab->addHiddenInput("month",$month);
			$tab->addHiddenInput("fuel_surcharge",$fuel_surcharge);
			$tab->addHiddenInput("gst",$gst);
			$tab->addHiddenInput("date",$date);
		$tab->stopTable();
	}
}
?>