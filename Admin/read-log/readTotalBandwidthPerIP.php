<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('python /root/log.py -a');
    }
    echo "<pre>$output</pre>";
?>