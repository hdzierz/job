#!/bin/bash

NOW=$(date +"%m_%d_%Y__%H_%M_%S")
OUTFILE="/var/www/html/job/log/run_$NOW.sh"
LOGFILE="/var/www/html/job/log/run_$NOW.log"
LOGFILESTAT="/var/www/html/job/log/run_send_out.log"
QRYSELECT="select oid, config from schedule_mail_send_out where status=2"
QRYUPDATE="mysql -pinkl67z -u sendout -D coural -e \"update schedule_mail_send_out set status=3 where oid=%s\""

echo -e "SENDOUT STARTED AT " $NOW "\n" >> $LOGFILESTAT

echo -e "#!/bin/bash\n" > $OUTFILE

mysql -pinkl67z -u sendout -D coural -e "select oid, config from schedule_mail_send_out where status=2 AND loch=0; UPDATE schedule_mail_send_out SET loch=1 WHERE status=2" | awk '$2 ~ /[0-9]+/ {printf "curl \"https://bob:inkl67z@jobs.coural.co.nz/job/run_send_out.php?%s\";mysql -pinkl67z -u sendout -D coural -e \"update schedule_mail_send_out set status=3 where oid=%s\";\n",  $2, $1}' >> $OUTFILE

chmod u+x $OUTFILE
$OUTFILE | sed "s/<br \/>/\\n/g" | sed -e :a -e 's/<[^>]*>//g;/</N;//ba' > $LOGFILE 

NOW=$(date +"%m_%d_%Y__%H_%M_%S")
echo -e "SENDOUT ENDED AT " $NOW "\n" >> $LOGFILESTAT
./run_send_out_email.sh
