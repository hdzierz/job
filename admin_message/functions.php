<?

// Prints the booked routes on the job edit screen
function show_table($message_id){

	$qry = "SELECT 	operator.operator_id 	AS Record,
					operator.company AS Company,
					mobile AS Mobile,
					fax AS Fax,
					email AS Email
			FROM message
			LEFT JOIN message_op
			ON message.message_id = message_op.message_id
			LEFT JOIN operator
			ON operator.operator_id=message_op.operator_id
			LEFT JOIN address
			ON address.operator_id=operator.operator_id
			WHERE message.message_id='$message_id'";
	//echo nl2br($qry);
	$tab = new MySQLTable("admin_message.php",$qry,"nocoll");
	$tab->formatLine=true;
	$tab->cssSQLTable="sqltable_scroll";

	$tab->checkboxTitle="Select";
	$tab->submitButtonName="submit";
	$tab->submitButtonValue="Send";
	$tab->submitButtonName2="submit";
	$tab->submitButtonValue2="Delete Selection";
	
	$tab->showRec=0;
	//$tab->colWidth["Action"]=1000;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=true;
	$tab->hasSubmitButton2=true;
	$tab->hasSelectFieldBeforeSubmit=true	;
	$sel = new Select("send_type");
	$sel->setOption("SMS","SMS");
	$sel->setOption("FAX","Fax");
	$sel->setOption("EMAIL","Email");
	$sel->setOption("EMAIL or SMS","Email or SMS");
	//$sel->setOptionIsVal($send_type);
	//$sel->defaultText="All";
	//$sel->multiple = true;
	//$sel->size = 10;
	
	$tab->selectField = $sel;
	$tab->hasCheckBoxes=true;
	$tab->startTable();
	$tab->writeTable();	
	$tab->addHiddenInput("action","table_action");
	$tab->addHiddenInput("message_id",$message_id);
	$tab->stopTable();			
}


	

?>