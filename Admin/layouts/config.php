<?php
define('DB_SERVER', 'att-lab-viectute-cbfd.a.aivencloud.com');
define('DB_USERNAME', 'avnadmin');
define('DB_PASSWORD', 'AVNS_3pecshNeArlR1NgalJS');
define('DB_NAME', 'DProxy');
define('DB_PORT', '16456');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

if ($link === false) {
    die("ERROR: Could not connect to the database server." . mysqli_connect_error());
}

$gmailid = ''; // YOUR gmail email
$gmailpassword = ''; // YOUR gmail password
$gmailusername = ''; // YOUR gmail User name

?>