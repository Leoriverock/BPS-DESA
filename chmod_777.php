<?php

system('chown apache:apache -R '.__dir__);
system('chmod 777 '.__dir__.'/config.inc.php');
system('chmod 777 '.__dir__.'/config.ludere.php');
system('chmod 777 '.__dir__.'/config.ludere.test.php');
system('chmod 777 '.__dir__.'/tabdata.php');
system('chmod 777 '.__dir__.'/parent_tabdata.php');
system('chmod 777 -R '.__dir__.'/user_privileges/');