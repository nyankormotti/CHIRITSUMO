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
    <section class="main mypage">

        <div id="mycontent">
            <!-- サイドバー -->
            <div id="side-bar">
                <div class="profile-form">
                    <div class="profile-img-form">
                        <img class="profile-img" src="" alt="">
                    </div>
                    <div class="profile-text">
                        <p>〇〇さん</p>
                        <p><span>〇〇〇〇</span><span>年</span><span>〇〇</span><span>月</span><span>〇〇</span><span>日</span></p>
                        <p>より継続開始！！</p>
                        <p>継続開始より<span>◯◯</span>日目</p>
                        <p>累計時間：<span>◯◯</span>時間</p>
                    </div>
                </div>

                <div class="search-form">

                </div>

            </div>

            <!-- 実績結果 -->
            <section id="active-result">
                <div class="active-field">
                    <div class="title-bar">
                        <div class="title-bar2">
                            <h2 class="mytitle">実績一覧</h2>
                            <!-- 検索チェックボックス -->
                            <div class="search-form-button">
                                <div class="serch-form-button2 ol-searchform">
                                    <input id="ol-search" type="checkbox">
                                    <label for="ol-search" class="search-icon"></label>
                                    <div class="ol-search-wrap">
                                        <label for="ol-search" class="overlay"></label>
                                        <!--検索フォーム-->
                                        <form role="search" method="get" class="searchform" action="#">
                                            <h1 class="search-title">検索フォーム</h1>
                                            <p class="search-category search-input">カテゴリー</p>
                                            <div class="selectbox">
                                                <span class="icn_select"></span>
                                                <select name="c_id" id="">
                                                    <option value="0">選択してください</option>
                                                </select>
                                            </div>
                                            <p class="search-sort search-input">表示順</p>
                                            <div class="selectbox">
                                                <span class="icn_select"></span>
                                                <select name="sort" id="">
                                                    <!-- <option value="0">選択してください</option> -->
                                                    <option value="1">新しい順</option>
                                                    <option value="2">古い順</option>
                                                </select>
                                            </div>
                                            <p class="search-day search-input">期間&nbsp;&nbsp;<span class="search-input-valid">※半角数字で入力してください</span></p>

                                            <div class="input-day">
                                                <input type="text" class="search-field" name="start-year" placeholder="">
                                                <p class="start-year-parts">年</p>
                                                <input type="text" class="search-field start-month" name="start-month" placeholder="">
                                                <p class="start-month-parts">月&nbsp;&nbsp;〜</p>
                                                <input type="text" class="search-field end-year" name="end-year" placeholder="">
                                                <p class="end-year-parts">年</p>
                                                <input type="text" class="search-field end-month" name="start-month" placeholder="">
                                                <p class="end-month-parts">月</p>
                                            </div>




                                            <input class="search-button" type="submit" value="検索">
                                            <!-- <button type="submit" class="search-submit">
                                                <span class="fa fa-search fa-fw"></span>
                                            </button> -->
                                        </form>
                                        <!--検索フォーム-->
                                    </div>
                                </div>
                            </div>


                            <div class="kensu">
                                <span class="total-num">〇〇</span><span>件のうち </span>
                                <span class="num">〇〇</span><span> - </span>
                                <span>〇〇</span><span>件表示</span>
                            </div>
                        </div>
                    </div>

                    <!-- 実積記録一覧 -->
                    <div class="active-list-aria">
                        <div class="active-parts">
                            <!-- 画像 -->
                            <div class="active-img-form">
                                <img class="active-img" src="" alt="">
                            </div>
                            <!-- 詳細 -->
                            <div class="active-detail">
                                <!-- タイトル -->
                                <h2 class="active-title">PHPを勉強したよーーーーーーーーーー！！！！！！！！！アイウエオかきくけこ</h2>
                                <!-- パラメータ -->
                                <div class="active-para">
                                    <div class="active-day">
                                        <p>実施日</p>
                                        <p><span>〇〇〇〇</span><span>年</span><span>〇〇</span><span>月</span><span>〇〇</span><span>日</span></p>
                                    </div>
                                    <div class="active-time">
                                        <p>実施時間</p>
                                        <p><span>〇〇</span><span>時間</span></p>
                                    </div>
                                    <div class="active-category">
                                        <p>カテゴリー</p>
                                        <p>にゃんころもっち！！</p>
                                    </div>
                                </div>

                                <!-- リンク -->
                                <div class="active-link">
                                    <a href="" class="active-link-left">続きを読む</a>
                                    <a href="" class="active-link-right">編集</a>
                                </div>
                            </div>
                        </div>
                        <div class="active-parts">
                            <!-- 画像 -->
                            <div class="active-img-form">
                                <img class="active-img" src="" alt="">
                            </div>
                            <!-- 詳細 -->
                            <div class="active-detail">
                                <!-- タイトル -->
                                <h2 class="active-title">無駄無駄無駄無駄無駄無駄無駄無駄ぁぁ！！！</h2>
                                <!-- パラメータ -->
                                <div class="active-para">
                                    <div class="active-day">
                                        <p>実施日</p>
                                        <p><span>〇〇〇〇</span><span>年</span><span>〇〇</span><span>月</span><span>〇〇</span><span>日</span></p>
                                    </div>
                                    <div class="active-time">
                                        <p>実施時間</p>
                                        <p><span>〇〇</span><span>時間</span></p>
                                    </div>
                                    <div class="active-category">
                                        <p>カテゴリー</p>
                                        <p>ジョルノ</p>
                                    </div>
                                </div>

                                <!-- リンク -->
                                <div class="active-link">
                                    <a href="" class="active-link-left">続きを読む</a>
                                    <a href="" class="active-link-right">編集</a>
                                </div>
                            </div>
                        </div>
                        <div class="active-parts">
                            <!-- 画像 -->
                            <div class="active-img-form">
                                <img class="active-img" src="" alt="">
                            </div>
                            <!-- 詳細 -->
                            <div class="active-detail">
                                <!-- タイトル -->
                                <h2 class="active-title">WRYYYYYYYYYYYYYYYYYYY!!!!!!</h2>
                                <!-- パラメータ -->
                                <div class="active-para">
                                    <div class="active-day">
                                        <p>実施日</p>
                                        <p><span>〇〇〇〇</span><span>年</span><span>〇〇</span><span>月</span><span>〇〇</span><span>日</span></p>
                                    </div>
                                    <div class="active-time">
                                        <p>実施時間</p>
                                        <p><span>〇〇</span><span>時間</span></p>
                                    </div>
                                    <div class="active-category">
                                        <p>カテゴリー</p>
                                        <p>DIO</p>
                                    </div>
                                </div>

                                <!-- リンク -->
                                <div class="active-link">
                                    <a href="" class="active-link-left">続きを読む</a>
                                    <a href="" class="active-link-right">編集</a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ページネイション -->
                    <div class="pagination">
                        <ul class="pagination-list">
                            <li class="list-item active"><a href="index.php?p=1">1</a></li>
                            <li class="list-item"><a href="index.php?p=2">2</a></li>
                            <li class="list-item"><a href="index.php?p=">&gt;</a></li>
                        </ul>
                    </div>
                </div>




            </section>
        </div>



    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 