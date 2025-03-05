<?php

error_reporting(E_ALL);

set_time_limit(0);

ob_implicit_flush();


$address = '127.0.0.1';
$port = 8000;
$null = NULL;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($sock, $address, $port);

socket_listen($sock);

$clients = array($sock);

// base---------------------------
$db = new PDO('sqlite:../bd/my_db.db');
//--------------------------------

do{

  
$changed = $clients;

socket_select($changed, $null, $null, 100);

if(in_array($sock, $changed)){

    $socket_new = socket_accept($sock);

    $clients[] = $socket_new;
    $header = socket_read($socket_new, 1024);

    if(preg_match('/Sec-WebSocket-Key: (.*)/', $header, $match)){

        $tesss = rtrim($match[1]);
               
        $WebSocketAccept = base64_encode(pack('H*', sha1($tesss.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        
        $in_header = "HTTP/1.1 101 Switching Protocols\r\n" .
                    "Upgrade: websocket\r\n" .
                    "Connection: Upgrade\r\n" .
                    "Sec-WebSocket-Accept: $WebSocketAccept\r\n\r\n";
        
        socket_write($socket_new, $in_header);}

       

    $found_socket = array_search($sock, $changed);
   
    unset($changed[$found_socket]);
}

foreach($changed as $chaged_socket){


$message = socket_read($chaged_socket, 1024);

$decodedMessage = decode($message);

$arrayFromChat = json_decode($decodedMessage['payload'], true);

if(!empty($arrayFromChat['message'])){

	try{

		stmt($db, $arrayFromChat['userField'], $arrayFromChat['userNick'], $arrayFromChat['chatName'], $arrayFromChat['message']);
	
	}catch(PDOException){
				
        createTable ($db);
		stmt($db, $arrayFromChat['userField'], $arrayFromChat['userNick'], $arrayFromChat['chatName'], $arrayFromChat['message']);
	}
    $sql = "SELECT id FROM `chats` ORDER BY id DESC LIMIT 1";

    $stmt = $db->query($sql);

    $resultLastId = $stmt->FETCH(PDO::FETCH_ASSOC);   
}

if($decodedMessage['type'] === 'close'){

    $found_socket = array_search($chaged_socket, $clients);
    
    unset($clients[$found_socket]);
    
    }else {

        array_pop($arrayFromChat);
        array_pop($arrayFromChat);
      $arrayFromChat['messageId']=$resultLastId['id'];
        $jsonEncoded = json_encode($arrayFromChat);
$encodedMessage = encode($jsonEncoded);

foreach($clients as $key => $clis){
if($key != 0){
    socket_write($clis, $encodedMessage);
}
}

}
}

}while(true);

 
socket_close($sock);




// functions----------------------------------------------------------------
function decode($data)
{
    $unmaskedPayload = '';
    $decodedData = array();

    // estimate frame type:
    $firstByteBinary = sprintf('%08b', ord($data[0]));
    $secondByteBinary = sprintf('%08b', ord($data[1]));
    $opcode = bindec(substr($firstByteBinary, 4, 4));
    $isMasked = ($secondByteBinary[0] == '1') ? true : false;
    $payloadLength = ord($data[1]) & 127;

    // unmasked frame is received:
    if (!$isMasked) {
        return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');
    }

    switch ($opcode) {
        // text frame:
        case 1:
            $decodedData['type'] = 'text';
            break;

        case 2:
            $decodedData['type'] = 'binary';
            break;

        // connection close frame:
        case 8:
            $decodedData['type'] = 'close';
            break;

        // ping frame:
        case 9:
            $decodedData['type'] = 'ping';
            break;

        // pong frame:
        case 10:
            $decodedData['type'] = 'pong';
            break;

        default:
            return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
    }

    if ($payloadLength === 126) {
        $mask = substr($data, 4, 4);
        $payloadOffset = 8;
        $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
    } elseif ($payloadLength === 127) {
        $mask = substr($data, 10, 4);
        $payloadOffset = 14;
        $tmp = '';
        for ($i = 0; $i < 8; $i++) {
            $tmp .= sprintf('%08b', ord($data[$i + 2]));
        }
        $dataLength = bindec($tmp) + $payloadOffset;
        unset($tmp);
    } else {
        $mask = substr($data, 2, 4);
        $payloadOffset = 6;
        $dataLength = $payloadLength + $payloadOffset;
    }

    /**
     * We have to check for large frames here. socket_recv cuts at 1024 bytes
     * so if websocket-frame is > 1024 bytes we have to wait until whole
     * data is transferd.
     */
    if (strlen($data) < $dataLength) {
        return false;
    }

    if ($isMasked) {
        for ($i = $payloadOffset; $i < $dataLength; $i++) {
            $j = $i - $payloadOffset;
            if (isset($data[$i])) {
                $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
            }
        }
        $decodedData['payload'] = $unmaskedPayload;
    } else {
        $payloadOffset = $payloadOffset - 4;
        $decodedData['payload'] = substr($data, $payloadOffset);
    }

    return $decodedData;
}
    


function encode($payload, $type = 'text', $masked = false)
{
    $frameHead = array();
    $payloadLength = strlen($payload);

    switch ($type) {
        case 'text':
            // first byte indicates FIN, Text-Frame (10000001):
            $frameHead[0] = 129;
            break;

        case 'close':
            // first byte indicates FIN, Close Frame(10001000):
            $frameHead[0] = 136;
            break;

        case 'ping':
            // first byte indicates FIN, Ping frame (10001001):
            $frameHead[0] = 137;
            break;

        case 'pong':
            // first byte indicates FIN, Pong frame (10001010):
            $frameHead[0] = 138;
            break;
    }

    // set mask and payload length (using 1, 3 or 9 bytes)
    if ($payloadLength > 65535) {
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 255 : 127;
        for ($i = 0; $i < 8; $i++) {
            $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
        }
        // most significant bit MUST be 0
        if ($frameHead[2] > 127) {
            return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
        }
    } elseif ($payloadLength > 125) {
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 254 : 126;
        $frameHead[2] = bindec($payloadLengthBin[0]);
        $frameHead[3] = bindec($payloadLengthBin[1]);
    } else {
        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
    }

    // convert frame-head to string:
    foreach (array_keys($frameHead) as $i) {
        $frameHead[$i] = chr($frameHead[$i]);
    }
    if ($masked === true) {
        // generate a random mask:
        $mask = array();
        for ($i = 0; $i < 4; $i++) {
            $mask[$i] = chr(rand(0, 255));
        }

        $frameHead = array_merge($frameHead, $mask);
    }
    $frame = implode('', $frameHead);

    // append payload to frame:
    for ($i = 0; $i < $payloadLength; $i++) {
        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
    }

    return $frame;
}


function stmt ($db, $param1, $param2, $param3, $param4) {
    $stmt = $db->prepare("INSERT INTO chats (userId, userNick, chatName, valueText) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $param1);
    $stmt->bindParam(2, $param2);
    $stmt->bindParam(3, $param3);
    $stmt->bindParam(4, $param4);
    $stmt->execute();
} 

function createTable ($db){
	$criate = $db->prepare("CREATE TABLE chats (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
        userId INTEGER NOT NULL DEFAULT 'no', 
        userNick VARCHAR(40) NOT NULL ,
        chatName VARCHAR(70) NOT NULL , 
        valueText VARCHAR(255)
      );");
    $criate->execute();
}