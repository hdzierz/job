<span style="color:#21CE39">.</span>
<span class="has_submenu">
<? if(!$action){?>
		<a class="subaself" href="parcels.php">Parcel Home</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php">Parcel Home</a>|
<?
	}
?>
<? if($action=="manage_rates"){?>
		<a class="subaself" href="parcels.php?action=manage_rates">Manage Rates</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=manage_rates">Manage Rates</a>|
<?
	}
?>
<? if($action=="receive_tickets"){?>
		<a class="subaself" href="parcels.php?action=receive_tickets">Receive Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=receive_tickets">Receive Tickets</a>|
<?
	}
?>
<? if($action=="sell_tickets"){?>
		<a class="subaself" href="parcels.php?action=choose_client">Sell Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=choose_client">Sell Tickets</a>|
<?
	}
?>

<? if($action=="show_tickets"){?>
		<a class="subaself" href="parcels.php?action=show_tickets">Show Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=show_tickets">Show Tickets</a>|
<?
	}
?>
<? /*if($action=="process_xerox_scan"){?>
		<a class="subaself" href="parcels.php?action=process_xerox_scan">Canon</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=process_xerox_scan">Canon</a>|
<?
	}*/
?>
<? if($action=="select_xerox_scan"){?>
		<a class="subaself" href="parcels.php?action=select_xerox_scan">Canon Scan</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=select_xerox_scan">Canon Scan</a>|
<?
	}
?>
<? if($action=="select_mobile_scan"){?>
        <a class="subaself" href="parcels.php?action=select_mobile_scan">Mobile Scan</a>|
<?
    }
    else{
?>
        <a class="suba" href="parcels.php?action=select_mobile_scan">Mobile Scan</a>|
<?
    }
?>
<? if($action=="redeem"){?>
		<a class="subaself" href="parcels.php?action=redeem">Ticket Redemption</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=redeem">Ticket Redemption</a>|
<?
	}
?>
<? if($action=="show_redeemed"){?>
		<a class="subaself" href="parcels.php?action=show_redeemed">Previous Ticket Redemption</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=show_redeemed">Previous Ticket Redemption</a>|
<?
	}
?>
<? if($action=="search_tickets"){?>
		<a class="subaself" href="parcels.php?action=search_tickets">Search Tickets</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=search_tickets">Search Tickets</a>|
<?
	}
?>
<? if($action=="search_ticket"){?>
        <a class="subaself" href="parcels.php?action=search_ticket">Search Mob. Ticket</a>|
<?
    }
    else{
?>
        <a class="suba" href="parcels.php?action=search_ticket">Search Mob. Ticket</a>|
<?
    }
?>

<? if($action=="double_ups" || $target=="double_ups"){?>
        <a class="subaself" href="parcels.php?action=double_ups">Doubles</a>|
<?
    }
    else{
?>
        <a class="suba" href="parcels.php?action=double_ups">Doubles</a>|
<?
    }
?>

<? if($action=="show_ticket_notes"){?>
		<a class="subaself" href="parcels.php?action=show_ticket_notes">Ticket Notes</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=show_ticket_notes">Ticket Notes</a>|
<?
	}
	
 	if($action=="print_ticket_header_sheet"){?>
		<a class="subaself" href="parcels.php?action=print_ticket_header_sheet">Print Ticket Header</a>|
<? 
	}
	else{
?>
		<a class="suba" href="parcels.php?action=print_ticket_header_sheet">Print Ticket Header</a>|
<?
	}	
		if($CK_CHANGE_GST=='Y'){
	if($action=="gst"){?>
			<a class="subaself" href="parcels.php?action=gst">GST</a>
<? 
		}
		else{
?>
			<a class="suba" href="parcels.php?action=gst">GST</a>
<?
		}	
	}
?>
<?
if($action=="mobile_data"){?>
    |<a class="subaself" href="parcels.php?action=mobile_data">Mobile Data</a>|
<?
    }
else{
?>
    |<a class="suba" href="parcels.php?action=mobile_data">Mobile Data</a>|
<?
}
?>

</span>
