<?php
session_start();
if(empty($_SESSION['name'])){
    header('location: ./');
    exit;
}


$name = $_SESSION['name'];
$clean_name = "";
$clean_text = "";
$img_name = "";
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

if(!empty($_POST['text']) || isset($_FILES['datafile'])){
    if (isset($_FILES['datafile'])) {
        $tempfile = $_FILES['datafile']['tmp_name'];
        $filename = $_FILES['datafile']['name'];
        if (is_uploaded_file($tempfile)) {
            $f_name = 'img/'.$filename;
            if (move_uploaded_file($tempfile, 'img/'.$filename)) {
                $img_name = "<a href=\"$f_name\" data-lightbox=\"group\"><img src=\"$f_name\" width=\"70px\" height=\"70px\"></a>";
            }
        }
    }
    

    $clean_name = htmlspecialchars($name,ENT_QUOTES);
    $clean_text = htmlspecialchars($_POST['text'], ENT_QUOTES);
    $json = json_encode(array(
        "name" => $clean_name,
        "text" => $clean_text,
        "img" => $img_name
    ), JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare("INSERT INTO line (text) VALUES (:text)");
    $stmt->bindParam(':text', $json, PDO::PARAM_STR);
    $stmt->execute();
    echo $json;
}else{
    echo 1;
}


?>