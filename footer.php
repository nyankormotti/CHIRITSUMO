<footer>
    Copyright
    <?php
    if (empty($_SESSION['user_id'])) {
        ?>
        <a href="index.php">nyanko_R_motti</a>
    <?php

} else {
    ?>
        <a href="mypage.php">nyanko_R_motti</a>
    <?php

}
?>
    .All Rights Reserved
</footer>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
<script src="js/main.js"></script>
</body>

</html>