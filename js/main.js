$(function(){

    // メッセージの宣言
    const MSG_CONTACT_COUNT_ERR = '200文字以内で入力してください。';
    const MSG_CONTACT_COUNT = '※200文字以内にてご入力ください';

    // headerを固定
    // var $htr = $('header');
    // $htr.attr({ 'style': 'position:fixed; top:0px;z-index:5;'});

    // メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s ]+|[\s ]+$/g,"").length) {
        $jsShowMsg.slideToggle('slow');
        setTimeout(function () { $jsShowMsg.slideToggle('slow'); }, 5000);
    }

    // コメントカウント(お問い合わせ画面)
    $('#count-contact-text').keyup(function(){
        // 入力値の文字列長を取得
        var count = $(this).val().length;
        // 文字列長を画面に出力
        $('.comment-count').text(count);

        // 親要素のDOMを取得
        var form_g = $(this).closest('.form-group');

        if(count > 200) {
            form_g.find('#count-contact-text').addClass('err');
            form_g.find('.help-block').addClass('area-msg');
            form_g.find('.help-block').text(MSG_CONTACT_COUNT_ERR);
            $('.btn-mid').prop("disabled",true);
            $('.btn-mid').addClass('inactive');
            
        } else {
            form_g.find('#count-contact-text').removeClass('err');
            form_g.find('.help-block').removeClass('area-msg');
            form_g.find('.help-block').text(MSG_CONTACT_COUNT);
            $('.btn-mid').prop("disabled", false);
            $('.btn-mid').removeClass('inactive');
        }
    });

    // カレンダー表示
    $.datepicker.setDefaults($.datepicker.regional["ja"]);
    $('.calender').datepicker();

    // 画像ドラッグ＆ドロップ
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });
    $fileInput.on('change', function (e) {
        $dropArea.css('border', 'none');
        var file = this.files[0],
            $img = $(this).siblings('.prev-img'),
            fileReader = new FileReader();

        fileReader.onload = function (event) {

            $img.attr('src', event.target.result).show();
        };

        fileReader.readAsDataURL(file);
    });
});