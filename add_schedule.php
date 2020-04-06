<?php 
    ini_set('display_errors', 1);   
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (!isset($_POST["content"], $_POST["cron"], $_POST["frequency"])) {
        header("HTTP/1.1 511 Error getting args");
        exit;
    }

    $id = uniqid();

    //Ligne qui sera ajoutée dans le cronfile
    $cronQuery = $_POST["cron"]." php-cgi ".__DIR__."/bot.php id=".$id."\n";

    //Si ya pas de cronfile on le créer
    if (!file_exists("cronfile")) {
        file_put_contents("cronfile", "");
    }
    //On le lit ligne par ligne dans un array
    $cronfile = file("cronfile");
    array_push($cronfile, $cronQuery);  //On rajoute notre ligne
    $cronfile = implode("\n", $cronfile);       //On refait une string  
    if (!file_put_contents("./cronfile", $cronfile)) {      //On la sauvegarde
        header("HTTP/1.1 511 error saving cronfile");
        exit;
    }
    exec("crontab cronfile"); //On update le gestionnaire

    $db = json_decode(file_get_contents("./db.json"), true);    //on update la db
    $db["schedule"][$id] = Array(
        "content" => $_POST["content"],
        "frequency" => $_POST["frequency"],
        "cron" => $_POST["cron"],
    );
    if (!file_put_contents("./db.json", json_encode($db)))
        header("HTTP/1.1 512 Error saving database");
    else { ?>
        <tr id="<?php echo $id?>">
            <td><?php echo $_POST["frequency"]; ?></td>
            <td><?php echo $_POST["cron"]; ?></td>
            <td><?php echo $_POST["content"]; ?><i class="material-icons">delete</i></td>
        </tr>
    <?php } 
?>