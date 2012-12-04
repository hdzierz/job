<span style="color:#21CE39">.</span>
<span class="has_submenu">
<? if($action==""){?>
		<a class="subaself" href="admin_route.php?action=">Routes</a>|
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=">Routes</a>|
<?
	}
?>
<? if($action=="show_old_numbers"||$action=="run_show_old_numbers"){?>
		<a class="subaself" href="admin_route.php?action=show_old_numbers">Old Numbers</a>|
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=show_old_numbers">Old Numbers</a>|
<?
	}
?>
<? /*if($action=="maintain_sequence"){?>
		<a class="subaself" href="admin_route.php?action=maintain_sequence">Sequence</a>|
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=maintain_sequence">Sequence</a>|
<?
	}*/
?>

<? if($action=="maintain_numbers"){?>
		<a class="subaself" href="admin_route.php?action=maintain_numbers">Numbers</a>|
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=maintain_numbers">Numbers</a>|
<?
	}
?>

<? if($action=="change_region"){?>
		<a class="subaself" href="admin_route.php?action=change_region">Regions</a>|
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=change_region">Regions</a>|
<?
	}
?>
<? if($action=="change_area"){?>
		<a class="subaself" href="admin_route.php?action=change_area">Areas</a>
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=change_area">Areas</a>
<?
	}
?>
<? if($action=="errors"){?>
		<a class="subaself" href="admin_route.php?action=errors">Errors</a>
<? 
	}
	else{
?>
		<a class="suba" href="admin_route.php?action=errors">Errors</a>
<?
	}
?>


</span>