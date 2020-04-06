<?php 

    if (!isset($_GET["webhook"]))
        header("HTTP/1.1 513 Error parameter not received");

    $db = json_decode(file_get_contents("db.json"), true);

    $db["webhook"] = $_GET["webhook"];
    $db["webhook_state"] = "done";

    if (file_put_contents("db.json", json_encode($db)))
        echo "Le webhook discord à bien été enregistré";
    else 
        header("HTTP/1.1 514 Error saving database");

?>