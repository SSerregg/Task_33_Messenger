<?php

function stmt ($db, $username, $hashedPass) {
    $stmt = $db->prepare("INSERT INTO users (email, pass) VALUES (?, ?)");
    $stmt->bindParam(1, $username);
    $stmt->bindParam(2, $hashedPass);
    $stmt->execute();
    } 


require_once '../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!empty($_POST['username']) && !empty($_POST['password'])&& !empty($_POST['repeatPass']) && preg_match('/@.+\./', $_POST['username'])){

	$username = (string) $_POST['username'];
    $hashedPass = md5((string)$_POST['password']."HAYTHERE");

	$db = new PDO('sqlite:../bd/users.db');

	try{

		stmt($db, $username, $hashedPass);
	
	}catch(PDOException){
					$criate = $db->prepare("CREATE TABLE users (
					id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
					user VARCHAR(30) , 
					email VARCHAR(50) , 
					avatar VARCHAR(50) , 
					pass VARCHAR(50) ,
					friends VARCHAR(255) ,
					hideMail TINYINT(1) NULL 
				  );");
				$criate->execute();
	
		stmt($db, $username, $hashedPass);
	}

	//mail($username, 'Здравствуйте!', "Недавно вы зарегестрировались\n 'Наш чат Skillfactory'\n если это не вы обратитесь к нам.");

// Для реальной работы нужно ввести действующий email \/
//PHPMailer-Objekt instanziieren
 $mail = new PHPMailer(true);
 
    try {
        // SMTP-Einstellungen
        $mail->isSMTP();
        $mail->Host = 'ssl://smtp.yandex.ru'; // SMTP-Server des E-Mail-Anbieters
        $mail->SMTPAuth = true;
 
        $mail->Username = 'test@yandex.ru'; // Benutzername Ihres E-Mail-Kontos
        $mail->Password = 'ckaojkjbugdpexes'; // Passwort Ihres E-Mail-Kontos
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 465; // Standardport für SMTP

        // Absender und Empfänger festlegen
      
        $mail->setFrom('test@yandex.ru');
        $mail->addAddress($username); // Die E-Mail wird an den Benutzer gesendet

        // E-Mail-Inhalt
        $mail->isHTML(true);
        $mail->Subject = 'чат Skillfactory!';
        $mail->Body    = "Здравствуйте! Недавно вы зарегестрировались\n Наш чат Skillfactory\n если это не вы обратитесь к нам.";

        // E-Mail senden
        $mail->send();
    
    } catch (Exception $e) {

        //echo "E-Mail konnte nicht gesendet werden. Fehler: {$mail->ErrorInfo}";
    }

	header ('Location:index.php');
} else {
	header ('Location:route.php');
} 

