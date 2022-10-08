<html lang="de">

<head>
    <title>Registrieren</title>
    <link rel="stylesheet" href="src/style.css">
    <script src="src/jquery-3.5.1.min.js"></script>
    <script src="src/alertHandler.js"></script>
    <script src="src/inputValidator.js"></script>
</head>

<body onload="initAlertHandler(); initInputs();">
    <div id="blur" class="invisible"></div>
    <div id="alertBox" class="alertBox center invisible">
        <div id="alertHeader"></div>
        <div id="alertMessage"></div>
        <div id="alertOptions"></div>
    </div>
<?php
    include "util.php";
    $database = require "connect.php";

    $correctNick = false;
    $correctName = false;
    $correctMail = false;
    $correctPassword = false;

    $alertMessage = "";

    if (isset($_POST['nick'])) {
        $username_S = mysqli_real_escape_string($database, htmlentities($_POST['nick']));

        // Validate length
        if (strlen($username_S) >= 5 && strlen($username_S) <= 25) {
            // Validate Contents
            if (preg_match("/^[a-zA-Z0-9]*$/", $username_S)) {
                $correctNick = true;
            } else {
                $alertMessage .= "Dein Benutzername darf nur Buchstaben und Zahlen enthalten!<br>";
            }
        } else {
            $alertMessage .= "Der Benutzername muss zwischen 5 und 25 Zeichen lang sein!<br>";
        }

        if (mysqli_num_rows(SQLRequest($database, "SELECT * FROM users WHERE username = '$username_S'")) > 0) {
            $alertMessage .= "Es existiert bereits ein Account mit diesem Benutzernamen!<br>";
            $correctNick = false;
        }
    }

    if (isset($_POST['name'])) {
        $name_S = mysqli_real_escape_string($database, htmlentities($_POST['name']));

        // Validate length
        if (strlen($name_S) >= 5 && strlen($name_S) <= 50) {
            // Validate Contents
            if (preg_match("/^[a-zA-Z -]*$/", $name_S)) {
                $correctName = true;
            } else {
                $alertMessage .= "Dein Name darf nur Buchstaben oder einen Bindestrich enthalten!<br>";
            }
        } else {
            $alertMessage .= "Dein Name muss zwischen 5 und 50 Zeichen lang sein!<br>";
        }
    }

    if (isset($_POST['email'])) {
        $mail_S = mysqli_real_escape_string($database, htmlentities($_POST['email']));

        // Validate Mail
        if (filter_var($mail_S, FILTER_VALIDATE_EMAIL)) {
            $correctMail = true;
        } else {
            $alertMessage .= "Die Mail-Adresse hat ein ungültiges Format!<br>";
        }

        if (mysqli_num_rows(SQLRequest($database, "SELECT * FROM users WHERE mail = '$mail_S'")) > 0) {
            $alertMessage .= "Es existiert bereits ein Account mit dieser E-Mail Adresse!<br>";
            $correctMail = false;
        }
    }

    if (isset($_POST['password']) && isset($_POST['confirmPassword'])) {
        $password = $_POST['password'];
        if (strlen($password) >= 4) {
            if ($password === $_POST['confirmPassword']) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $correctPassword = true;
            } else {
                $alertMessage .= "Die Passwörter stimmen nicht überein!<br>";
            }
        } else {
            $alertMessage .= "Dein Password muss mindestens 4 Zeichen enthalten!<br>";
        }
    }

    $gender = "n";
    if (isset($_POST['gender'])) {
        $gender = $_POST['gender'];
    }

    $age = 0;
    if (isset($_POST['age'])) {
        $age = $_POST['age'];
    }

    $picture = false;
    $correctPicture = true;
    if (isset($_FILES['picture']) && $_FILES['picture']["tmp_name"]) {
        $picture = true;

        $check = getimagesize($_FILES['picture']["tmp_name"]);
        if ($check === false) {
            $alertMessage .= "Das Bild hat ein ungültiges Format!<br>";
            $correctPicture = false;
        }

        // 1048576 is 1MB
        if ($_FILES["picture"]["size"] > 5 * 1048576) {
            $alertMessage .= "Das Bild ist zu groß!<br>";
            $correctPicture = false;
        }
    }

    $challenge = md5(rand() . time());

    $correctData = $correctNick && $correctName && $correctMail && $correctPassword && $correctPicture;
    if ($correctData) {
        if (isset($username_S) && isset($name_S) && isset($mail_S) && isset($password)) {
            $ident = "username, name, mail, password, challenge";
            $values = "'$username_S', '$name_S', '$mail_S', '$password', '$challenge'";

            if ($gender != "n") {
                $ident .= ", gender";
                $values .= ", '$gender'";
            }

            if ($age > 0) {
                $ident .= ", age";
                $values .= ", '$age'";
            }

            if ($picture) {
                $ident .= ", picture";
                $values .= ", 1";
            }

            SQLRequest($database, "INSERT INTO users ($ident) VALUES($values)");

            if (1 == mysqli_affected_rows($database)) {
                // Upload picture by using the user id
                $userID = mysqli_fetch_assoc(SQLRequest($database, "SELECT id FROM users WHERE username = '$username_S'"))['id'];

                if ($picture && move_uploaded_file($_FILES['picture']["tmp_name"], "src/img/user/$userID.png")) {
                    $alertMessage .= "Profilbild wurde hochgeladen!<br>";
                } else if ($picture) {
                    $alertMessage .= "Profilbild konnte nicht hochgeladen werden!<br>";
                }

                // Get the ip and current folder
                $serverIP = gethostbyname(gethostname()) . "/" . str_replace("" . dirname(getcwd()) . "\\", "", getcwd());

                // Send mail confirmation
                $msg = "Bitte klicke <a href='$serverIP/confirm.php?mail=$mail_S&challenge=$challenge'>hier</a>, um deinen Account zu bestätigen!" . $serverIP;

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                if (mail($mail_S, "Anmeldung bestätigen", $msg, $headers)) {
                    $alertMessage .= "Die Mail mit dem Bestätigungslink wurde erfolgreich an $mail_S versandt!";
                } else {
                    $alertMessage .= "Das Senden der Mail hat nicht funktioniert! Klicke <a href=\"confirm.php?mail=$mail_S&challenge=$challenge\">hier</a>, um deinen Account zu bestätigen!";
                }
            }
        }
    }

    if ($alertMessage !== "") {
        $variant = $correctData ? 7 : 1;
        echo "<script>initAlertHandler(); displayAlert('Registrierung', '$alertMessage', $variant)</script>";
    }
