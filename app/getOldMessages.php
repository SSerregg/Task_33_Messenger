<?php

$db = new PDO('sqlite:../bd/my_db.db');

$sql = "SELECT id, userNick, valueText FROM `chats`";

try{
$stmt = $db->query($sql);
$result = $stmt->FetchAll(PDO::FETCH_ASSOC);

}catch(PDOException){ 

    $result = '';   
}



print_r(json_encode($result));