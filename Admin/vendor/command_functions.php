<?php
function restartSquid() {
    exec("systemctl restart squid");
}

if(isset($_GET['restart_squid'])) {
    restartSquid();
    echo "Dịch vụ Squid đã khởi động lại thành công.";
}

function updateSystem() {
    exec("sudo apt-get update");
}

function checkDiskUsage() {
    exec("df -h", $output);
    return $output;
}
?>