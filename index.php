<?php 
    $db = json_decode(file_get_contents("./db.json"), true);
    file_put_contents("username.log", shell_exec("whoami"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programmateur de messages Discord</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="/lib/materialize-src/js/bin/materialize.min.js"></script>
    <script src="/index.js"></script>
    <?php if ($db["webhook_state"] == "close") { ?>
        <script>M.toast({html: "Aucun webhook n'a été spécifié ou alors celui-ci ne fonctionne pas"}); </script>
    <?php } ?>
</head>
<body class="black">
    <nav>
        <div class="nav-wrapper grey darken-4">
            <a href="/" class="brand-logo center grey-text text-lighten-2">Programmateur de messages Discord</a>
        </div>
    </nav>
    <div class="body-wrapper">
        <div class="webhook-status waves-effect" webhook="<?php echo $db["webhook"]; ?>">Webhook Discord<i class="material-icons green-text" style="margin-left: 15px; font-size:1.3em;"><?php echo $db["webhook_state"]; ?></i></div>
        <div class="crons">
            <table class="centered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Cron</th>
                        <th>Contenu du message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($db["schedule"] as $id => $task) { ?>
                        <tr id="<?php echo $id?>">
                            <td><?php echo $task["frequency"]; ?></td>
                            <td><?php echo $task["cron"]; ?></td>
                            <td><?php echo $task["content"]; ?><i class="material-icons">delete</i></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed-action-btn">
        <a class="btn-floating btn-large grey darken-2 waves-effect waves-light">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <div id="add_cron" class="modal modal-fixed-footer">
        <div class="modal-content">
            <form method="post" name="addForm">
                <div class="input-field col s12 section">
                    <textarea id="content" name="content" class="materialize-textarea"></textarea>
                    <label for="content">Contenu de votre message</label>
                </div>
                <div class="input-field col s12 section">
                    <select name="each" id="each">
                        <option value="semaine">semaine</option>
                        <option value="jour">jour</option>
                        <option value="heure">heure</option>
                        <option value="minute">minute</option>
                    </select>
                    <label for="each">Chaque : </label>
                </div>
                <div class="input-field col s12 scale-transition section daySelect-wrapper">
                    <select name="daySelect" id="daySelect" multiple>
                        <option value="1">Lundi</option>
                        <option value="2">Mardi</option>
                        <option value="3">Mercredi</option>
                        <option value="4">Jeudi</option>
                        <option value="5">Vendredi</option>
                        <option value="6">Samedi</option>
                        <option value="0">Dimanche</option>
                    </select>
                    <label for="daySelect">Jour de la semaine</label>
                </div>
                <div class="input-field col s12 scale-transition section timeSelect-wrapper">
                    <input type="text" class="timepicker" id="timeSelect">
                    <label for="timeSelect">À :</label>
                </div>
            </form>
        </div>
        <div class="modal-footer footer">
            <a href="#!" class="modal-close waves-effect waves-light btn-flat grey-text text-lighten-2">Annuler</a>
            <a href="#!" class="modal-confirm waves-effect waves-light btn grey darken-3">Ajouter le message</a>
        </div>
    </div>
    <div id="webhook_modal" class="modal modal-fixed-footer">
        <div class="modal-content grey darken-3 grey-text text-lighten-2">
            <div class="input-field col s12">
                <input type="text" id="setWebhook" />
                <label for="setWebhook">URL du Webhook : </label>
            </div>
        </div>
        <div class="modal-footer footer">
            <a href="#!" class="modal-close waves-effect waves-light btn grey-text text-lighten-2 grey darken-3">Confirmer</a>
        </div>
    </div>
    <div id="remove_modal" class="modal bottom-sheet modal-fixed-footer" style="height: 30% !important">
        <div class="modal-content grey darken-3 grey-text text-lighten-2" style="text-align: center">
            <h5>Es-tu sur de supprimer cette horaire ?</h5>
        </div>
        <div class="modal-footer footer">
            <a href="#!" class="modal-close waves-effect waves-light btn-flat grey-text text-lighten-2">Annuler</a>
            <a href="#!" class="modal-confirm waves-effect waves-light btn grey darken-3">Confirmer</a>
        </div>
    </div>
</body>
</html>
