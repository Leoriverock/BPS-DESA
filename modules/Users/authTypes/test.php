<?php

//We just need six varaiables here
$baseDN = 'CN=Users,DC=domain,DC=local';
$baseDN = 'CN=Users,DC=net,DC=in,DC=iantel,DC=com,DC=uy';
$adminDN = "CSS_VTIGER";//this is the admin distinguishedName
$adminPswd = "montevideo2016";
$username = 'b975069';//this is the user samaccountname
$userpass = 'solventa2017'; //('b975069', 'solventa2017'
$ldap_conn = ldap_connect('ldap://net.in.iantel.com.uy', 389);//I'm using LDAPS here

if (! $ldap_conn) {
        echo ("<p style='color: red;'>Couldn't connect to LDAP service</p>");
    }
else {    
        echo ("<p style='color: green;'>Connection to LDAP service successful!</p>");
     }
//The first step is to bind the administrator so that we can search user info
$ldapBindAdmin = ldap_bind($ldap_conn, $adminDN, $adminPswd);

if ($ldapBindAdmin){
    echo ("<p style='color: green;'>Admin binding and authentication successful!!!</p>");

    $filter = '(sAMAccountName='.$username.')';
    $attributes = array("name", "telephonenumber", "mail", "samaccountname");
    $result = ldap_search($ldap_conn, $baseDN, $filter, $attributes);
    //$result = ldap_search($ldap_conn, $baseDN);

    $entries = ldap_get_entries($ldap_conn, $result);  
    $userDN = $entries[0]["dn"]; 
    //$userDN="CN=Vignali Sassi\, Fernando Fabian,CN=Users,DC=net,DC=in,DC=iantel,DC=com,DC=uy";
    echo ('<p style="color:green;">I have the user DN: '.$userDN.'</p>');

    //Okay, we're in! But now we need bind the user now that we have the user's DN
    $ldapBindUser = ldap_bind($ldap_conn, $userDN, $userpass);

    if($ldapBindUser){
        echo ("<p style='color: green;'>User binding and authentication successful!!!</p>");        

        ldap_unbind($ldap_connection); // Clean up after ourselves.

    } else {
        echo ("<p style='color: red;'>There was a problem binding the user to LDAP :(</p>");  
        echo ldap_error($ldap_conn); 
    }     

} else {
    echo ("<p style='color: red;'>There was a problem binding the admin to LDAP :(</p>");   
} 
?>