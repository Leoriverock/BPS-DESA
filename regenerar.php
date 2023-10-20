<?php
set_time_limit(0);
ini_set('display_errors', 1);
error_reporting(E_ERROR);

require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';

vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport('includes.runtime.Controller');
vimport('includes.runtime.LanguageHandler');
vimport('modules.Install.models.Utils');


global $adb, $current_user, $site_URL;
if (empty($adb)) {
    $adb = PearDatabase::getInstance();
}
if (!$current_user) {
    //    $current_user = Users::getActiveAdminUser();
}

//para el tabdata + parent_tabdata
create_tab_data_file();
create_parenttab_data_file();

echo "FIN regenerar tabdata y parent_tabdata<br>";

//para los user_privileges
require_once 'modules/Users/CreateUserPrivilegeFile.php';
//primero crear la carpeta user_privileges y copiar el archivo default_module_view.php
global $adb;
$userres = $adb->query('SELECT id FROM vtiger_users WHERE deleted = 0');
if ($userres && $adb->num_rows($userres)) {
    while ($userrow = $adb->fetch_array($userres)) {
        createUserPrivilegesfile($userrow['id']);
        createUserSharingPrivilegesfile($userrow['id']);
    }
}

echo "FIN regenerar user_privileges";
