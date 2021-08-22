<!DOCTYPE html>
<html>
    <head>
        <meta charset="ja">
        <title>misson3-05</title>
    </head>
    <body>
        <?php
            $table = "tb5_05";
            //データベースに接続
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $tb_password = 'パスワード';
            $pdo = new PDO($dsn, $user, $tb_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            //新規投稿
            if(!empty($_POST["strn"]) && !empty($_POST["strc"])){
                
                $sql = $pdo -> prepare("INSERT INTO $table (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                
                $name = $_POST["strn"];
                $comment = $_POST["strc"];
                $date = date('Y-m-d H:i:s');
                $pass = $_POST["pass"];
                $sql -> execute();
                echo "書き込み成功！";
            }
            //削除
            elseif(!empty($_POST["delete"]) && !empty($_POST["delete_pass"])){
                
                $d_id = $_POST["delete"];
                $d_pass = $_POST["delete_pass"];
                //対応するidの行を抜き出し、そのパスワードを抜き出す
                $sql = "SELECT * FROM $table where id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $d_id, PDO::PARAM_INT);
                $stmt->execute();
                $stmt_d = $stmt->fetchAll();
                foreach ($stmt_d as $row){
                    $del_pass = $row["pass"];
                }
                //パスワードがあっていたら対象IDを削除
                if($del_pass == $d_pass){
                    $sql = "delete from $table where id=:id AND pass=:pass";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $d_id, PDO::PARAM_INT);
                    $stmt->bindParam(':pass', $d_pass, PDO::PARAM_INT);
                    $stmt->execute();
                    echo "削除成功！";
                }else{
                    echo "IDとパスワードが一致しません。<br>";
                }
            }
            //編集選択
            elseif(!empty($_POST["editselect"]) && !empty($_POST["edit_select_pass"])){
                $e_id = $_POST["editselect"];
                $e_pass = $_POST["edit_select_pass"];
                //対応するidの行を抜き出し、そのパスワードを抜き出す
                $sql = "SELECT * FROM $table where id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $d_id, PDO::PARAM_INT);
                $stmt->execute();
                $stmt_d = $stmt->fetchAll();
                foreach ($stmt_d as $row){
                    $ed_pass = $row['pass'];
                }
                //パスワードがあっていたら対象IDを編集
                if($ed_pass == $e_pass){
                    $flag = 1;
                    $sql = "SELECT * FROM $table where id=:id AND pass=:pass";/*AND pass=:pass*/
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':id', $e_id, PDO::PARAM_INT);
                    $stmt -> bindParam(':pass', $e_pass, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt->fetchAll();
                    
                    foreach($results as $row){
                        $edit_select_strn = $row['name'];
                        $edit_select_strc = $row['comment'];
                        $edit_select_pass = $row['pass'];
                    }
                }else{
                    $flag = 0;
                    echo "IDとパスワードが一致しません。<br>";
                }
            }
            //編集実行
            elseif(!empty($_POST["edit_num"]) && !empty($_POST["edit_pass"])){
                $edit_id = $_POST["edit_num"];
                $edit_name = $_POST["edit_strn"];
                $edit_comment = $_POST["edit_strc"];
                $edit_date = date('Y-m-d H:i:s').'(編集付み)';
                $edit_pass = $_POST["edit_pass"];
                $sql = "UPDATE tb5_04 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id AND pass=:pass";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $edit_name, PDO::PARAM_INT);
                $stmt->bindParam(':comment', $edit_comment, PDO::PARAM_INT);
                $stmt->bindParam(':date', $edit_date, PDO::PARAM_INT);
                $stmt->bindParam(':pass', $edit_pass, PDO::PARAM_INT);
                $stmt->execute();
                echo "編集成功！";
            }
            //初期表示
            else{
                echo "<br>生まれ変わったら入りたいサークルや部活はありますかー？<br>(投稿した名前・コメントの編集または削除を行いたい場合はパスワードを設定してください)<br>";
            }
        ?>
            <form action="" method="post">
                <?php
                //ファイル内の投稿番号取得
                if(isset($e_id) && $flag == 1){//flag 0:投稿番号とeditselectが不一致　1:投稿番号とeditselectが一致
                ?>
                【編集中】名前、コメント、パスワードを変更できます。(変更した後に送信を押してください。)<br>
                <input type="text" name="edit_strn" value =
                <?php
                echo $edit_select_strn;
                ?>
                ><br>
                <?php
                }else{
                ?>
                <input type="text" name="strn" placeholder="名前"><br>
                <?php
                }
                ?>
                <?php
                if(isset($e_id) && $flag == 1){
                ?>
                    <input type="text" name="edit_strc" value =
                    <?php
                        echo $edit_select_strc;
                    ?>
                    >
                <?php
                }else{
                ?>
                <input type="text" name="strc" placeholder="コメント">
                <?php
                }
                ?>
                <input type="hidden" name="edit_num" value=<?php if(isset($e_id) && $flag == 1)echo $e_id;?>><br>
                <?php
                if(isset($e_id) && $flag == 1){
                ?>
                    <input type="text" name="edit_pass" value =
                    <?php 
                        echo $edit_select_pass;
                    ?>
                    >
                <?php
                }else{
                ?>
                <input type="number" name="pass" placeholder="パスワード">
                <?php
                }
                ?>
                <input type="submit" value="送信"><br><br>
                <input type="number" name="delete"placeholder="削除対象番号"><br>
                <input type="number" name="delete_pass"placeholder="パスワード">
                <input type="submit" value="削除"><br><br>
                <input type="number" name="editselect" placeholder="編集対象番号"><br>
                <input type="number" name="edit_select_pass"placeholder="パスワード">
                <input type="submit" value="編集">
            </form>
        <?php
            //作成したテーブルの内容を表示
            /*$sql ="SHOW CREATE TABLE $table";
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                echo $row[1];
            }
            echo "<hr>";
            */
            //ファイルの中を表示
            $sql = "SELECT * FROM $table";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'];
                echo '<名前>'.$row['name'];
                echo '<日付>'.$row['date'].'<br>';
                echo "&emsp;"."&emsp;".$row['comment'].'<br>';
                //echo '<パスワード>'.$row['pass'].'<br>';
            }
        ?>
    </body>
</html>