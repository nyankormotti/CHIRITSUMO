<?php
// 共通関数を読み込み
require('function.php');

// debug('==============================');
// debug('ログアウトページ');
// debug('==============================');
// debugLogStart();

// セッションを削除(ログアウト)
session_destroy();
// ログインページへ
header("Location:login.php");
?>