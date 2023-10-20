<?php

class LudereProUsers_UserSetup_View extends Users_UserSetup_View
{

    public function process(Vtiger_Request $request)
    {
    	//se quita el redireccionamiento a la pantalla de conf luego del primer login
        if (isset($_SESSION['return_params'])) {
            $return_params = urldecode($_SESSION['return_params']);
            //para evitar bug con req de llamadas activas cuando se cierra o caduca la sesion y se vuelve a iniciar
            if ($return_params === 'module=Calls&action=GetActiveCall') {
                header("Location: index.php");
                exit();
            }
            header("Location: index.php?$return_params");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }
    }
}
