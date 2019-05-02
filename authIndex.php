<?php
// =====================
// ログイン認証・自動ログアウト
// =====================
// ログインしている場合
if(!empty($_SESSION['user_id'])) {
    // debug('ログイン済みユーザーです。');

    // 現在日時が最終ログイン日時＋有効期限を超えていた場合
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        // debug('ログイン有効期限オーバーです。');

        // セッションを削除(ログアウトする)
        session_destroy();
        // ログインページへ
        header("Location:index.php");
    }else{
        // debug('ログイン有効期限切れです。');
        // 最終ログイン日時を現在日時に更新
        $_SESSION['login_date'] = time();

        if(basename($_SERVER['PHP_SELF']) === 'index.php'){
            // debug('マイページへ遷移します。');
            header("Location:mypage.php");
        }
    }
} else {
    // debug('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header("Location:index.php");
    }
}
