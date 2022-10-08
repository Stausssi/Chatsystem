<?php
    $database = require "connect.php";

    if (isset($_GET['mail']) && isset($_GET['challenge'])) {
        $mail = $_GET['mail'];
        $challenge = $_GET['challenge'];

        $request = "SELECT * FROM users WHERE mail = '" . $mail . "';";
        $result = mysqli_query($database, $request);

        if ($data = mysqli_fetch_assoc($result)) {
            $data_chal = $data['challenge'];
            if ($challenge == $data_chal) {
                if (!$data['confirmed']) {
                    $request = "UPDATE users SET confirmed = 1 WHERE mail = '" . $mail . "';";
                    $result = mysqli_query($database, $request);

                    echo "Anmeldung erfolgreich bestätigt!";
                    echo "<a  href=\"login.php\">Anmelden</a>";
                } else {
                    echo "Dieser Benutzer wurde bereits bestätigt!";
                }

            } else {
                echo "Invalid Challenge!";
            }
        }
    }
