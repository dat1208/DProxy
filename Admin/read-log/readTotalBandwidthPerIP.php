<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = 'python /root/log.py -a 2>&1';
    $output = shell_exec($command);
    echo "<p>$output</p>";
}
?>