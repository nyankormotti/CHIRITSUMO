<?php
 // 共通関数の読み込み
require('function.php');
?>

<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<body class="category-body">
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main category-section">
        <h1 class="title">カテゴリー編集</h1>

        <div class="category-content">
            <!-- フォーム -->
            <section class="category-form">
                <!-- 登録フォーム -->
                <div class="category-regist category-form-part">
                    <p class="c-title">登録フォーム</p>
                    <p class="c-regist-title-valid">※15字以内で入力ください</p>
                    <input type="text" name="category_name" class="c_regist-input">
                    <input type="submit" name="submit" value="登録" class="c_regist-submit">
                </div>

                <!-- 削除フォーム -->
                <div class="category-delete category-form-part">
                    <p class="c-title">削除フォーム</p>
                    <div class="selectbox">
                        <span class="icn_select"></span>
                        <select name="c_id" id="">
                            <option value="0">選択してください</option>
                            <option value="1">にゃんこ</option>
                            <option value="2">JOJO</option>
                        </select>
                    </div>
                    <input type="submit" name="submit" value="削除" class="c_regist-submit">
                </div>
            </section>

            <!-- 一覧 -->
            <section class="category-list">
                <div class="category-list-area">
                    <p class="c-title">カテゴリー　一覧</p>
                    <div class="category-list-area-form">
                        <p class="c_object">アイウエオかきくけこさしすせそ</p>
                        <p class="c_object">JOJO</p>
                        <p class="c_object">にゃんこ</p>
                        <p class="c_object">1</p>
                        <p class="c_object">22222222</p>
                        <p class="c_object">アイウエオ</p>
                        <p class="c_object">アイウエオかきくけこさしすせそ</p>
                        <p class="c_object">アイウエオかきくけこさしすせそ</p>
                        <p class="c_object">huhuhuhuhu</p>
                        <p class="c_object">かきくけこさしすせそ</p>
                        <p class="c_object">アイウエオかきくけこさしすせそ</p>
                        <p class="c_object">アイウエオかきくけこさしすせそ</p>
                    </div>
                </div>

            </section>
        </div>
    </section>



    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 