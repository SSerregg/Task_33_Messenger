<?php
session_start();
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

$fileName = $_FILES['photo']['name'];
$filePathName = str_replace(' ', '', "$fileName");
$id = $_POST['id'];

$filePath = "img/".$filePathName;

if ($_FILES['photo']['size'] > 1000000) {
    echo 'Недопустимый размер файла ';

}

if (!in_array($_FILES['photo']['type'], ['image/jpeg', 'image/png'])) {
    echo 'Недопустимый формат файла ' ;

}


if(!move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
    echo 'Ошибка загрузки файла ';
}

try {
    $db = new PDO('sqlite:../bd/users.db');
    }
    catch(PDOException){
        echo 'нет соединения с базой';
    }


    $sql = "UPDATE `users` SET `avatar`='$filePathName' WHERE  `id`='$id'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $result['avatar'] = $filePathName;
    echo $result['avatar'];

    header ('Location:route.php?need=authoriz');