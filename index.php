<?php

    //@@@ OPEN SESSION @@@\\
    session_start();
    $username = "root";
    $password = "user";
    $host     = "localhost";
    $dbname   = "chat";

    try {

    //@@@ CONNECT TO MYSQL @@@\\
    $db = new PDO('mysql:dbname=' .$dbname. ';host=' .$host. ';charset=utf8', $username, $password);
    }
    catch(Exception $e){

    //@@@ IF ERROR WE KILL IT @@@\\
    die('Error: ' .$e->getMessage());
    }

    if(isset($_POST['login']))

    //@@@ VERIFY THAT INPUTS ARE NOT EMPTY @@@\\
    if(!empty($_POST['username']) AND !empty($_POST['password'])) {

        //@@@ SANITIZE @@@\\
        $_POST['username'] = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
        $_POST['password'] = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

        //@@@ PROTECT USERNAME AND ENCRYPT PASSWORD @@@\\
        $username = htmlspecialchars($_POST['username']);
        $password = sha1($_POST['password']);
        $result = $db->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $result->execute(array($username, $password));

        $userinfo = $result->fetchAll(PDO::FETCH_ASSOC);
        if(count($userinfo) == 1){
            $_SESSION['id']       = $userinfo[0]['id'];
            $_SESSION['username'] = $userinfo[0]['username'];
            $_SESSION['password'] = $userinfo[0]['password'];
        }

        else{
            $error="Your aren't registered.";
                echo($error);
                echo "<br>";
                echo($password);
        }
    }

        if(isset($_POST['logout'])){
            $_POST['username'] = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $_POST['password'] = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            session_unset();        //@@@ EMPTY SESSION DATA @@@\\
            session_destroy();      //@@@ DESTROY SESSION @@@\\
            header('Location: index.php');
        }
    
    global $db;

        if(isset($_POST['send']) && isset($_POST['message']) && !empty($_SESSION['username'])){
            $_POST['message'] = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
            $req = $db->prepare('INSERT INTO messages (users_id, text) VALUE (?,?)');
            $username2 = $_SESSION['id'];
            $req->execute(array($username2, $_POST['message']));
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
        <form method="post" action="index.php">
            <div class='header'>
                <h1>CHAT ROOM</h1>
            </div>
            <?php if(empty($_SESSION['username'])): ?>
            <div class='main'>
                <div class='userscreen'>
                    <input type='text' class='input-user' placeholder="ENTRER VOTRE NOM D'UTILISATEUR" name='username' autofocus <?php $value=$username;
                        echo "value='$username'" ?> />
                    <input type='password' class='input-user' placeholder="ENTRER VOTRE MOT DE PASSE ICI" name='password' maxlength="20" <?php
                        $value=$password; echo "value='$password'" ?> />
                    <input type='submit' class='btn btn-user' value='LOG IN' name='login' />
                    <?php else : ?>
                    <input type='submit' class='btn btn-user' value='LOG OUT' name='logout' />
                    <?php endif; ?>
        </form>
        <?php if(empty($_SESSION['username'])): ?>
        <a class='logout' href="register.php">REGISTER</a>
        <?php endif; ?>
        </div>
        </div>

        <div class='main'>
            <div id='result'>
                <?php if(!empty($_SESSION['username'])): ?>
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
            <?php else : ?>
            <?php
                    $msg = $db->query("SELECT messages.text, users.username FROM messages INNER JOIN users ON messages.users_id = users.id ORDER BY messages.users_id ASC");
                    $data = $msg->fetchAll();
                    for($i = 0; $i < count($data); $i++){
                        echo "<p>".$data[$i]['username']." a écrit:</p>";
                        echo "<p>".$data[$i]['text']."</p></br>";
                    }
                ?>
                <?php endif; ?>
        </div>
    </body>

    </html>