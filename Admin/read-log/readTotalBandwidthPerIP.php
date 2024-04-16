<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $output = shell_exec('python /root/log.py -a');
        $output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);  // Loại bỏ mã điều khiển ANSI
        $output = nl2br($output);  // Chuyển đổi ký tự xuống dòng thành thẻ <br>
    }
    echo $output;
?>