<span style="color:#21CE39">.</span>
<span class="has_submenu">

<?

	if($report=="ticket_redeemed_by_contractor"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_redeemed_by_contractor">Contractor Ticket Redemption</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_redeemed_by_contractor">Contractor Ticket Redemption</a>|
<?
	}
	if($report=="ticket_redeemed"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_redeemed">Redeemed Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_redeemed">Redeemed Tickets</a>|
<?
	}
	if($report=="ticket_redeemed2"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_redeemed2">Redeemed Tickets 2</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_redeemed2">Redeemed Tickets 2</a>|
<?
	}
	if($report=="invoice"){?>
		<a class="subaself" href="rep_parcels.php?report=invoice">Ticket Redemption / Invoice</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=invoice">Ticket Redemption / Invoice</a>|
<?
	}
	
	if($report=="tickets_received"){?>
		<a class="subaself" href="rep_parcels.php?report=tickets_received">Received Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=tickets_received">Received Tickets</a>|
<?
	}
	if($report=="ticket_unsold"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_unsold">Tickets Unsold</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_unsold">Tickets Unsold</a>|
<?
	}
	if($report=="ticket_sold"){
?>	
		<a class="subaself" href="rep_parcels.php?report=ticket_sold">Tickets Sold</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_sold">Tickets Sold</a>|
<?
	}

	if($report=="ticket_unredeemed"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_unredeemed">Unredeemed Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_unredeemed">Unredeemed Tickets</a>|
<?
	}
    if($report=="ticket_unredeemed_val"){?>
        <a class="subaself" href="rep_parcels.php?report=ticket_unredeemed_val">Unredeemed Tickets Values</a>|
<?
    }
    else{
?>
        <a class="suba" href="rep_parcels.php?report=ticket_unredeemed_val">Unredeemed Tickets Values</a>|
<?
    }
    if($report=="ticket_trace"){?>
        <a class="subaself" href="rep_parcels.php?report=ticket_trace">Ticket Trace</a>|
<?
    }
    else{
?>
        <a class="suba" href="rep_parcels.php?report=ticket_trace">Ticket Trace</a>|
<?
    }

	if($report=="ticket_sales"){?>
		<a class="subaself" href="rep_parcels.php?report=ticket_sales">Ticket Sales</a>
<? 
	}
	else{
?>
		<a class="suba" href="rep_parcels.php?report=ticket_sales">Ticket Sales</a>
<?
	}
	
?>
</span>
