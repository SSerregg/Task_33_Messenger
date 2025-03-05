<?php

if (!empty($_POST['nickname'])){
    $db = new PDO('sqlite:../bd/users.db');
 
$nickname = $_POST['nickname'];
$id = $_POST['id'];

if(preg_match('/@.+\./', $nickname)){
   

    $sql = "SELECT * FROM `users` WHERE  `email`='$nickname' ";
    $stmt = $db->query($sql);
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);

    if(empty($result['hideMail'])){
        $newFriends = $result['id'];
    } else {
        $newFriends = NULL;
    }

} else {
    $sql = "SELECT * FROM `users` WHERE  `user`='$nickname' ";
    $stmt = $db->query($sql);
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);
    $newFriends = $result['id']?? null;
}

// Поиск уже имеющихся контактов
$sqlMain = "SELECT * FROM `users` WHERE  `id`='$id' ";
$stmtMain = $db->query($sqlMain);
$resultMain = $stmtMain->FETCH(PDO::FETCH_ASSOC);
if(empty($resultMain['friends'])){
    $friendArray = [];
} else {
    $friendArray = explode(",", $resultMain['friends']);
}
if(!in_array($newFriends, $friendArray)){
    $friendArray[] = $result['id']??'';
$pushFriends = implode(",", $friendArray);

// Добавление новых контактов
$sql = "UPDATE `users` SET `friends`='$pushFriends' WHERE  `id`='$id'";
$stmt = $db->prepare($sql);
$stmt->execute();
}
header ('Location:route.php?need=authoriz');

} else {
    header ('Location:route.php?need=authoriz');
}