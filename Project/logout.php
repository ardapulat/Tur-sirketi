<?php
require_once 'config.php';

// Session'ı temizle
session_unset();
session_destroy();

// Ana sayfaya yönlendir
header('Location: index.php');
exit;
?>

