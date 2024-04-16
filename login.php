<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link href="css/style.css" rel="stylesheet">
<title>ログイン</title>
</head>
<body>

<header class="header">
    <?php include("inc/menu.php");?>
    <?php 
    // ログインしている場合、ユーザー名を表示
    if(isset($_SESSION["user_name"])) {
        echo $_SESSION["user_name"] . "さん";
    }
    ?>
</header>

<!-- lLOGINogin_act.php は認証処理用のPHPです。 -->
<form name="form1" action="login_act.php" method="post">
会員ID：<input type="text" name="lid"><br>
パスWD:<input class="input_pw" type="password" name="lpw">
<input type="submit" value="ログイン">
<p>初めての方は登録から</p>
<a href="user.php">ユーザー登録</a>

</form>
<footer class="footer">
<?php include("inc/foot.html"); ?>
</footer>


</body>
</html>