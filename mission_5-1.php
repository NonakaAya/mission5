<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
          integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
     <title>mission_5-1</title>
</head>
<body>
    <?php
        //データベースへの接続
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザ名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS tbtest2"
        ." ("
        . "postnumber INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "pw TEXT"
        .");";
        $stmt = $pdo->query($sql);

 
        //入力フォームの名前欄・コメント欄・パスワード欄が空欄でない場合
        if(!empty($_POST["name"])  && !empty($_POST["comment"]) && !empty($_POST["pw"])){

            //隠している編集確認用フォームが空でない場合＝編集の場合
            if(!empty($_POST["editcheck"])){
                $editnumber = $_POST["editcheck"];
                $editname = $_POST["name"];
                $editcomment = $_POST["comment"];
                $editdate = date("Y/m/d H:i:s");
                $editpw = $_POST["pw"];

                $sql = 'UPDATE tbtest2 SET name=:name, comment=:comment, date=:date, pw=:pw WHERE postnumber=:postnumber';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':postnumber', $editnumber, PDO::PARAM_INT);
                $stmt->bindParam(':name', $editname, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $editcomment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $editdate, PDO::PARAM_STR);
                $stmt->bindParam(':pw', $editpw, PDO::PARAM_STR);
                $stmt->execute();

                // 編集が完了したら変数を初期化
                $editname = "";
                $editcomment = "";
                $editpw = "";
                $editnumber = "";
            
            //隠している編集確認用フォームが空の場合＝新規投稿の場合
            } else {
                $sql = $pdo -> prepare("INSERT INTO tbtest2 (name, comment, date, pw) VALUES (:name, :comment, :date, :pw)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pw', $pw, PDO::PARAM_STR);
                
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $pw = $_POST["pw"];
                
                $sql -> execute();
            }
        }
            
        //削除フォームが空でない場合、以下の処理を行う
        if(isset($_POST["deletesubmit"])) {
            $deletenumber = $_POST["deleteNo"];
            $deletepw = $_POST["deletepw"];
            //指定した削除対象番号のパスワードを取得する
            $sql = 'SELECT pw FROM tbtest2 WHERE postnumber=:postnumber';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':postnumber', $deletenumber, PDO::PARAM_INT);
            $stmt->execute();
            // データベースから1行分のデータを配列として取得する
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // 投稿番号と削除対象番号、かつパスワードが一致したら削除
            if ($row && $row["pw"] == $deletepw){
                $sql = 'delete from tbtest2 where postnumber=:postnumber';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':postnumber', $deletenumber, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                echo "パスワードが一致しません";
            }
        }
                
        //編集フォームが空でない場合、以下の処理を行う
        if(isset($_POST["editsubmit"])) {
            $editnumber = $_POST["editNo"];
            $editpw = $_POST["editpw"];
            //指定した番号の名前、コメント、日付、パスワードを取得する
            $sql = 'SELECT name, comment, date, pw FROM tbtest2 WHERE postnumber=:postnumber';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':postnumber', $editnumber, PDO::PARAM_INT);
            $stmt -> execute();
            //データベースから1行分のデータを取得する
            $row = $stmt ->fetch(PDO::FETCH_ASSOC);
            //編集対象番号と投稿番号、そしてパスワードが一致したら、その投稿の名前、コメント、パスワードを取得
            if($row && $row["pw"] == $editpw){
                $editname = $row["name"];
                $editcomment = $row["comment"];
                $editdate = $row["date"];
                $editpw = $row["pw"];
            } else {
                echo "パスワードが間違っています";
            }
        }
    ?>

    <form action="" method="post">
        <!-- 入力フォーム -->
        <p><input type="text" name="name" placeholder="名前" value="<?php echo !empty($editname) ? $editname: ""; ?>"></p>
        <p><input type="text" name="comment" placeholder="コメント" value="<?php echo !empty($editcomment) ? $editcomment: ""; ?>"></p>
        <div class="password">
            <p>
                <input type="password" name="pw" placeholder="パスワード" value="<?php echo !empty($editpw) ? $editpw: ""; ?>">
                <i id="eye1" class="fa-solid fa-eye"></i>
            </p>
        </div>
        <p><input type="hidden" name="editcheck" value="<?php echo isset($_POST["editsubmit"]) ? $editnumber: ""; ?>"></p>
        <p><input type="submit" name="submit" value="投稿"></p>
        
        <!-- 削除フォーム -->
        <p><input type="number" name="deleteNo" placeholder="削除対象番号"></p>
        <div class="deletepassword">
            <p>
                <input type="password" name="deletepw" placeholder="パスワード">
                <i id="eye2" class="fa-solid fa-eye"></i>
            </p>
        </div>
        <p><input type="submit" name="deletesubmit" value="削除"></p>
        
        <!-- 編集フォーム -->
        <p><input type="number" name="editNo" placeholder="編集対象番号"></p>
        <div class="editpassword">
            <p>
                <input type="password" name="editpw" placeholder="パスワード">
                <i id="eye3" class="fa-solid fa-eye"></i>
            </p>
        </div>
        <p><input type="submit" name="editsubmit" value="編集"></p>
    </form>

    <script>
        //入力フォームのパスワードが表示・非表示と切り替えできるようにする
        let eye1 = document.getElementById("eye1");
        eye1.addEventListener('click', function () {
            if (this.previousElementSibling.getAttribute('type') == 'password') {
                this.previousElementSibling.setAttribute('type', 'text');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            } else {
                this.previousElementSibling.setAttribute('type', 'password');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        })
        
        //削除フォームのパスワードが表示・非表示と切り替えできるようにする
        let eye2 = document.getElementById("eye2");
        eye2.addEventListener('click', function () {
            if (this.previousElementSibling.getAttribute('type') == 'password') {
                this.previousElementSibling.setAttribute('type', 'text');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            } else {
                this.previousElementSibling.setAttribute('type', 'password');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        })
        
        //編集フォームのパスワードが表示・非表示と切り替えできるようにする
        let eye3 = document.getElementById("eye3");
        eye3.addEventListener('click', function () {
            if (this.previousElementSibling.getAttribute('type') == 'password') {
                this.previousElementSibling.setAttribute('type', 'text');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            } else {
                this.previousElementSibling.setAttribute('type', 'password');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        })
    </script>

    <?php
        //画面への表示処理
        $sql = 'SELECT * FROM tbtest2';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['postnumber'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].',';
            echo "<hr>";
        }
    ?>

</body>

</html>