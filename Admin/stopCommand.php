<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('sudo systemctl stop squid');
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>