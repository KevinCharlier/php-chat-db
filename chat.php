<?php
    try
    {
        // Connexion à MySql
        $db = new PDO('mysql:host=localhost;dbname=chat;charset=utf8', 'root', 'user');
    }
    catch(Exception $error)
    {
        // Si erreur
        die('Erreur : '.$error->getMessage());
    }

    session_start();
    
    if(isset($_POST['submit'])){

        $options = array('chat' 	=> FILTER_SANITIZE_STRING);
        $result = filter_input_array(INPUT_POST, $options);
        $checkResult =[];	 
        $chat = ($_POST['chat']);

        $nombreErreur = 0; // Variable qui compte le nombre d'erreur
    
        if (!isset($_POST['chat'])) {
                $nombreErreur++;
                $erreur1 = '<p>Il y a un problème avec la variable "chat".</p>';
        } else {
            if (empty($_POST['chat'])) {
            $nombreErreur++;
            $erreur2 = "<p>Vous avez oublié d'entrer votre message</p>";
            }
        }
    
        if ($nombreErreur==0) {
            $chat = filter_var($_POST['chat'], FILTER_SANITIZE_STRING);
            $dbadd = "INSERT INTO messages (text) VALUES ($chat)";
            $result = $db->exec($dbadd); // requête envoyée à la db \\
            header('Location:chat.php');
        }
    
        else { // S'il y a un moins une erreur
    
            echo '<div style="border:1px solid #ff0000; padding:5px;">';
            echo '<p style="color:#ff0000;">Désolé, il y a eu '.$nombreErreur.' erreur(s). Voici le détail des erreurs:</p>';
            if (isset($erreur1)) echo '<p>'.$erreur1.'</p>';
            if (isset($erreur2)) echo '<p>'.$erreur2.'</p>';
            echo '</div>';
        }
    }
    
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/style.css" />
        <title>Chat Room</title>
    </head>

    <body>
        <div class='header'>
            <h1>CHAT ROOM</h1>
            <input type='submit' class='btn btn-user' value='LOG OUT' name='logout' />
        </div>

        <div class='main'>
            <div id='result'>
                <?php
                    $msg = $db->query("SELECT messages.text, users.username FROM messages INNER JOIN users ON messages.users_id = users.id ORDER BY messages.users_id ASC");
                    $data = $msg->fetchAll();
                    for($i = 0; $i < count($data); $i++){
                        echo "<p>".$data[$i]['username']." a écrit:</p>";
                        echo "<p>".$data[$i]['text']."</p></br>";
                    }
                ?>
            </div>
            <div class='chatcontrols'>
                <form method="post">
                    <input type='text' name='chat' id='chatbox' autocomplete="off" placeholder="CHATTEZ" />
                    <input type='submit' name='submit' id='send' class='btn btn-send' value='Envoyez' />
                </form>
            </div>
        </div>
    </body>

    </html>