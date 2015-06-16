#!/bin/bash

NOW=$(date +"%m_%d_%Y__%H_%M_%S")
LOGFILE="log/run_send_out_email_$NOW.log"
LOGFILESTAT="log/run_send_out_email.log"

echo -e "SENDING EMAILS STARTED AT " $NOW "\n" >> $LOGFILESTAT

php run_send_out_email.php | sed "s/<br \/>/\\n/g" | sed -e :a -e 's/<[^>]*>//g;/</N;//ba' >> $LOGFILE

NOW=$(date +"%m_%d_%Y__%H_%M_%S")
echo -e "SENDING EMAILS STARTED AT " $NOW "\n" >> $LOGFILESTAT

