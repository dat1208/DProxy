<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  // Làm sạch và loại bỏ các thẻ HTML
  $editor = strip_tags(htmlspecialchars_decode($editor, ENT_QUOTES));
  // Ghi nội dung vào file
  file_put_contents('/path/to/your/file.txt', $editor);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>