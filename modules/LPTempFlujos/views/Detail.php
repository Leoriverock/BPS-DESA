<?php

class LPTempFlujos_Detail_View extends Vtiger_Detail_View {

    public function process(Vtiger_Request $request) {
		global $adb, $log;
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        if(!$recordModel){
            if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            }
        }
        if ($recordId){
            // cargar los valores de los nodos
            $campo_modq = $adb->pquery("SELECT tf_campo_mod, tf_modulo FROM vtiger_lptempflujos WHERE lptempflujosid=?", array($recordId));
            $tf_campo_mod = $adb->query_result($campo_modq, 0, 'tf_campo_mod');
            $tf_modulo = $adb->query_result($campo_modq, 0, 'tf_modulo');
            $campos_valores=array();
            $nombres_nodos = array();
            try{
                $query_valores = $adb->query("SELECT $tf_campo_mod, sortorderid FROM vtiger_$tf_campo_mod");
                foreach($query_valores as $v) {
                    $nombres_nodos[]=$v[$tf_campo_mod];
                    $campos_valores[] = array(
                        'data'=> array (
                            'id' => $v[$tf_campo_mod],
                            'order' => $v['sortorderid'],
                            'name' => vtranslate($v[$tf_campo_mod], $tf_modulo)
                        )
                    );
                }
            } catch(Exception $e){ }
            // cargar los flujos entre los valores validos
            $edgesq = $adb->pquery(
                "SELECT * 
                FROM vtiger_lptempflujocambios
                JOIN vtiger_crmentity ON crmid=lptempflujocambiosid AND NOT deleted
                WHERE tfc_template = ?",
                array($recordId)
            );
            $edges = array();
            foreach($edgesq as $edge){
                // verificar que las conecciones esten entre los nodos, si no va a fallar la visualizacion
                // esto puede pasar si se cambia el campo_mod (algo que no deberia pasar, mucho por las restricciones)
                if (in_array( $edge['tfc_origen'],$nombres_nodos) && 
                    in_array( $edge['tfc_destino'],$nombres_nodos) ){                        
                    $edges[] = array(
                        'data' => array(
                            "id" => $edge['lptempflujocambiosid'],
                            "template" => $edge['tfc_template'],
                            "color" => $edge['tfc_color'],
                            "comentario" => $edge['tfc_comentario'],
                            "etiqueta" => $edge['tfc_etiqueta'],
                            "server" => true, 
                            "source" => $edge['tfc_origen'],
                            "target" => $edge['tfc_destino'],
                        )
                    );
                }
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('CAMPOS_VALORES', $campos_valores);
            $viewer->assign('edges', $edges);
        } else  $log->debug(array("LPTempFlujos_Detail_View Nuuu", $recordId, $moduleName));

		parent::process($request);
    }

}