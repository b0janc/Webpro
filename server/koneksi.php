<?php
$servername = 'localhost';
$user = 'root';
$passwd = '';
$db = 'db_nasgor';

$koneksi = (mysqli_connect($servername, $user, $passwd, $db));

if (!$koneksi) {
    echo 'fail';
}


?>