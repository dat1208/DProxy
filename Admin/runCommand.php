<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('ls'); 
        echo "<pre>$output</pre>";
    }
?>