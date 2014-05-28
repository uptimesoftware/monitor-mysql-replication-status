monitor-mysql-replication-status
================================
This monitor returns a number of variables from the Show Slave Status. 
You will need to make sure the user try to connect with has at least Grant Replication Client permissions.

At this time it is written for just a Linux monitoring station as it uses a shell script to make the calls to mysql on the monitoring station to connect remotely then it processes the output.