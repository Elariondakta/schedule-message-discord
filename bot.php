<?php 
    $db = json_decode(file_get_contents("./db.json"), true);

    if (!isset($db["webhook"]) || strlen($db["webhook"]) < 3)
        return header("HTTP/1.1 508 webhook not set");

    if (!isset($_GET["id"]))
        return header("HTTP/1.1 509 id not set");

    
    $json_data = array ('content' => $db["schedule"][$_GET["id"]]["content"]);
    $make_json = json_encode($json_data);
    $ch = curl_init($db["webhook"]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $response = curl_exec($ch);
    if ($response)
        header("HTTP/1.1 200 sent");
    else 
        header("HTTP/1.1 510 message not sent");
?>