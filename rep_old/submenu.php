<span style="color:#21CE39">.</span>
<span class="has_submenu">

<?php 
if($report=="envelopes"){?>
		<a class="subaself" href="rep_old.php?report=envelopes">Envelopes</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=envelopes">Envelopes</a>|
<?
	}	
		if($report=="rep_send_out"){?>
		<a class="subaself" href="rep_old.php?report=rep_send_out">Mailout Report</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=rep_send_out">Mailout Report</a>|
<?
	}		
	if($report=="rep_cirpay_by_payee"){?>
		<a class="subaself" href="rep_old.php?report=rep_cirpay_by_payee">Cir.-Pay by Payee</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=rep_cirpay_by_payee">Cir.-Pay by Payee</a>|
<?
	}
	if($report=="rep_cirpay_by_payee_subsum"){?>
		<a class="subaself" href="rep_old.php?report=rep_cirpay_by_payee_subsum">Cir.-Pay by Payee Break Down</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=rep_cirpay_by_payee_subsum">Cir.-Pay by Payee Break Down</a>|
<?
	}	
	if($report=="rep_payout_breakdown"){?>
		<a class="subaself" href="rep_old.php?report=rep_payout_breakdown">Payout Breakdown</a>|
<?		
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=rep_payout_breakdown">Payout Breakdown</a>|
<?	
	}
	if($report=="rep_payout_breakdown_by_dist"){?>
		<a class="subaself" href="rep_old.php?report=rep_payout_breakdown_by_dist">Payout Breakdown by Dist</a>|
<?		
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=rep_payout_breakdown_by_dist">Payout Breakdown by Dist</a>|
<?	
	}
	
?>
<? if($report=="delivery_details"){?>
		<a class="subaself" href="rep_old.php?report=delivery_details">Delivery Details</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=delivery_details">Delivery Details</a>|
<?
	}
?>
<? if($report=="dropoff_details"){?>
		<a class="subaself" href="rep_old.php?report=dropoff_details">Drop Off Details</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_old.php?report=dropoff_details">Drop Off Details</a>|
<?
	}
?>
</span>