<?php

class LudereProAccounts_Module_Model extends Vtiger_Module_Model
{

    public static function isContribuyente($account_id)
    {
        global $adb;

        $sql = "SELECT acccontexternalnumber FROM vtiger_account WHERE accountid = ?";
        $result = $adb->pquery( $sql, array( $account_id ) );
        $contribuyente = $adb->query_result( $result, 0, 'acccontexternalnumber' );

        return $contribuyente != 0;
    }
}
