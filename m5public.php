<!DOCTYPE html>
<?php
//DB接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));  

$sql = "CREATE TABLE IF NOT EXISTS m5" 
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "date TEXT,"
. "pass TEXT"
.");";
$stmt = $pdo -> query($sql);

//編集
if(!empty($_POST["enum"]) && !empty($_POST["epass"])){
    $enum = $_POST["enum"];
    $epass = $_POST["epass"];
    $sql = 'SELECT * FROM m5';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $row){
        if($row["id"] == $enum && $row["pass"] == $epass){
            $fenum = $row["id"];
            $ename = $row["name"];
            $ecomment = $row["comment"];
        }    
        elseif($row["id"] == $enum && $row["pass"] != $epass){
            $wrongpass = true;
        }
    }
}
?>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
【　投稿フォーム　】
    <form method="post">
        <table>
        <tr>
     <!--　 <th>編集する投稿番号（非表示）:</th>  -->
		    <td><input type="hidden" name="fenum" value="<?php if(isset($fenum)){echo $fenum;} ?>"></td>
		</tr>
		<tr>
			<th>名前:</th>
			<td><input type="text" name="name" value="<?php if(isset($ename)){echo $ename;} ?>"></td>
		</tr>
		<tr>
			<th>コメント:</th>
			<td><input type="text" name="comment" value="<?php if(isset($ecomment)){echo $ecomment;};?>"></td>
		</tr>
		<tr>
		    <th>パスワード:</th>
		    <td><input type="password" name="pass"></td>
		</tr>
		    <td><input type="submit" value="送信"></td>
		</table>
		<br>
【　削除フォーム　】		
        <table>
		<tr>
		    <th>投稿番号:</th>
		    <td><input type="text" name="dnum"></td>
		</tr>
		<tr>
		    <th>パスワード:</th>
		    <td><input type="password" name="dpass"></td>
		</tr>
		<tr>
		    <td><input type="submit" value="送信"></td>
		</tr>
	    </table>
	    <br>
【　編集フォーム　】
        <table>
		<tr>
		    <th>投稿番号:</th>
		    <td><input type="text" name="enum"></td>
		</tr>
		<tr>
		    <th>パスワード:</th>
		    <td><input type="password" name="epass"></td>
		</tr>
		<tr>
		    <td><input type="submit" value="送信"></td>
		</tr>
	    </table>
    </form>
    <br>
</body>
</html>

<?php
//フラグ設定
$edit = false;
if(!empty($_POST["fenum"])){
    $edit = true;
}

//新規投稿・削除
if($edit==false){
    
    //新規投稿
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
        $sql = $pdo -> prepare("INSERT INTO m5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = date("Y/m/d H:i");
        $pass = $_POST["pass"];
        $sql -> execute();                    
    } 
    
    //削除
    if(!empty($_POST["dnum"]) && !empty($_POST["dpass"])){
        $dnum = $_POST["dnum"];
        $dpass = $_POST["dpass"];
        $sql = 'SELECT * FROM m5';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchAll();
        foreach($results as $row){
            if($row["id"] == $dnum && $row["pass"] == $dpass){
                $sql = 'delete from m5 where id=:id' ;
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $id = $_POST["dnum"];
                $stmt->execute();
            }    
            elseif($row["id"] == $dnum && $row["pass"] != $dpass){
                $wrongpass = true;    
            }
        }
    }
}

//編集
elseif($edit==true){
    
    //編集
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
        $newname = $_POST["name"];
        $newcomment = $_POST["comment"];
        $newnum = $_POST["fenum"];
        $newpass = $_POST["pass"];
        $id = $newnum;
        $name = $newname;
        $comment = $newcomment;
        $pass = $newpass;
        $newdate = date("Y/m/d H:i");
        $sql = 'UPDATE m5 SET name=:newname, comment=:newcomment, pass=:newpass, date=:newdate WHERE id=:newnum';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':newnum', $newnum, PDO::PARAM_INT);
        $stmt->bindParam(':newname', $newname, PDO::PARAM_STR);
        $stmt->bindParam(':newcomment', $newcomment, PDO::PARAM_STR);
        $stmt->bindParam(':newpass', $newpass, PDO::PARAM_STR);
        $stmt->bindParam(':newdate', $newdate, PDO::PARAM_STR);
        $stmt->execute();        
    }
}

//投稿を表示
    if(isset($wrongpass) == true){
        echo "パスワードが違います<br><br>";
    }    
    
    echo "【　投稿一覧　】<br>";
    
    $sql = 'SELECT * FROM m5';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach ($results as $row){
        echo $row['id'].', ';
        echo $row['name'].', 「';
        echo $row['comment'].'」, ';
        echo $row['date'].' <br>';
        echo "<hr>";
    }    
?>
