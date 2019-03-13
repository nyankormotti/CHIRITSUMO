$(function(){

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
});