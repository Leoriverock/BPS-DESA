//si no hay usuario logueado no se hace req
//luego de 5 errores se desubscribe el proceso

let lpAttesIntervalId;
let lpAttesErrorCounter = 0;
jQuery(document).on('ready', () => {
    lpAttesErrorCounter = 0;
    //lpAttesIntervalId = setInterval(getActiveAttes, 2000);
});

function getActiveAttes() {
    if (typeof app.getUserId() !== 'undefined') {
        app.request.get({
            data: {
                module: 'AtencionPresencial',
                action: 'GetActiveAtte'
            }
        }).then(function(error, data) {
            if (error || !data) {
                if (lpAttesErrorCounter > 5) {
                    clearInterval(lpAttesIntervalId);
                }
                lpAttesErrorCounter++;
            }
            if (data && data.success && !data.result) {
                //console.log("entra al if");
                
                jQuery('#lblatte').text("Sin atenciones");
                jQuery('#linkToAtte').removeAttr('href');
                
                 
            } else {
                //console.log("entra al else");
                jQuery('#lblatte').text(data.ap_numero);
                //document.getElementById('linkToAtte').href = data.atencionpresencialurl;
            }
        });
    }
}

