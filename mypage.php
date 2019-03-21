<?php
 // 共通関数の読み込み
require('function.php');
debug('=================');
debug('マイページ');
debug('=================');
debugLogStart();

// ログイン認証
require('auth.php');

// =================
// 画面処理
// =================
// GETパラメータを取得
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? (int)$_GET['p'] : 1;
// DBからユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);
// カテゴリーID
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GER['sort'] : '';
// 表示件数
$listSpan = 20;
// 現在のレコード先頭を産出
$currentMinNum = (($currentPageNum - 1) * $listSpan);

$startDate = '';
$endDate = '';
$dbPerfData = getPerformanceAll($_SESSION['user_id'], $sort, $c_id, $startDate, $endDate, $currentMinNum);
// $dbTotalData = getTotalTime($_SESSION['user_id']);
debug('取得したユーザー情報：' . print_r($dbFormData, true));
debug('取得した実績情報：' . print_r($dbPerfData, true));
// debug('取得した累計時間情報：' . print_r($dbTotalData, true));

// プロフィール画像の有無判定
$userPic = '';
if (!empty($dbFormData['pic'])) {
    $userPic = $dbFormData['pic'];
} else {
    $userPic = 'img/sample-img.png';
}

$nowDate = strtotime(reset($dbPerfData['data'])['action_date']);
debug('取得日時：' . print_r(reset($dbPerfData['data'])['action_date'], true));
debug('現在時刻：' . strtotime(date('H:i:s')));
debug('現在時刻：' . strtotime(reset($dbPerfData['data'])['action_date']));
debug('差分：' . (strtotime(date('H:i:s')) - $nowDate));
// debug('累計時間：' . print_r(timeSum($dbPerfData),true));

// $total_hour = '';
// $total_minute = '';
// if ($dbTotalData) {
//     $total_hour = mb_substr($dbTotalData['total_time'], 0, 2);
//     $total_minute = mb_substr($dbTotalData['total_time'], 3, 2);
// }