?>
    <div class="center login">
        <h1>Registrieren</h1>
        <form method="post" action="register.php" enctype="multipart/form-data">
            <label for="nick">Benutzername <span class="required">*</span></label>
            <input type="text" name="nick" id="nick" class="width100" placeholder="Gebe hier deinen Benutzernamen ein" required autofocus minlength=5 maxlength="25" autocomplete="nickname" value="<?php echo htmlentities($_POST['nick'] ?? '');?>">
            <p id="nickInfo" class="inputNotif"></p>

            <label for="name">Name <span class="required">*</span></label>
            <input type="text" name="name" id="name" class="width100" placeholder="Gebe hier deinen Namen ein" required minlength=5 maxlength="50" autocomplete="name" value="<?php echo htmlentities($_POST['name'] ?? '');?>">
            <p id="nameInfo" class="inputNotif"></p>

            <label for="email">E-Mail <span class="required">*</span></label>
            <input type="email" name="email" id="email" class="width100" placeholder="Gebe hier deine E-Mail-Adresse ein" required maxlength="50" value="<?php echo htmlentities($_POST['email'] ?? '');?>">
            <p id="mailInfo" class="inputNotif"></p>

            <label for="password">Passwort <span class="required">*</span></label>
            <input type="password" name="password" id="password" class="width100" placeholder="Gebe hier dein Passwort ein" required minlength=4 autocomplete="new-password" value="<?php echo htmlentities($_POST['password'] ?? '');?>">

            <label for="confirmPassword">Passwort wiederholen<span class="required">*</span></label>
            <input type="password" name="confirmPassword" id="confirmPassword" class="width100" placeholder="Gebe hier nochmal dein Passwort ein" required minlength=4 autocomplete="off" value="<?php echo htmlentities($_POST['confirmPassword'] ?? '');?>">
            <p id="passwordInfo" class="inputNotif"></p>

            <label for="gender">Geschlecht</label>
            <select name="gender" id="gender" class="width100" autocomplete="sex">
                <option value="n">Bitte wähle dein Geschlecht aus</option>
                <script>
                    let prevGender = "<?php echo $_POST['gender'] ?? 'n' ?>";
                    let select = document.getElementById("gender");

                    select.options[1] = new Option("Männlich", "m", prevGender === "m", prevGender === "m");
                    select.options[2] = new Option("Weiblich", "w", prevGender === "w", prevGender === "w");
                    select.options[3] = new Option("Divers", "d", prevGender === "d", prevGender === "d");
                </script>
            </select>

            <label for="ageSelect">Alter</label>
            <select name="age" id="ageSelect" class="width100">
                <option value="n">Bitte wähle dein Alter aus</option>
                <script>
                    let prevAge = "<?php echo $_POST['age'] ?? 'n' ?>";
                    select = document.getElementById("ageSelect");

                    for (let i = 1; i < 100; i++) {
                        let selected = false;
                        if (prevAge !== "n" && i === parseInt(prevAge)) {
                            selected = true;
                        }

                        select.options[i] = new Option("" + i, "" + i, selected, selected);
                    }
                </script>
            </select>

            <input type="hidden" name="MAX_FILE_SIZE" value="50000">

            <label for="picture">Profilbild (.png, max. 5MB)</label>
            <input type="file" name="picture" id="picture" class="width100" accept="image/png">

            <input type="submit" value="Registrieren" class="width100">

            <p>Felder mit <span class="required">*</span> müssen ausgefüllt werden!</p>
        </form>
    </div>

</body>
</html>
