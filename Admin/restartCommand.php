<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('sudo systemctl restart squid');
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);

?>