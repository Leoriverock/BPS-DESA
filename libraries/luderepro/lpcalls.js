//si no hay usuario logueado no se hace req
//luego de 5 errores se desubscribe el proceso
jQuery.ajax({ url: 'config.ludere.js', dataType: 'script', async: false }); // Configuraciones de LuderePro para JS
let lpCallsIntervalId;
let lpCallsErrorCounter = 0;
jQuery(document).on('ready', () => {
    lpCallsErrorCounter = 0;
    localStorage.clear();
    //ControldeGrupo();
    if ($('#linkToCall').length)
        lpCallsIntervalId = setInterval(getActiveCalls, IntervalActiveCalls);
     
    
});



function getActiveCalls() {
    var ahora = Date.now(); 
    console.log("ahora: "+ahora);
    var ultima = localStorage.getItem('tiempoUltima'); 
    console.log("tiempoUltima: "+ultima);
    var tiempoUltimaEjecutando = localStorage.getItem('tiempoUltimaEjecutando'); 
    console.log("tiempoUltimaEjecutando: "+tiempoUltimaEjecutando);

    console.log(localStorage);
    var ejecutandoSinLeer = !!localStorage.getItem('ejecutandoSinLeer');


    if(!ejecutandoSinLeer && (!ultima || (ahora - ultima) > IntervalActiveCalls)){
            localStorage.setItem('tiempoUltimaEjecutando', ahora)
            localStorage.setItem('ejecutandoSinLeer', true)
            if (typeof app.getUserId() !== 'undefined') {
            app.request.get({
                data: {
                    module: 'Calls',
                    action: 'GetActiveCall'
                }
                }).then(function(error, data) {
                    console.log("do it");
                        
                        
                    
                    if (error || !data) {
                        if (lpCallsErrorCounter > 5) {
                            clearInterval(lpCallsIntervalId);
                        }
                        lpCallsErrorCounter++;
                    }
                    console.log(data);
                    if (data && data.success && !data.result) {
                        console.log("entra 1");
                        jQuery('#lblcallphone').text("Sin llamadas");
                        jQuery('#linkToCall').removeAttr('href');

                    } else {
                        console.log("entra 2");
                        localStorage.setItem('tiempoUltima', ahora);
                        localStorage.setItem('result', JSON.stringify(data));
                        jQuery('#lblcallphone').text(data.callphonenumber);
                        document.getElementById('linkToCall').href = data.callurl;
                    }
                });
                localStorage.removeItem('ejecutandoSinLeer');
            }//Fin ejex ajax

        }//Fin de if       
        else{
            
            var result = JSON.parse(localStorage.getItem('result'));
            console.log("else");

            console.log(result.callphonenumber);
            console.log(result.result);
            console.log(result.callphonenumber === '');
            console.log(result.result === null);
            if(result.callphonenumber === '' || result.result === null){
                console.log("entra 3");
                jQuery('#lblcallphone').text("Sin llamadas");
                jQuery('#linkToCall').removeAttr('href');
            }else{
                console.log("entra 4");
                console.log(result);
                //if(result != null){
                    jQuery('#lblcallphone').text(result.callphonenumber);
                    document.getElementById('linkToCall').href = result.callurl;
                //}
               
            }
            //

        }
        //getActiveCalls();
}

