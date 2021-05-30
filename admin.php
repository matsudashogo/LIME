<?php
session_start();
if(empty($_SESSION['admin'])){
    header('location: ./');
    exit;
}
$message = null;
if(!empty($_POST['submit'])){
    if(!empty($_POST['name']) && !empty($_POST['mail']) && !empty($_POST['password'])){
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=board;charset=utf8',
                'root',
                'root',
                array(PDO::ATTR_EMULATE_PREPARES => false)
            );
        } catch (PDOException $e) {
            exit('データベース接続失敗。' . $e->getMessage());
        }
        $clean_name = htmlspecialchars($_POST['name'], ENT_QUOTES);
        $clean_mail = htmlspecialchars($_POST['mail'], ENT_QUOTES);
        $clean_password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES),PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT mail FROM user WHERE mail = \"$clean_mail\";");
        $stmt->execute();
        $mail_check = $stmt->fetch(PDO::FETCH_ASSOC);

        if(empty($mail_check)){
            $stmt = $pdo->prepare("INSERT INTO user (name,mail,password) VALUES (:name,:mail,:password)");
            $stmt->bindParam(':name', $clean_name, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $clean_mail, PDO::PARAM_STR);
            $stmt->bindParam(':password', $clean_password, PDO::PARAM_STR);
            $stmt->execute();
            $pdo = null;
            $message = 3;
        }else{
            $pdo = null;
            $message = 2;
        }
    }else{
        $message = 1;
        echo $message;
    }
}
if(!empty($_POST['logout'])){
    $_SESSION = array();
    session_destroy();
    $message = 4;
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <title>管理者画面</title>
</head>
<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <h1>管理者画面</h1>
    <h2>新規登録</h2>
    <form action="" method="post">
        <input type="text" name="name" placeholder="name" size="50"><br>
        <input type="mail" name="mail" placeholder="mail" size="50"><br>
        <input type="password" name="password" placeholder="password" size="50"><br>
        <button class="btn-info" type="submit" name="submit" value="submit">登録</button>
    </form>
    <form action="" method="post">
        <button class="btn-danger" type="submit" name="logout" value="logout">ログアウト</button>
    </form>
    <?php if(!empty($message)): ?>
        <?php if($message == 1): ?>
            <script>swal('入力エラー','未入力の項目があります。','error');</script>
            <?php elseif($message == 2): ?>
                <script>swal('登録エラー','既に登録されているメールアドレスです','error');</script>
            <?php elseif($message == 3): ?>
                <script>swal('登録が完了しました。','','success');</script>
            <?php else: ?>
                <script>
                swal('ログアウトしました。','','success')
                .then(function(){
                    window.location.href="./";
                })
                </script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>