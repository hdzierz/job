<span style="color:#21CE39">.</span>
<span class="has_submenu">
<? if($report=="month_job"){?>
		<a class="subaself" href="rep_revenue.php?report=month_job">Monthly Job</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=month_job">Monthly Job</a>|
<?
	}
	if($report=="label"){?>
		<a class="subaself" href="rep_revenue.php?report=label">Labels</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=label">Labels</a>|
<?
	}
	
	if($report=="weekly"){?>
		<a class="subaself" href="rep_revenue.php?report=weekly">Summary Delivery Instructions</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=weekly">Summary Delivery Instructions</a>|
<?
	}
	
	if($report=="linehaul"){?>
		<a class="subaself" href="rep_revenue.php?report=linehaul">Summary Linehauler</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=linehaul">Summary Linehauler</a>|
<?
	}
	
	if($report=="job_delivery_select"){?>
		<a class="subaself" href="rep_revenue.php?report=job_delivery_select">Summary Delivery Instructions by Job</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=job_delivery_select">Summary Delivery Instructions by Job</a>|
<?
	}
	
	if($report=="rep_cirpay_by_dist"){?>
		<a class="subaself" href="rep_revenue.php?report=rep_cirpay_by_dist">Cir.-Pay by Dist.</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=rep_cirpay_by_dist">Cir.-Pay by Dist.</a>|
<?
	}
	
	if($report=="rep_payout_breakdown_by_dist2"){?>
		<a class="subaself" href="rep_revenue.php?report=rep_payout_breakdown_by_dist2">Payout by Dist 2</a>|
<?		
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=rep_payout_breakdown_by_dist2">Payout by Dist 2</a>|
<?	
	}
	
	
	if($report=="rep_rate_discr"){?>
		<a class="subaself" href="rep_revenue.php?report=rep_rate_discr">Rate Discr.</a>|
<?		
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=rep_rate_discr">Rate Discr.</a>|
<?	
	}
	
	
	if($report=="revenue"){
?>
		<a class="subaself" href="rep_revenue.php?report=revenue">Revenue</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=revenue">Revenue</a>|
<?
	}
	
	if($report=="revenue2"){
?>
		<a class="subaself" href="rep_revenue.php?report=revenue2">Revenue2</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=revenue2">Revenue2</a>|
<?
	}

if($report=="job_manifest_select"){
?>
		<a class="subaself" href="rep_revenue.php?report=job_manifest_select">Manifest</a>
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=job_manifest_select">Manifest</a>
<?
	}

if($report=="label_eight"){
?>
		<a class="subaself" href="rep_revenue.php?report=label_eight">Label 2</a>
<? 
	}
	else{
?>
		<a class="suba" href="rep_revenue.php?report=label_eight">Label 2</a>
<?
	}
?>

</span>