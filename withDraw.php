<?php
 // 共通関数の読み込み
require('function.php');

// debug('=======================');
// debug('退会ページ');
// debug('=======================');
// debugLogStart();

// ログイン認証
require('auth.php');

// =================
// 画面処理
// =================

// post送信されていた場合
if (!empty($_POST)) {
    // debug('POST送信があります。');

    // 例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql1 = 'UPDATE user SET delete_flg = 1 WHERE id = :us_id';
        $sql2 = 'UPDATE performance SET delete_flg = 1 WHERE user_id = :us_id';
        $sql3 = 'UPDATE category SET delete_flg = 1 WHERE user_id  = :us_id';
        // データ流し込み
        $data = array(':us_id' => $_SESSION['user_id']);
        // クエリ実行
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);
        $stmt3 = queryPost($dbh, $sql3, $data);

        // クエリ実行結果が成功の場合
        if ($stmt1) {
            // セッションを削除
            session_destroy();
            // debug('セッション変数の中身：' . print_r($_SESSION, true));
            // debug('トップページへ遷移します。');
            header("Location:index.php");
        } else {
            // debug('クエリが失敗した。');
            $err_msg['common'] = MSG07;
        }
    } catch (Exception $e) {
        error_log('エラー発生' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

// debug('画面表示処理終了============');

?>

<?php
$siteTitle = '退会';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">退会</h1>
        <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
        </div>

        <div class="form">
            <form action="" method="post" class="form contact-form">
                <div class="withdraw-comment">
                    <p class="pass-withdraw">退会します。<br>
                        よろしいですか？</p>
                </div>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn-mid btn-withdraw" value="退会する"><br>
                </div>
            </form>

        </div>
    </section>



    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 