<?php

$dir = __dir__."/../../../";
system('chown apache:apache -R '.$dir);
system('chmod 777 '.$dir.'config.inc.php');
system('chmod 777 '.$dir.'config.ludere.php');
system('chmod 777 '.$dir.'tabdata.php');
system('chmod 777 '.$dir.'parent_tabdata.php');
system('chmod 777 -R '.$dir.'user_privileges/');

echo "OK!!";