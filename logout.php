<?php
// 共通関数を読み込み
require('function.php');

debug('==============================');
debug('ログアウトページ');
debug('==============================');
debugLogStart();

debug('ログアウトします。');
// セッションを削除(ログアウト)
session_destroy();
debug('ログインページに遷移します。');
// ログインページへ
header("Location:login.php");
?>