<?php
 // 共通関数の読み込み
require('function.php');
?>

<?php
$siteTitle = 'マイページ';
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
        <h1 class="title">マイページ</h1>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 