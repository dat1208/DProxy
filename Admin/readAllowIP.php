<?php
    $file_path = '/etc/squid/allowed_ips.txt';

    if(file_exists($file_path)) {
        $content = file_get_contents($file_path);
        $content = nl2br($content);  // Chuyển đổi ký tự xuống dòng thành thẻ <br>
        echo $content;
    } else {
        echo "File không tồn tại.";
    }
?>