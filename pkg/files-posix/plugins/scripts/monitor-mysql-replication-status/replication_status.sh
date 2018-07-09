#!/bin/bash

HOSTNAME=$UPTIME_HOSTNAME
USER=$UPTIME_USERNAME
PASSWORD=$UPTIME_PASSWORD
PORT=$UPTIME_PORT

UPTIMEDIR=`grep pidfile /etc/init.d/uptime_core | head -n 1 | cut -d: -f2 |  cut -c 2- | rev | cut -c 12- | rev`

TMPFILE=`/bin/mktemp /tmp/uptimeTempConfig.XXXXXXXXXX`

echo "[client]
user = $UPTIME_USERNAME
password = $UPTIME_PASSWORD
host = $UPTIME_HOSTNAME
port = $UPTIME_PORT" >> "$TMPFILE"



$UPTIMEDIR/mysql/bin/mysql --defaults-extra-file=$TMPFILE --protocol=tcp -e "SHOW SLAVE STATUS\G" > test.txt
grep "Seconds_Behind_Master" test.txt | awk -F": " {' print "second_behind_master " $2 '}
grep "Slave_IO_State" test.txt | awk -F": " {' print "slave_io_state " $2 '}
grep "Slave_IO_Running" test.txt | awk -F": " {' print "slave_io_running " $2 '}
grep "Slave_SQL_Running" test.txt | awk -F": " {' print "slave_sql_running " $2 '}
grep "Last_IO_Errno" test.txt | awk -F": " {' print "last_io_errno " $2 '}
grep "Last_IO_Error" test.txt | awk -F": " {'if ($2=="") print "last_io_error NONE"; else print "last_io_error " $2 ;'}
grep "Last_SQL_Errno" test.txt | awk -F": " {' print "last_sql_errno " $2 '}
grep "Last_SQL_Error" test.txt | awk -F": " {'if ($2=="") print "last_sql_error NONE"; else print "last_sql_error " $2 ;'}

rm -f test.txt
rm -f $TMPFILE