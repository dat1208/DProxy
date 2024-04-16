<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  
  $result = str_replace(['<p>', '</p>', '<br>', '&nbsp;'], "\n", $editor);
  // write the input to the blocked_sites.txt file
  file_put_contents('/etc/squid/blocked_sites.txt', $result);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>