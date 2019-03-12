<?php
 // 共通関数呼び出し
require('function.php');

debug('=========================');
debug('トップページ');
debug('=========================');
debugLogStart();
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

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 id="index-title">IN DREAMS BEGIN THE RESPONSIBILITIES</h1>
        <h2 id="index-subtitle">夢の中で、キミが始まる</h2>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 