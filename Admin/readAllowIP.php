<?php
    $file_path = '/etc/squid/allowed_ips.txt';

    if(file_exists($file_path)) {
        $content = file_get_contents($file_path);

        echo $content;
    } else {
        echo "File không tồn tại.";
    }
?>