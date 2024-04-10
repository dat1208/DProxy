<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  // Lưu dữ liệu từ người dùng vào một tệp, giữ an toàn bằng PHP thay vì cung cấp quyền cho người dùng thực hiện các lệnh shell
  file_put_contents('/etc/squid/blocked_sites.txt', $editor);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>