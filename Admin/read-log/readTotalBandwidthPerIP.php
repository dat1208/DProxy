<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('python log.py -a');
    }
    echo $output;
?>