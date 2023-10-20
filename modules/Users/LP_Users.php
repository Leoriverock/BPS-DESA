<?php

require_once 'modules/Users/authTypes/adLDAP.php';

class LudereProUsers extends Users
{
    //se agrega soporte para ldap
    public function doLogin($user_password)
    {
        global $AUTHCFG;
        $usr_name = $this->column_fields["user_name"];
        $metodoLogin = strtoupper($AUTHCFG['authType']);
        $ok = false;
        switch ($metodoLogin) {
            case 'AD':
                $this->log->debug("Using Active Directory authentication");
                $adldap = new adLDAP();
                $ok     = $adldap->authenticate($this->column_fields["user_name"], $user_password);
                break;
            default:
                $query  = "SELECT crypt_type, user_password, status, user_name FROM $this->table_name WHERE user_name=?";
                $result = $this->db->requirePsSingleResult($query, array($usr_name), false);
                if (empty($result)) {
                    return false;
                }
                $this->column_fields["user_name"] = $this->db->query_result($result, 0, 'user_name');
                $crypt_type                       = $this->db->query_result($result, 0, 'crypt_type');
                $user_status                      = $this->db->query_result($result, 0, 'status');
                $dbuser_password                  = $this->db->query_result($result, 0, 'user_password');
                if ($user_status == 'Active') {
                    if ($crypt_type == 'PHASH') {
                        $ok = password_verify($user_password, $dbuser_password);
                    } else {
                        $encrypted_password = $this->encrypt_password($user_password, $crypt_type);
                        $ok                 = ($dbuser_password == $encrypted_password);
                    }
                }
                break;
        }
        return $ok;
    }
}
