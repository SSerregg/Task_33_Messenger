<?php


$way = $_POST["way"] ?? $_GET["need"];

switch ($way) {
    case 'authoriz':
        require_once '../app/authoriz.php';
        break;
    case 'registration':
        require_once '../app/registration.php';
        break;
    case 'reg2':
        require_once '../app/reg2.php';
        break;
    case 'plusfriend':
        require_once '../app/plusfriend.php';
        break;
    case 'oldMessage':
        require_once '../app/getOldMessages.php';
        break;
    case 'set':
        require_once '../app/set.php';
        break;
    case 'dowloadava':
        require_once '../app/dowloadava.php';
        break;   
    default:
        header('HTTP/1.1 404 Not Found');  
        break;       
}