<?php

// ..\..\apache\php\php master_status.php -h=$UPTIME_HOSTNAME -P=$UPTIME_MYSQL_PORT -d$UPTIME_MYSQL_DB -u$UPTIME_MYSQL_USER -p$UPTIME_MYSQL_PASS

// ./mysql -uuptime -puptime -P53308 --protocol=tcp -h10.1.40.119 -e"show master status\G" uptime

// Options:
// -h Remote (MySQL) server
// -P MySQL Port
// -d MySQL Database
// -u MySQL Username
// -p MySQL Password


try {

$options = getopt("h:P:d:u:p:");
//var_dump($options);
if (isset($options['h'])) {
        $host = $options['h'];
}
else {
        $host = 'localhost';
}
if (isset($options['P'])) {
        $port = $options['P'];
}
else {
        $port = '53308';
}
if (isset($options['d'])) {
        $db = $options['d'];
}
else {
        $db = 'uptime';
}
if (isset($options['u'])) {
        $user = $options['u'];
}
else {
        $user = 'uptime';
}
if (isset($options['p'])) {
        $pass = $options['p'];
}
else {
        $pass = "uptime";
}


////////////////////////////////////////////////////////////
// get the output

$ori_output = array();
$variables_arr = array();
$rv = 0;
$ok = true;
$mysql_bin = '../../mysql/bin/mysql';
$sql = 'show slave status\G';
$full_cmd = "{$mysql_bin} -u{$user} -p{$pass} -P{$port} -h{$host} {$db} -e'{$sql}'";


//print $full_cmd;

exec($full_cmd, $ori_output, $rv);

if (count($ori_output) > 20) {  // first let's make sure we got the right output (usually about 41 lines are outputed)
        foreach ($ori_output as $line) {
                // ignore **************..... line
                if ( preg_match("/^\*\*\*/", $line) ) {
                        continue;
                }

                $cur_line = preg_split("/\:/", $line, 2);
                // trim the values
                $cur_line[0] = strtolower(trim($cur_line[0]));
                $cur_line[1] = trim($cur_line[1]);

                $variables_arr[ $cur_line[0] ] = "\"\"";
                if (count($cur_line) == 2 && strlen($cur_line[1]) > 0) {
                        $variables_arr[ $cur_line[0] ] = $cur_line[1];
                }
        }


//print_r($variables_arr);
		
        // seconds behind master
        print "seconds_behind_master {$variables_arr['seconds_behind_master']}\n";
        
		// Slave IO state
        print "Slave_IO_State {$variables_arr['Slave_IO_State']}\n";
		
        // make sure the slave IO and SQL threads are running
        if ( $variables_arr["slave_io_running"] == "Yes" &&
        $variables_arr["slave_sql_running"] == "Yes" ) {
                // running, so good
                print "slave_IO_running Yes\n";
        }
        else {
                print "slave_IO_running No\n";
                $ok = false;
        }

        // Slave_SQL_Running
        print "Slave_SQL_Running {$variables_arr['Slave_SQL_Running']}\n";

		// last error
        print "Last_IO_Errno {$variables_arr['Last_IO_Errno']}\n";

		// last error
        print "Last_SQL_Errno {$variables_arr['Last_SQL_Errno']}\n";

        if ( ! $ok ) {
                exit(2);
        }

}
else {
        print "Error:";
        foreach ($ori_output as $line) {
                print $line . "\n";
        }
        exit(2);
}

} catch (Exception $e) {
        var_dump($e->getMessage());
        exit(2);
}

?>
