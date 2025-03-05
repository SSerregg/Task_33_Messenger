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
            <p>Регистрация!</p>
            <form action="route.php" method="post" class="btn btn-primary">

            <input name="username" type="text" placeholder="email">
            <br>
            <br>
            <input name="password" type="password" placeholder="Пароль">
            <br>
            <br>
            <input name="repeatPass" type="password" placeholder="Повтор пароля">
            <br>
            <br>
            <input name="reg" type="submit" value="Зарегистрироваться" >
            <input type="hidden" name="way" value="reg2">
     
    
        </form>
        <div id="message-box"></div>
       

        </div>
        <div id="right">  
        </div>
    </div>
</body>
</html>
 