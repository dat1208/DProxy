<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // thu thập dữ liệu form
   $editor = $_POST['editor'];
   // Loại bỏ các thẻ HTML
   $editor = strip_tags($editor);
   // Thay thế các thẻ <p> và </p> với ký tự xuống dòng
   $editor = str_replace(array('<p>', '</p>'), "\n", $editor);
   // Ghi nội dung vào file
  file_put_contents('/etc/squid/blocked_sites.txt', $editor);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>