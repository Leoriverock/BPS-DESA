<?php

function getSafeId($id)
{
    if (strpos($id, 'x') != false) {
        $aux = explode('x', $id);
        $id  = $aux[1];
    }
    return $id;
}

function guardarWFLog($fn, $recordId, $result)
{
    global $adb;
    $date_var = date("Y-m-d H:i:s");
    $adb->pquery("INSERT INTO wf_logs (function_name, record_id, fecha_hora, resultado) VALUES (?,?,?,?)", array($fn, $recordId, $adb->formatDate($date_var, true), $result));
}

function getDocumentTypes()
{
    global $adb;
    $documentTypes = array();
    $result        = $adb->query("SELECT *
            FROM lp_accdocumenttypes
            ORDER BY value");
    foreach ($result as $rs) {
        $documentTypes[$rs['id']] = $rs['value'];
    }
    return $documentTypes;
}

function getCountries()
{
    global $adb;
    $countries = array();
    $result    = $adb->query("SELECT *
            FROM lp_acccountries
            ORDER BY value");
    foreach ($result as $rs) {
        $countries[$rs['id']] = $rs['value'];
    }
    return $countries;
}

function findDocumentTypeIdByValue($value)
{
    global $adb;
    $sql = "SELECT id
            FROM lp_accdocumenttypes
            WHERE value = ?";

    $result = $adb->pquery($sql, array($value));
    return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, 'id') : null;
}

function findCountryIdByValue($value)
{
    global $adb;
    $sql = "SELECT id
            FROM lp_acccountries
            WHERE value = ?";

    $result = $adb->pquery($sql, array($value));
    return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, 'id') : null;
}
