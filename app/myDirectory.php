<?php

function showfriend($arrFriends, $db){
    if (!empty($arrFriends)){

        $friendArray = explode(",", $arrFriends);
        foreach($friendArray as $key=>$value){
            $sql = "SELECT * FROM `users` WHERE  `id`='$value'";
            $stmt = $db->query($sql);
            $result = $stmt->FETCH(PDO::FETCH_ASSOC); 
    
    if(!empty($result['avatar'])){
        $fAvatar = $result['avatar'];
    } else {
        $fAvatar = 'stock.jpg';
    } 
    if(!empty($result['user'])){
        $fUser = $result['user'];
    } else {
        $fUser = ' ';
    }
    if(empty($result['hideMail'])){
        $fEmail = $result['email'];
    } else {
        $fEmail = 'hidden';
    }
    
    
       $string = '<div class="friends"><img src="img/'.$fAvatar.'"
        width="40" height="40" alt="Что-то пошло не так" >
        <p class ="friendP">'.$fEmail.'<br>'.$fUser.'</p>
    </div>';
    echo $string;
        }
}}