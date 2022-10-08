<?php
    include "util.php";
    $database = require "connect.php";

    session_start();

    $_POST['refreshUserCount'] = true;
    $_SESSION['lastActive'] = time();
?>
<?php
    $loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];
?>
<?php
    if ($loggedIn) {
        updateUserActive($database, $_SESSION['userID']);
    }

    if (isset($_POST['logout']) && $loggedIn) {
        userLogout($database, $_SESSION['userID'], $_SESSION['roomID']);

        session_unset();
        session_destroy();

        unset($_POST['logout']);
    }

    if (isset($_POST['login'])) {
        header("location: login.php");

        unset($_POST['login']);
    }
?>
<?php
    $loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];
?>
<html lang="de">

<head>
    <title>Chatsystem</title>
    <link rel="stylesheet" href="src/style.css">
    <script src="src/jquery-3.5.1.min.js"></script>
    <script src="src/alertHandler.js"></script>
    <script src="src/colorHandler.js"></script>
    <script src="src/messageHandler.js"></script>
    <script src="src/roomHandler.js"></script>
    <script src="src/sessionHandler.js"></script>
    <script src="src/userCountHandler.js"></script>
</head>

<body onload="initUserCount(); initMessages(); initColorPicker(); initRoomHandler(); initAlertHandler(); initSessionHandler()">
    <div id="blur" class="invisible"></div>
    <div id="alertBox" class="alertBox center invisible">
        <div id="alertHeader"></div>
        <div id="alertMessage"></div>
        <div id="alertOptions"></div>
    </div>
    <div class="main center">
        <div class="left">
            <p id="userCount"></p>
            <?php
                if ($loggedIn) {
            ?>
            <div id="roomHeader">
                <h1 class="roomHeader">Räume</h1>
                <img src="src/img/plus.png" alt="Add Room Icon" title="Raum erstellen" class="addRoom" onclick="displayAlert('Raum erstellen', 'Achtung: Dieser Raum wird gelöscht, sobald sich keine Benutzer mehr in ihm befinden!', 4)">
            </div>
            <div id="rooms" class="rooms">
            </div>
            <?php
                }
            ?>
            <div class="logButton">
                <p id="auto"></p>
                <form method="post">
                    <?php
                        echo "<input type=\"submit\" name=\"" . ($loggedIn ? "logout" : "login") . "\" value=\"" . ($loggedIn ? "Ausloggen" : "Einloggen") . "\">";
                    ?>
                </form>
            </div>
            <?php
                if ($loggedIn) {
                    echo "<img src='src/img/edit.png' alt='Profil bearbeiten' title='Profil bearbeiten' class='editProfile' onclick='displayAlert(\"Profil bearbeiten\", \"\", 5)'>";
                }
            ?>
        </div>

        <div class="right">
            <div id="messages">
                <div id="notifier" class="messageNotifier"></div>
            </div>

            <?php
                if ($loggedIn) {
            ?>
            <div class="input">
                <label for="textInput"></label>
                <textarea id="textInput" placeholder="Gebe hier deine Nachricht ein" autofocus></textarea>

                <label for="colorPicker"></label>
                <div class="input_right">
                    <?php
                        $color = $_SESSION['color'];
                        echo "<input id='colorPicker' type='color' title='Farbe ändern' onchange='changeColor()' value='$color'>"
                    ?>
                    <button id="sendMsg" onclick="sendMessage()">Senden</button>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
</body>
</html>