// 
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
                        <img class="profile-img" src="<?php echo $userPic; ?>" alt="">
                    </div>
                    <div class="profile-text">
                        <p><?php echo $dbFormData['username']; ?>さん</p>
                        <?php
                        if ($dbPerfData) {
                            ?>

                        <p><span><?php echo mb_substr(reset($dbPerfData['data'])['action_date'], 0, 4) . ' 年 ' . mb_substr(reset($dbPerfData['data'])['action_date'], 5, 2) . ' 月 ' . mb_substr(reset($dbPerfData['data'])['action_date'], 8, 2) . ' 日 '; ?></span></p>
                        <p>より継続開始！！</p>

                        <p><?php echo '継続開始より ' . floor((strtotime(date('H:i:s')) - $nowDate) / (60 * 60 * 24)) . ' 日目 '; ?></p>
                        <!-- <p>累計時間：<span>¥ . ' 時間 ' . mb_substr($dbTotalData['total_time'], 3, 2), ' 分 '; ?></span></p> -->
                        <?php

                    } else {
                        ?>
                        <p>実績を記録しましょう！！</p>
                        <?php

                    }
                    ?>
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
                                            <p class="search-day search-input">期間&nbsp;&nbsp;<span>※開始年月と終了年月をご指定ください。</span></p>

                                            <div class="input-day">
                                                <!-- <input type="text" class="search-field" name="start-year" placeholder=""> -->
                                                <select name="start-year" id="" class="search-field">
                                                    <option value="0" selected></option>
                                                    <?php
                                                    for ($i = 2018; $i < 2031; $i++) {
                                                        ?>
                                                    <option value="<?php echo $i; ?>">
                                                        <?php
                                                        echo $i;
                                                        ?>
                                                    </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="start-year-parts">年</p>
                                                <!-- <input type="text" class="search-field start-month" name="start-month" placeholder=""> -->
                                                <select name="start-month" id="" class="search-field start-month">
                                                    <option value="0" selected></option>
                                                    <?php
                                                    for ($i = 1; $i < 13; $i++) {
                                                        ?>
                                                    <option value="<?php echo $i; ?>">
                                                        <?php
                                                        echo $i;
                                                        ?>
                                                    </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="start-month-parts">月&nbsp;&nbsp;〜</p>
                                                <!-- <input type="text" class="search-field end-year" name="end-year" placeholder=""> -->
                                                <select name="end-year" id="" class="search-field end-year">
                                                    <option value="0" selected></option>
                                                    <?php
                                                    for ($i = 2018; $i < 2031; $i++) {
                                                        ?>
                                                    <option value="<?php echo $i; ?>">
                                                        <?php
                                                        echo $i;
                                                        ?>
                                                    </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="end-year-parts">年</p>
                                                <!-- <input type="text" class="search-field end-month" name="end-month" placeholder=""> -->
                                                <select name="end-month" id="" class="search-field end-month">
                                                    <option value="0" selected></option>
                                                    <?php
                                                    for ($i = 1; $i < 13; $i++) {
                                                        ?>
                                                    <option value="<?php echo $i; ?>">
                                                        <?php
                                                        echo $i;
                                                        ?>
                                                    </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
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
                                <span class="total-num"><?php echo sanitize($dbPerfData['total']) . '件のうち '; ?></span>
                                <span class="num"><?php echo (!empty($dbPerfData['data'])) ? $currentMinNum + 1 : 0; ?></span><span> - </span>
                                <span><?php echo $currentMinNum + count($dbPerfData['data']); ?></span><span>件表示</span>
                            </div>
                        </div>
                    </div>

                    <!-- 実積記録一覧 -->
                    <div class="active-list-aria">

                        <?php
                        foreach ($dbPerfData['data'] as $key => $val) {
                            ?>
                        <div class="active-parts">
                            <!-- 画像 -->
                            <div class="active-img-form">
                                <img class="active-img" src="<?php if (!empty($val['pic1'])) {
                                                                    echo $val['pic1'];
                                                                } elseif (!empty($val['pic2'])) {
                                                                    echo $val['pic2'];
                                                                } elseif (!empty($val['pic3'])) {
                                                                    echo $val['pic3'];
                                                                } else {
                                                                    echo 'img/sample-img.png';
                                                                } ?>" alt="">
                            </div>
                            <!-- 詳細 -->
                            <div class="active-detail">

                                <!-- タイトル -->
                                <h2 class="active-title"><?php echo $val['title']; ?></h2>
                                <!-- パラメータ -->
                                <div class="active-para">
                                    <div class="active-day">
                                        <p>実施日</p>
                                        <p><?php echo mb_substr($val['action_date'], 0, 4) . ' 年 ' . mb_substr($val['action_date'], 5, 2) . ' 月 ' . mb_substr($val['action_date'], 8, 2) . ' 日 '; ?></p>
                                    </div>
                                    <div class="active-time">
                                        <p>実施時間</p>
                                        <p><?php echo mb_substr($val['action_time'], 0, 2) . ' 時間 ' . mb_substr($val['action_time'], 3, 2) . ' 分 '; ?></p>
                                    </div>
                                    <div class="active-category">
                                        <p>カテゴリー</p>
                                        <p><?php echo $val['category_name']; ?></p>
                                    </div>
                                </div>

                                <!-- リンク -->
                                <div class="active-link">
                                    <a href="" class="active-link-left">続きを読む</a>
                                    <a href="" class="active-link-right">編集</a>
                                </div>
                            </div>
                        </div>
                        <?php

                    }
                    ?>
                    </div>

                    <!-- ページネイション -->
                    <div class="pagination">
                        <ul class="pagination-list">
                            <li class="list-item active"><a href="index.php?p=1">1</a></li>
                            <li class="list-item"><a href="index.php?p=2">2</a></li>
                            <li class="list-item"><a href="index.php?p =">&gt;</a></li>
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