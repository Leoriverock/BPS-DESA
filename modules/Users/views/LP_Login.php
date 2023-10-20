<?php

class LudereProUsers_Login_View extends Users_Login_View
{

    public function process(Vtiger_Request $request)
    {
        $finalJsonData = array();
        $viewer        = $this->getViewer($request);

        $mailStatus = $request->get('mailStatus');
        $error      = $request->get('error');
        $message    = '';
        if ($error) {
            switch ($error) {
                case 'login':$message = 'Usuario y/o contraseña incorrectos';
                    break;
                case 'fpError':$message = 'Usuario y/o email incorrectos';
                    break;
                case 'statusError':$message = 'Servidor de correos no configurado';
                    break;
            }
        } else if ($mailStatus) {
            $message = 'Mail has been sent to your inbox, please check your e-mail';
        }

        $viewer->assign('ERROR', $error);
        $viewer->assign('MESSAGE', $message);
        $viewer->assign('MAIL_STATUS', $mailStatus);
        $viewer->view('Login.tpl', 'Users');
    }
}
