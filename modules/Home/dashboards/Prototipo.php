<?php

class Home_Prototipo_Dashboard extends Vtiger_IndexAjax_View
{

    public function process(Vtiger_Request $request)
    {
    	global $adb, $site_URL;
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer      = $this->getViewer($request);
        $moduleName  = $request->getModule();

        $linkId = $request->get('linkid');
        $data        = array();

        $xml = simplexml_load_file($site_URL . "modules/Accounts/vtlibs/PaisesBPS.xml");
        $countries = array();
        foreach ($xml->colPaises as $pais) {
            $countries[(int) $pais->codigoPais] = $pais->nombre;
        }

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);

        $q = $adb->pquery("SELECT id, user_name AS nombre 
							FROM vtiger_users
							WHERE deleted = 0");
        $usuarios = array();
        foreach ($q as $row) {
        	$usuarios[$row['id']] = $row['nombre'];
        }
        $viewer->assign('USUARIOS', $usuarios);
        $viewer->assign('COUNTRIES', $countries);

        //Include special script and css needed for this widget
        $viewer->assign('CURRENTUSER', $currentUser);

        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/Prototipo.tpl', $moduleName);
        }
    }
}
