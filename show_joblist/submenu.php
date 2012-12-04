<span style="color:#21CE39">.</span>
<span class="has_submenu">
<? if($action==""){?>
		<a class="subaself" href="index.php?action=">Home</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=">Home</a>|
<?
	}
?>
<? if($action=="show_old_jobs"){?>
		<a class="subaself" href="index.php?action=show_old_jobs">Old Jobs</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=show_old_jobs">Old Jobs</a>|
<?
	}
?>
<? if($action=="show_templates"){?>
		<a class="subaself" href="index.php?action=show_templates">Templates</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=show_templates">Templates</a>|
<?
	}
?>
<? if($action=="show_quotes"){?>
		<a class="subaself" href="index.php?action=show_quotes">Quotes</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=show_quotes">Quotes</a>|
<?
	}
?>
<? 
	if($CK_CHANGE_GST=='Y'){
		if($action=="gst"){?>
			<a class="subaself" href="index.php?action=gst">GST</a>|
<? 
		}
		else{
?>
			<a class="suba" href="index.php?action=gst">GST</a>|
<?
		}
	}
?>
<? if($action=="fax_email"){?>
		<a class="subaself" href="index.php?action=fax_email">Fax email</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=fax_email">Fax email</a>|
<?
	}
?>
<? 
if($CK_CHANGE_GST=='Y'){
	if($action=="sync_webpage"){?>
		<a class="subaself" href="index.php?action=sync_webpage">Sync Webpage</a>|
<? 
	}
	else{
?>
		<a class="suba" href="index.php?action=sync_webpage">Sync Webpage</a>|
<?
	}
?>
	
<? 
	
		if($action=="fax_email"){?>
			<a class="subaself" href="index.php?action=clean_jobs">Archive Jobs</a>
	<? 
		}
		else{
	?>
			<a class="suba" href="index.php?action=clean_jobs">Archive Jobs</a>
	<?
		}
	}
?>
</span>