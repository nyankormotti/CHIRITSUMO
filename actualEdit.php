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

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">実績記載</h1>
        <!-- main -->
        <form action="" method="post" class="form form-actualedit" enctype="multipart/form-data">
            <div class="area-msg">

            </div>
            <label class="<?php if (!empty($err_msg['a_title'])) echo 'err'; ?>">
                タイトル<span class="label-require">必須</span>
                <input type="text" name="a_title" value="">
            </label>
            <div class="area-msg">

            </div>

            <label class="<?php if (!empty($err_msg['a_date'])) echo 'err'; ?>">
                実施日<span class="label-require">必須</span>
                <input type="text" name="a_title" value="" class="calender">
            </label>
            <div class="area-msg">

            </div>

            <label class="a_time-form <?php if (!empty($err_msg['a_time'])) echo 'err'; ?>">
                実施時間<span class="label-require">必須</span>
                <input type="text" name="a_time" value="" class="a_time-input">
                <p class="a_time-parts">時間</p>

            </label>
            <div class="area-msg">

            </div>

            <label class="a_category-form <?php if (!empty($err_msg['a_category'])) echo 'err'; ?>">
                カテゴリー<span class="label-require">必須</span>
                <div class="selectbox">
                    <span class="icn_select"></span>
                    <select name="c_id" id="">
                        <option value="0">選択してください</option>
                    </select>
                </div>
            </label>
            <div class="area-msg">

            </div>

            <label class="form-group">
                <p class="comment">内容
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="help-block">※200文字以内にてご入力ください</span></span>
                </p>
                <textarea class="" name="comment" id="count-contact-text" cols="63" rows="8" value="<?php if (!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
                <div class="comment-su"><span class="comment-count">0</span><span>/ 200</span></div>

            </label>

            <div style="overflow:hidden;">
                <div class="imgDrop-container">
                    画像１
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic1" class="input-file" style="min-height:140px">
                        <img src="" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
                <div class="imgDrop-container">
                    画像２
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic2" class="input-file" style="min-height:140px">
                        <img src="" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
                <div class="imgDrop-container">
                    画像３
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic3" class="input-file" style="min-height:140px">
                        <img src="" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
            </div>

            <div class="btn-contner">
                <input type="submit" name="submit" class="btn btn-mid" value="実績記載">
            </div>
        </form>
    </section>



    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 