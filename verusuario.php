<?php

/*archivo que se crea con la finalidad de poder retornar una url en la operación DarUrl del ws_call.php
dado que enviar un '&' en el xml genera polémica, opté por generar una url que no use '&' y lo redirija*/

$id = $_REQUEST['record'];

header("Location: index.php?module=Accounts&view=Detail&record={$id}&app=SUPPORT");
exit();
