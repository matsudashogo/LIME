<?php
session_start();
if(empty($_SESSION['name'])){
    header('location: ./');
    exit;
}
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
$stmt = $pdo->prepare("SELECT text FROM line;");
$stmt->execute();
while ($talk = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $talks[] = json_decode($talk['text'], true);
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/line.css">
    <title>LINE</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="d-flex align-items-center justify-content-center balloon3-right-btm">
                    LIME
                </div>
            </div>
            <div class="col-md-8">
                <div class="talk_area" id="talk_area">
                    <div class="d-flex justify-content-center flex-column">
                        <?php if (!empty($talks)) : ?>
                            <?php foreach ($talks as $value) : ?>
                                <?php if ($value['name'] == $_SESSION['name']) : ?>
                                    <div class="d-flex flex-column flex-row-reverse">
                                        <article class="my_talk" style="margin-top: 30px; margin-right: 50px;">
                                            <?php if (!empty($value['img'])) : ?>
                                                <?= $value['img']; ?>
                                            <?php endif ?>
                                            <p class="message"><?= $value['text']; ?></p>
                                        </article>
                                    </div>
                                <?php else : ?>
                                    <img src="img/human2.png" alt="" width="50px" height="50px" style="border-radius: 50%;">
                                    <p class="name"><?= $value['name']; ?></p>
                                    <article class="article">
                                        <?php if (!empty($value['img'])) : ?>
                                            <?= $value['img']; ?>
                                        <?php endif ?>
                                        <p class="message"><?= $value['text']; ?></p>
                                    </article>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <form action="" method="post">
                    <button class="logout btn-danger btn-lg" type="sutmit" name="logout" value="logout">ログアウト</button>
                </form>
            </div>
        </div>
        <div class="talk">
            <form id="send-form" action="" class="form-inline" method="post">
                <div class="form-group" style="margin-right: 20px; margin-top: 45px;">
                    <input type=text id="text" name="text"  placeholder="本文" wrap="hard" size="50" style="margin-top: 30px; position:absolute">
                </div>
                <div style="margin-top: 100px; margin-left: 480px; position:absolute">
                    <label>
                        <span class="filelabel" title="ファイルを選択">
                            <img src="img/camera2.png" width="32" height="26" alt="＋画像">
                            <span id="selectednum">選択</span>
                        </span>
                        <div class="img_size" style="width: 70px; height: 70px;">
                            <span id="previewbox"></span>
                        </div>
                        <input type="file" name="datafile" id="filesend" accept=".jpg,.gif,.png,image/gif,image/jpeg,image/png">
                    </label>
                </div>
                <button class="btn-info btn-lg" type="submit" id="submit" style="margin-top: 50px; margin-left: 1050px; position:absolute">送信</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <?php if(!empty($_POST['logout'])): ?>
        <script>
            swal('ログアウトしました','','success')
            .then(function(){
                <?php $_SESSION = array(); ?>
                <?php session_destroy(); ?>
                window.location.href = "./";
            })
        </script>
    <?php endif; ?>
    <script>
        $(function() {
            $('#talk_area').animate({
                scrollTop: $('#talk_area')[0].scrollHeight
            }, 1);
        })
        $('#send-form').submit(function(e) {
            e.preventDefault();
            var text = null;
            var text = $('#text').val();
            if (text != "") {
                var fd = new FormData();
                var fd = new FormData($('#send-form').get(0));
                fd.append('text', text);
                $.ajax({
                    url: 'data.php',
                    type: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'html'
                }).done(function(data) {
                    console.log(data);
                    const obj = JSON.parse(data);
                    $('#talk_area').append("<div class=\"d-flex flex-column flex-row-reverse\"><br>" + "<article class=\"my_talk\" style=\"margin-top: 30px; margin-right: 50px;\">" + obj['img'] + "<p class=\"message\">" + obj['text'] + "</p>" + "</article></div>");
                    $('#talk_area').animate({
                        scrollTop: $('#talk_area')[0].scrollHeight
                    }, 'slow');
                    event.preventDefault();
                    $('form').get(0).reset();
                    $(function() {
                        $('#talk_area').animate({
                            scrollTop: $('#talk_area')[0].scrollHeight
                        }, 1);
                    })
                    $('#previewbox').hide();
                    event.preventDefault();
                    $('form').get(0).reset();
                    $(function(){
                        $('#text').val("");
                    })
                });
            } else {
                swal("本文を入力してください。");
            }
            return false;
        });

        document.getElementById("filesend").addEventListener('change', function(e) {
            var files = e.target.files;
            previewUserFiles(files);
        });

        function previewUserFiles(files) {
            resetPreview();
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type.indexOf("image") < 0) {
                    continue;
                }
                var img = document.createElement("img");
                img.classList.add("previewImage");
                img.file = file;
                img.height = 100;
                document.getElementById('previewbox').appendChild(img);
                var reader = new FileReader();
                reader.onload = (function(tImg) {
                    return function(e) {
                        tImg.src = e.target.result;
                    };
                })(img);
                reader.readAsDataURL(file);
                $('#previewbox').show();
            }
        }

        function resetPreview() {
            var element = document.getElementById("previewbox");
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }
            document.getElementById("selectednum").innerHTML = "選択";
        }
    </script>
</body>

</html>