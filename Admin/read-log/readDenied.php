<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('sudo python /root/log.py -d');
        echo "<p>$output</p>";
    }
?>