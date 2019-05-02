<?php
// 共通関数呼び出し
require('function.php');

// debug('=========================');
// debug('トップページ');
// debug('=========================');
// debugLogStart();
// ログイン認証
require('authIndex.php');

?>


<?php
$siteTitle = 'トップ';
require('head.php');
?>

<body>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 id="index-title">IN DREAMS BEGIN THE RESPONSIBILITIES</h1>
        <h2 id="index-subtitle">夢の中で、キミが始まる</h2>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>