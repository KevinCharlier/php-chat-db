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
    //@@@ SEND BUTTON @@@\\
    if(isset($_POST['submit']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])){

    $_POST['username'] = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
    $_POST['password'] = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
    $_POST['email']    = filter_var($_POST['email'],FILTER_SANITIZE_STRING);

    $username = htmlspecialchars($_POST['username']);
    $email    = htmlspecialchars($_POST['email']);
    $password = sha1($_POST['password']);
    
    global $db;
    
    $usernamereq = $db->prepare('SELECT * FROM users WHERE username = ?');
    $usernamereq->execute(array($username));
    $usernameexist = $usernamereq->rowcount();

    $emailreq = $db->prepare('SELECT * FROM users WHERE email = ?');
    $emailreq->execute(array($email));
    $emailexist = $emailreq->rowcount();
    
        if($usernameexist == 0){

            if($emailexist == 0){

                $insertutil= $db->prepare("INSERT INTO users (username, email, password) VALUES ('".$username."', '".$email."','".$password."')");
                $insertutil->execute(array(
                    "username" => $username, 
                    "password" => $password, 
                    "email" => $email));
                header('location: index.php');
            }

            else {
                $error = "Your email exists alredy!";
            }
        }

            else {
                $error = "Username alredy exists!";
            }
        }

            else {
                $error = "Please fill the required fields!";
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
            <h1>
                CHAT ROOM
            </h1>
        </div>

        <div class='main'>
            <div class='userscreen'>
                <form method="post">
                    <input type='text' class='input-user' placeholder="ENTRER VOTRE USERNAME ICI" name="username" autofocus <?php $value=$username;
                        echo "value='$username'" ?> />
                    <input type='email' class='input-user' placeholder="ENTRER VOTRE ADRESSE EMAIL ICI" name="email" autofocus <?php $value=$email;
                        echo "value='$email'" ?> />
                    <input type='password' class='input-user' placeholder="ENTRER VOTRE MOT DE PASSE ICI" name='password' maxlength="20" />
                    <input type='submit' class='btn btn-user' value='REGISTER' name='submit' />
                </form>
            </div>
        </div>
    </body>

    </html>