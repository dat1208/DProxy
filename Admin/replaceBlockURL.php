<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  // Loại bỏ các thẻ HTML
  $editor = strip_tags($editor);
  // write the input to the blocked_sites.txt file
  file_put_contents('/etc/squid/blocked_sites.txt', $editor);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>