<?php
// Tokiko laborategiko balioak; Docker/CI-k ingurune-aldagaiak erabiltzen ditu.
$mysql_host = getenv('DB_HOST') ?: 'localhost';
$mysql_port = (int) (getenv('DB_PORT') ?: 3306);
$mysql_user = getenv('DB_USER') ?: 'root';
$mysql_pass = getenv('DB_PASSWORD') === false ? '' : getenv('DB_PASSWORD');
$mysql_db = getenv('DB_NAME') ?: 'BDweb';
$enpresa = 'Arrue Hortz Sintetikoak';
$version = '1.1';
