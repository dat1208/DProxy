<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  // write the input to the blocked_sites.txt file
  $output = shell_exec('echo ' . escapeshellarg($editor) . ' > /etc/squid/blocked_sites.txt');
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>