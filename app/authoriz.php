<?php
   session_start();
   try {
    $db = new PDO('sqlite:../bd/users.db');
    }
    catch(PDOException){
        echo 'нет соединения с базой';
    }

if(!empty($_SESSION['id'])){
    
   $_SESSION['key'] = bin2hex(random_bytes($_SESSION['id']));
}

if(!empty($_POST['username']) && !empty($_POST['password'])){
    

	$username = (string)$_POST['username'];
    $hashedPass = md5((string)$_POST['password']."HAYTHERE");

    
    $sql = "SELECT * FROM `users` WHERE  `email`='$username' AND `pass`='$hashedPass'";

    $stmt = $db->query($sql);
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);

// -------------------------------------------------------

    if(!empty($result)){
        session_unset();
        session_destroy();
        session_start();
        
        $_SESSION['friends'] = $result['friends'];
        $_SESSION['id'] = $result['id'];
        $_SESSION['key'] = bin2hex(random_bytes($_SESSION['id']));

        if($result['hideMail']==NULL){
            $_SESSION['nickName'] = $result['email'];
        } else {
            $_SESSION['nickName'] = $result['user'];
        }

        if($result['avatar']==NULL){
            $_SESSION['avatar'] = 'stock.jpg';
      
        } else {
            $_SESSION['avatar'] = $result['avatar'];
        }

    } else {
        header ('Location:index.php');
    }

} else {
  
    if(empty($_SESSION['id'])){
    header ('Location:index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Наш первый чат</title>
        <link rel="stylesheet" href="styles.css" type="text/css"/>

    <head>
<body>

    <div id="main">

        <div id="left">
        <p>Мои контакты:</p>
        <?php
        include_once 'myDirectory.php';
        showfriend($_SESSION['friends'], $db);?>
         <br>
        
<form action="route.php" method="post" class="btn btn-primary">
<input type="hidden" name="way" value="plusfriend">
<input name="nickname" type="text" placeholder="email or nickname">
<br>
<input type="hidden" name="id" value="<?php echo $_SESSION['id'];?>">
<input type="hidden" name="key" value="<?php echo $_SESSION['key'];?>">
<br>
<input name="apply" type="submit" value="Добавить контакт" >
</form>
<br>


<div class="create-chat">
<br>
<button class="create-button">Создать чат</button>
<br>
<input class="chat-name-input" type="text" placeholder="Введите название">


</div>

        </div>
        <div class="chat-wrapper">
        <p>Чат с пользователем SkillFactory</p>
        <button type="button" class="soundButton">Включить\выключить звук.</button>
        <div id="message-box">
       
        </div>
        <ul class="right-click-menu">
            <li id="l1">Редактировать сообщение</li>
            <li id="l2">Удалить сообщение</li>
            <li id="l3">Переслать сообщение</li>

        </ul>
        <div class="user-panel">

            <input type="text" name="message" id="message" placeholder="Ваше сообщение..." maxlength="100">
            <button id="send-message">Отправить</button>
        </div>
        </div>     
        <div id="right">  
        <p>Настройки!</p>

<form action="route.php" method="post" class="btn btn-primary">
<input type="hidden" name="way" value="set">
<input name="nickname" type="text" placeholder="nickname">
<br>
<input type="hidden" name="key" value="<?php echo $_SESSION['key'];?>">
<br>
<input name="hideMail" id="ml" type="checkbox">
<label for="ml">Скрыть email</label>
<br>
<br>
<input name="apply" type="submit" value="Применить настройки" >
</form>
<br>
<a href="generator-avatar/avatar.html">Генератор аватар!!!</a>
<br><br>
<img src="img/<?php echo $_SESSION['avatar'];?>"
width="120" height="120" alt="Что-то пошло не так" >
<br>
<form action="route.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $_SESSION['id'];?>">   
<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input type="hidden" name="key" value="<?php echo $_SESSION['key'];?>">
<input name="photo" type="file" ><br><br>
<input type="submit" value="Загрузить свою аватарку" ><br>
<input type="hidden" name="way" value="dowloadava">

</form>
        </div>
    </div>
<script>const userId = <?php echo $_SESSION['id'];?>;
const userNickName = '<?php echo $_SESSION['nickName'];?>';
</script>
<script src="init.js"></script>
</body>
</html>