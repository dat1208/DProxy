<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = 'python /home/log.py -d 2>&1';
    $output = shell_exec($command);
    echo "<pre>$output</pre>";
}
?>