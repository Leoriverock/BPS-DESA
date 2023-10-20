<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
 
$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
echo "<center>
        <p>YOUR HostName is ".$host." </p>
    </center>";
 ?> 
 </body>
</html>