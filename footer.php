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
<script src="js/main.js"></script>
</body>

</html> 