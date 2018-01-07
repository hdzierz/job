<?

function redeem_mobile(){            
            $files = glob("MobileScan/Export*");

            $date = $year.'-'.$month.'-15';
            $qry = "UPDATE parcel_run_date SET dtt='$date' WHERE type='mobile'";
            query($qry);

            foreach($files as $f){
                    $file = basename($f);
                    $batch_no = mobileFileReader::getBatchNo($file);
                    if (($handle = fopen("MobileScan/".$file.".csv", "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                            if($data[0] != "Batch_ID"){
                                $ticket = new mobileTicket($data);
                                $ticket->redeem($batch_no, $year, $month);
                            }
                        }
                        fclose($handle);
                    rename("MobileScan/".$file.".csv", "MobileScan/Processed_".$file."csv");
                    echo "File $file processed.<br />";
                }
            }
}


	function process_dates($start_date){
		$types = array("CD","CP","SR","RP","EX");
	
		$qry = "SELECT * FROM parcel_rates WHERE '$start_date' BETWEEN start_date AND end_date LIMIT 1";
		$res = query($qry);
		$existing_rate = mysql_fetch_object($res);
		
		$ex_start_date = $existing_rate->start_date;
		$ex_end_date = date("Y-m-d",strtotime($start_date. " -1 day"));
		$start_date = $start_date;
		$end_date = $existing_rate->end_date;
		
		if($start_date!=$ex_start_date){
			foreach($types as $type){
				$qry = "INSERT INTO parcel_rates SET start_date = '$start_date', end_date = '$end_date', type='$type'";
				query($qry);
			}
			
			$qry = "UPDATE parcel_rates SET start_date = '$ex_start_date', end_date = '$ex_end_date' WHERE start_date='$existing_rate->start_date'";
			query($qry);
		}
	}
	
	
	function create_job_no(){
		$last_job_no=get_max("parcel_job","job_no","","");
	
		// If for some reason the selection of the last job_no fails then the last job number is the highest anyway or
		// if that fails too the job number will be 10000. That way a failure always shows numbers >= 10000.
		if(!$last_job_no){
			$last_job_no=10000;
		}
		else if($last_job_no=="9999") {
			$last_job_no=1;
		}
		else{
			$last_job_no++;
		}
		return $last_job_no;
	}

?>
	
