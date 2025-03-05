<?php

session_start();

if($_POST['key']===$_SESSION['key']){
   
try {
    $db = new PDO('sqlite:../bd/users.db');
}
catch(PDOException){
    echo 'нет соединения с базой';
}
$id = $_SESSION['id'];
if(!empty($_POST['nickname'])){

  
    $nickname = $_POST['nickname'];
    
    $sql = "SELECT * FROM `users` WHERE  `user`='$nickname'";

    $stmt = $db->query($sql);
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);
    if(!empty($result)){
   
echo 'Ник занят!';

    } else {

        $sql = "UPDATE `users` SET `user`='$nickname' WHERE  `id`='$id'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
}


if(!empty($_POST['hideMail'])){

    $sql = "UPDATE `users` SET `hideMail`=1 WHERE  `id`='$id'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

} else {
    $sql = "UPDATE `users` SET `hideMail`=NULL WHERE  `id`='$id'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
}

header ('Location:route.php?need=authoriz');

} else {
    header ('Location:index.php');
}