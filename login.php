<html lang="de">

<head>
    <title>Anmeldung</title>
    <link rel="stylesheet" href="src/style.css">
    <script src="src/jquery-3.5.1.min.js"></script>
    <script src="src/alertHandler.js"></script>
</head>

<body onload="initAlertHandler();">
    <div id="blur" class="invisible"></div>
    <div id="alertBox" class="alertBox center invisible">
        <div id="alertHeader"></div>
        <div id="alertMessage"></div>
        <div id="alertOptions"></div>
    </div>
    <div class="center login">
        <h1>Login</h1>
        <form method="post" action="">
            <label for="ident">Benutzername / Email-Adresse</label>
            <input id="ident" type="text" name="identifier" class="width100" placeholder="Gebe hier deine Email-Adresse oder Benutzernamen ein" required autofocus autocomplete="username nickname">

            <label for="password">Passwort</label>
            <input id="password" type="password" name="password" class="width100"placeholder="Gebe hier dein Passwort ein" required minlength=4 autocomplete="current-password">

            <br>
            <div id="loginButtons">
                <input type="submit" value="Einloggen" class="width40">
                <a href='register.php'>Registrieren</a>
            </div>
        </form>
    </div>
</body>

<?php
    session_start();


    if ((isset($_POST['identifier'])) && (isset($_POST['password']))) {
        $_SESSION['loggedIn'] = false;

        include "util.php";
        $database = require('connect.php');

        $safe_identifier = mysqli_real_escape_string($database, $_POST['identifier']);

        if (filter_var($safe_identifier, FILTER_VALIDATE_EMAIL)) {
            $db_ident = "mail";
        } else {
            $db_ident = "username";
        }

        $result = SQLRequest($database, "SELECT * FROM users WHERE " . $db_ident . " = '" . $safe_identifier . "';");

        $alertMessage = "";
        $knownUser = false;
        if ($dataReceived = mysqli_fetch_assoc($result)) {
            if ($dataReceived['confirmed']) {
                if (!$dataReceived['active']) {
                    if (password_verify($_POST['password'], $dataReceived['password'])) {
                        $time = time();
                        $result = SQLRequest($database, "UPDATE users SET active = 1, lastActive = $time, inRoom = 1 WHERE " . $db_ident . " = '" . $safe_identifier . "';");

                        increaseUserCount($database, 1);

                        $knownUser = true;

                        $_SESSION['loggedIn'] = true;
                        $_SESSION['userID'] = $dataReceived['id'];
                        $_SESSION['username'] = $dataReceived['username'];
                        $_SESSION['name'] = $dataReceived['name'];
                        $_SESSION['email'] = $dataReceived['mail'];
                        $_SESSION['color'] = $dataReceived['color'];
                        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];

                        if ($dataReceived['age']) {
                            $_SESSION['age'] = $dataReceived['age'];
                        }

                        if ($dataReceived['gender']) {
                            $_SESSION['gender'] = $dataReceived['gender'];
                        }

                        header("location: index.php");
                    }
                } else {
                    $knownUser = true;
                    $alertMessage = "Dieser Benutzer ist bereits eingeloggt!";
                }
            } else {
                $knownUser = true;
                $alertMessage = "Dieser Account muss noch bestätigt werden!";
            }
        }

        if (!$knownUser) {
            $alertMessage = "Benutzername oder Passwort unbekannt!";
        }

        if ($alertMessage !== "") {
            echo "<script>initAlertHandler(); displayAlert('Anmeldung', '$alertMessage');</script>";
        }
    }
?>
</html>