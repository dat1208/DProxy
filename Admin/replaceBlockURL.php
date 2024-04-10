<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // thu thập dữ liệu form
  $editor = $_POST['editor'];
  // thay thế thẻ </p> với ký tự xuống dòng
  $editor = str_replace('</p>', "\r\n", $editor);
  // Loại bỏ các thẻ HTML còn lại
  $editor = strip_tags($editor);
  // Ghi nội dung vào file
  file_put_contents('/path/to/your/file.txt', $editor);
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>