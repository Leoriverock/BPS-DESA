<?php

require_once(dirname(FILE) . '/adLDAP.php');

try {
    $adldap = new adLDAP();
}

catch (adLDAPException $e) {
    echo "<br>".$e;
    exit();
}
$authUser = $adldap->authenticate('b975069', 'luderepro2019');
if ($authUser == true) {
  echo "User authenticated successfully";
}
else {
  // getLastError is not needed, but may be helpful for finding out why:
  echo "\n";
  echo $adldap->getLastError();
  echo "\n";

  echo "User authentication unsuccessful";
}

echo "\n";
$result=$adldap->user()->infoCollection('ldap', array("*"));
echo "User:\n";
echo $result->displayName;
echo "Mail:\n";
echo $result->mail;

?>