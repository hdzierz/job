<? 

 
?>
<div id="menu">
<? 
#echo "pagename = $pagename<Br>";
if ($pagename=="login.php") { ?>
	<div id="activetab">
		<span class="menulink">Login</span>
	</div>

<? } else { ?>

		<? if ($PAGE_NAME=="index.php") {?>
			<div id="activetab">
				<a class="menulink" href="index.php">Current Jobs</a>
			</div>
		<? } else { ?>
			<div id="tab">
				<a class="menulink2" href="index.php">Current Jobs</a>
			</div>		
		<? } #else pagename = index ?>
		
		<? 

		if ($CK_PAGE_JOBPROC=="Y") {
			if ($PAGE_NAME=="proc_job.php") {?>
				<div id="activetab">
					<a class="menulink" href="proc_job.php?action=new_job">Job Booking</a>
				</div>
			<? } else { ?>
				<div id="tab">
					<a class="menulink2" href="proc_job.php?action=new_job">Job Booking</a>
				</div>		
			<? } #else pagename = proc_invoice.php
		} #proc invoice ?>
		
		
		<? 
		if ($CK_PAGE_REPORTS=="Y") {
				if ($PAGE_NAME=="reports.php") {?>
					<div id="activetab">
						<a class="menulink" href="reports.php?report=by_region">Maint Reps</a>
					</div>
				<? } else { ?>
					<div id="tab">
						<a class="menulink2" href="reports.php?report=by_region">Maint Reps</a>
					</div>		
				<? } #else pagename = reports 
		} #reportpage 
		
		if ($CK_PAGE_REVENUE=="Y") {
			if ($pagename=="rep_revenue.php") {?>
				<div id="activetab">
					<a class="menulink" href="rep_revenue.php">Job Reps</a>
				</div>
			<? } else { ?>
				<div id="tab">
					<a class="menulink2" href="rep_revenue.php">Job Reps</a>
				</div>		
			<? } #else pagename = rep_revenue.php
		} #report finance
		
		if ($CK_PAGE_REP_OLD=="Y") {
			if ($pagename=="rep_old.php") {?>
				<div id="activetab">
					<a class="menulink" href="rep_old.php">Old Reps</a>
				</div>
			<? } else { ?>
				<div id="tab">
					<a class="menulink2" href="rep_old.php">Old Reps</a>
				</div>		
			<? } #else pagename = rep_revenue.php
		} #report finance
		
		if ($CK_PAGE_INVOICE=="Y") {
			if ($pagename=="proc_invoice.php?action=select_jobs") {?>
				<div id="activetab">
					<a class="menulink" href="proc_invoice.php?action=select_jobs">Invoices</a>
				</div>
			<? } else { ?>
				<div id="tab">
					<a class="menulink2" href="proc_invoice.php?action=select_jobs">Invoices</a>
				</div>		
			<? } #else pagename = rep_revenue.php
		} #report finance
		
		if ($CK_PAGE_PARCELS=="Y") {
			if ($pagename=="parcels.php") {?>
				<div id="activetab_parcel">
					<a class="menulink_parcel" href="parcels.php">Parcels</a>
				</div>
			<? } else { ?>
				<div id="tab_parcel">
					<a class="menulink2_parcel" href="parcels.php">Parcels</a>
				</div>		
			<? } #else pagename = rep_revenue.php
		} #report finance
		if ($CK_PAGE_REPPARCELS=="Y") {
			if ($pagename=="rep_parcels.php") {?>
				<div id="activetab_parcel">
					<a class="menulink_parcel" href="rep_parcels.php">Parcel Reps</a>
				</div>
			<? } else { ?>
				<div id="tab_parcel">
					<a class="menulink2_parcel" href="rep_parcels.php">Parcel Reps</a>
				</div>		
			<? } #else pagename = rep_revenue.php
		} #report finance
		
	 # right-hand side tabs
	 ########################################################################################################
		if ($PAGE_NAME=="logout.php") {?>
			<div id="tab2">
				<a class="logouta" href="logout.php">Logout</a>
			</div>
		<? } else { ?>
			<div id="tab2">
				<a class="logouta" href="logout.php">Logout</a>
			</div>		
		<? } #else pagename = crops  ?>
		
		
		<? /*if ($CK_PAGE_OPADMIN=="Y") {
				if ($PAGE_NAME=="admin_operator.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_operator.php?letter=abc">Share Holders</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_operator.php?letter=abc">Share Holders</a>
					</div>		
				<? } #else pagename = crops 
		} #opadmin */ ?>	
				
		<? if ($CK_PAGE_ADDRADMIN=="Y") {
		
				if ($PAGE_NAME=="admin_message.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_message.php">Messages</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_message.php">Messages</a>
					</div>		
				<? } #else pagename = crops 
				
				if ($PAGE_NAME=="admin_address.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_address.php">Addresses</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_address.php">Addresses</a>
					</div>		
				<? } #else pagename = crops 
		} #addradmin ?>	
		
						
		<? if ($CK_PAGE_CLIENTADMIN=="Y") {
				if ($PAGE_NAME=="admin_client.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_client.php">Clients</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_client.php">Clients</a>
					</div>		
				<? } #else pagename = crops 
		} #clientadmin  ?>		
						
		<? if ($CK_PAGE_ROUTEADMIN=="Y") {
				if ($PAGE_NAME=="admin_route.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_route.php">Routes</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_route.php">Routes</a>
					</div>		
				<? } #else pagename = crops 
		} #routeadmin  ?>			
		
		<? if ($CK_PAGE_USERADMIN=="Y") { 
				if ($PAGE_NAME=="admin_user.php") {?>
					<div id="activetab2">
						<a class="menulink" href="admin_user.php">Users</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="admin_user.php">Users</a>
					</div>		
				<? } #else pagename = crops 
		} #useradmin  ?>	
		
		<? if (1) { 
				if ($PAGE_NAME=="pod/index.php") {?>
					<div id="activetab2">
						<a class="menulink" href="/pod/index.php">POD</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="/pod/index.php">POD</a>
					</div>		
				<? } #else pagename = crops 
				if ($PAGE_NAME=="pod/sample.php") {?>
					<div id="activetab2">
						<a class="menulink" href="/pod/sample.php">Haul</a>
					</div>
				<? } else { ?>
					<div id="tab2">
						<a class="menulink2" href="/pod/sample.php">Haul</a>
					</div>		
				<? } #else pagename = crops 
		} #useradmin  ?>	
		
	

<? } #else pagename = login ?>			
	<!-- don't remove this div.  it aligns the menu properly. -->
	<div id="interm"><span class="intermt">.</span> </div>
		
 	<!--<div style="color:#FFFFFF; font-size:1px "><p>.</p></div>-->
</div> <!-- DIV menu --> 
