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
        </div>
        <div class="chat-wrapper">
            <p>Наш чат!</p>

            <form action="route.php" method="post" class="btn btn-primary">
            <input type="hidden" name="way" value="authoriz">
            <input name="username" type="text" placeholder="имя">
            <input name="password" type="password" placeholder="Пароль">
            <br>
            <br>
            <input name="aut" type="submit" value="Авторизироваться" >
    
        </form>
        <div id="message-box"></div>
       
        <form action="route.php" method="post">

            <br>
            <input name="reg" type="submit" value="Зарегистрироваться" >
            <input type="hidden" name="way" value="registration">
        </form>
        </div>
        <div id="right">  
        </div>
    </div>
</body>
</html>