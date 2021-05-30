<?php
session_start();
$message = null;
if(!empty($_POST['submit'])){
    if(!empty($_POST['mail']) && !empty($_POST['password'])){
        if($_POST['mail'] == "admin@admin.com" && $_POST['password'] == "admin"){
            $_SESSION['admin'] = true;
            $message = 1;
        }else{
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
            $clean_mail = htmlspecialchars($_POST['mail'], ENT_QUOTES);
            $clean_password = htmlspecialchars($_POST['password'], ENT_QUOTES);
            $stmt = $pdo->prepare("SELECT * FROM user WHERE mail = \"$clean_mail\";");
            $stmt->execute();
            $user_check = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($user_check)){
                if(password_verify($clean_password, $user_check['password']) === true){
                    $_SESSION['name'] = htmlspecialchars($user_check['name'], ENT_QUOTES);
                    $message = 2;
                }
            }else{
                $message = 3;
            }
        }
    }else{
        $message = 4;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/index.css">
    <title>ログイン画面</title>
</head>
<body>
    <h1>ログイン</h1>
    <div class="admin">
        <h2>管理者画面</h2>
        <p>mail : admin@admin.com</p>
        <p>password : admin</p>
    </div>
    <form action="" method="post">
        <input type="text" name="mail" placeholder="mail" size="50"><br>
        <input type="password" name="password" placeholder="password" size="50"><br>
        <button class="btn-info btn-lg" type=submit name="submit" value="submit">ログイン</button>
    </form>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <?php if(!empty($message)): ?>
        <?php if($message == 1): ?>
            <script>
                swal('ログイン成功','管理者としてログインしました。','success')
                .then(function(){
                    window.location.href = "./admin.php";
                })
            </script>
        <?php elseif($message == 2):?>
            <script>
                swal('ログイン成功','<?= $_SESSION['name']?>でログインしました。','success')
                .then(function(){
                    window.location.href = "./talk.php"
                })
            </script>
        <?php elseif($message == 3): ?>
            <script>
                swal('ログイン失敗','メールアドレスまたは、パスワードが間違っています','error');
            </script>
        <?php else: ?>
            <script>
                swal('入力エラー','未入力の項目があります。','error');
            </script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>