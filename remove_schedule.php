<?php 
    ini_set('display_errors', 1);   
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (!isset($_GET["id"])) {
        header("HTTP/1.1 510 Error parameters not received");
        exit;
    }
    $cronfile = file("./cronfile");
    foreach ($cronfile as $line_number => $line) {
        if (strstr($line, $_GET["id"]))
            array_splice($cronfile, $line_number, 1);
    }
    if (!file_put_contents("./cronfile", join("\n", $cronfile))) {
        header("HTTP/1.1 511 Error saving cronfile");
        exit;
    }
    shell_exec("crontab ./cronfile");
    $db = json_decode(file_get_contents("db.json"), true);

    unset($db["schedule"][$_GET["id"]]);

    if (!file_put_contents("db.json", json_encode($db)))
        header("HTTP/1.1 512 Error saving database");
    else 
        echo "Le message à été correctement supprimé";


?>