<span style="color:#21CE39">.</span>
<span class="has_submenu">
<? if($action=="close_jobs"){?>
		<a class="subaself" href="proc_invoice.php?action=close_jobs">Close jobs|</a>
<? 
	}
	else{
?>
		<a class="suba" href="proc_invoice.php?action=close_jobs">Close jobs|</a>
<?
	}
?>
<? if($action=="select_jobs"){?>
		<a class="subaself" href="proc_invoice.php?action=select_jobs">Invoicing</a>
<? 
	}
	else{
?>
		<a class="suba" href="proc_invoice.php?action=select_jobs">Invoicing</a>
<?
	}
?>
</span>