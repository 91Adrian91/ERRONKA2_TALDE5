<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conx = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);
mysqli_set_charset($conx, 'utf8mb4');
