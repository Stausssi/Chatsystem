<?php
    include "util.php";
    $database = require "connect.php";

    session_start();

    $requestHandled = false;
    $requestType = 0;

    // Set default room
    if (!isset($_SESSION['roomID'])) {
        $_SESSION['roomID'] = 1;
    }

    $roomID = $_SESSION['roomID'];


    /*
     * Validate user information
     *
     */
    if (isset($_POST['validateNickname'])) {
        $requestHandled = true;
        echo usernameTaken($database, mysqli_real_escape_string($database, htmlentities($_POST['nick'])));

        unset($_POST['validateNickname']);
    }

    if (isset($_POST['validateMail'])) {
        $requestHandled = true;
        echo mailTaken($database, mysqli_real_escape_string($database, htmlentities($_POST['mail'])));

        unset($_POST['validateMail']);
    }


    /*
     * Other Requests
     *
     */
    if (isset($_POST['refreshUserCount'])) {
        $requestHandled = true;

        echo mysqli_fetch_assoc(SQLRequest($database, "SELECT SUM(active = 1) as userCount FROM users"))['userCount'];

        // Log out users who quit by closing the window
        $time = time();
        $result = mysqli_fetch_all(SQLRequest($database, "SELECT id, inRoom FROM users WHERE lastActive < ($time - 15 * 60) AND active = 1"));

        foreach ($result as $item) {
            userLogout($database, $item[0], $item[1]);
        }

        unset($_POST['refreshUserCount']);
    }

    if (isset($_POST['sendMessage'])) {
        $requestHandled = true;
        $requestType = 1;

        $text = mysqli_real_escape_string($database, htmlentities($_POST['message']));
        $userID = $_SESSION['userID'];
        $background = $_SESSION['color'];

        $time = date("Y.m.d-H:i");

        SQLRequest($database, "INSERT INTO messages (userID, roomID, text, sendAt, color) VALUES('$userID', '$roomID', '$text', '$time', '$background')");

        unset($_POST['sendMessage']);
    }

    if (isset($_POST['refreshMessages'])) {
        $requestHandled = true;

        $messages = mysqli_fetch_all(SQLRequest($database, "SELECT userID, text, sendAt, color FROM messages WHERE roomID = $roomID"));

        $messageCount = 0;
        foreach ($messages as $message) {
            $messageCount += 1;

            $result = mysqli_fetch_assoc(SQLRequest($database, "SELECT username, picture, id FROM users WHERE id = $message[0]"));

            $user = $result['username'];
            $hasPicture = $result['picture'];

            $text = $message[1];
            $sendAt = $message[2];

            $date = explode(" ", $sendAt)[0];
            $time = explode(":", explode(" ", $sendAt)[1]);
            $time = $time[0] . ":" . $time[1];

            if ($date === date("Y-m-d")) {
                $sendAt = "heute";
            } else if (explode("-", $date)[0] === date("Y")) {
                $date = explode("-", $date);
                $sendAt = "am " . $date[2] . "." . $date[1];
            } else {
                $date = explode("-", $date);
                $sendAt = "am " . $date[2] . "." . $date[1] . "." . $date[0];
            }

            $background = $message[3];
            $color = $background === "#ffffff" ? "#000000" : "#ffffff";

            $classes = "message";
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $user == $_SESSION['username']) {
                $classes .= " ownMessage";
            }

            $imgPath = "src/img/user/";
            if ($hasPicture) {
                $imgPath .= $result['id'] . ".png";
            } else {
                if ($color === "#ffffff") {
                    $imgPath .= "default_white.png";
                } else {
                    $imgPath .= "default.png";
                }
            }

            echo "<div class='messageContainer'><div id='message$messageCount' class='$classes' style='background-color: $background; color: $color'><div class='messageInfo'><img src='$imgPath' alt='Profilbild' class='profilePicture'><p class='messageInfo'>$user, $sendAt um $time</p></div><p class='messageText'>$text</p></div></div>";
        }

        unset($_POST['refreshMessages']);
    }

    if (isset($_POST['refreshRooms'])) {
        $requestHandled = true;

        $rooms = mysqli_fetch_all(SQLRequest($database, "SELECT id, name, `desc`, password, users, autoDelete FROM rooms"));

        foreach ($rooms as $room) {
            $id = $room[0];

            if ($room[4] < 1 && $room[5]) {
                SQLRequest($database, "DELETE FROM rooms WHERE id = $id");

                SQLRequest($database, "DELETE FROM messages WHERE roomID = $id");
            } else {
                $classes = "room";
                $nameClasses = "roomName";

                $title = "Durch Klicken beitreten";
                if ($room[3] != null && $id != $roomID) {
                    $nameClasses .= " protected";
                    $title .= " (Passwordgeschützt)";
                }

                if ($roomID == $id) {
                    $classes .= " active";
                }

                $name = $room[1];
                $desc = $room[2];
                $userCount = $room[4];

                $onClick = "changeRoom($id";

                if ($room[3] != null) {
                    $onClick .= ", true";
                }

                $onClick .= ")";

                echo "<div class='$classes' onclick='$onClick' title='$title'><p class='$nameClasses' onclick='$onClick'>$name ($userCount)</p><p class='roomDesc' onclick='$onClick'><i>$desc</i></p></div>";
            }
        }

        unset($_POST['refreshRooms']);
    }

    if (isset($_POST['createRoom'])) {
        $requestHandled = true;
        $requestType = 1;

        $name = mysqli_real_escape_string($database, htmlentities($_POST['roomName']));

        if (mysqli_num_rows(SQLRequest($database, "SELECT * FROM rooms WHERE name = '$name'"))) {
            echo false;
            return;
        }

        $desc = mysqli_real_escape_string($database, htmlentities($_POST['roomDesc']));

        if ($_POST['password'] !== "") {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            $password = "";
        }

        SQLRequest($database, "INSERT INTO rooms (name, `desc`, password) VALUES('$name', '$desc', '$password')");

        $newRoomID = mysqli_fetch_assoc(SQLRequest($database, "SELECT id FROM rooms WHERE name = '$name'"))['id'];

        $_POST['changeRoom'] = $newRoomID;

        echo true;

        unset($_POST['createRoom']);
    }

    if (isset($_POST['changeRoom'])) {
        $requestHandled = true;
        $requestType = 1;

        $roomChanged = true;

        $newRoomID = $_POST['changeRoom'];

        $dataReceived = mysqli_fetch_assoc(SQLRequest($database, "SELECT * FROM rooms WHERE id = $newRoomID"));

        if ($dataReceived['password'] != null) {
            if (!password_verify($_POST['password'], $dataReceived['password'])) {
                $roomChanged = false;
            }
        }

        if ($roomChanged) {
            echo true;
            increaseUserCount($database, $newRoomID);

            decreaseUserCount($database, $roomID);

            $_SESSION['roomID'] = $_POST['changeRoom'];

            SQLRequest($database, "UPDATE users SET inRoom = $newRoomID WHERE id = " . $_SESSION['userID']);
        }

        unset($_POST['changeRoom']);
    }

    if (isset($_POST['changeColor'])) {
        $requestHandled = true;
        $requestType = 1;

        $background = $_POST['color'];
        $_SESSION['color'] = $background;

        $userID = $_SESSION['userID'];

        SQLRequest($database, "UPDATE users SET color = '$background' WHERE id = $userID");

        unset($_POST['changeColor']);
    }

    if (isset($_POST['getUserInfo'])) {
        $requestHandled = true;
        $requestType = 1;

        $result = mysqli_fetch_all(SQLRequest($database, "SELECT name, gender, age FROM users WHERE id = " . $_SESSION['userID']));

        echo $result[0][0];
        echo ",";
        echo $result[0][1];
        echo ",";
        echo $result[0][2];


        unset($_POST['getUserInfo']);
    }

    if (isset($_POST['updateUserInfo'])) {
        $requestHandled = true;
        $requestType = 1;

        $name = mysqli_real_escape_string($database, htmlentities($_POST['userName']));
        $gender = $_POST['userGender'];
        $age = $_POST['userAge'];
        
        $set = "name = '$name'";
        $set .= ", ";

        if ($gender !== "n") {
            $set .= "gender = '$gender'";
        } else {
            $set .= "gender = null";
        }
        $set .= ", ";

        if ($age !== "n") {
            $set .= "age = $age";
        } else {
            $set .= "age = null";
        }

        echo SQLRequest($database, "UPDATE users SET $set WHERE id = " . $_SESSION['userID']);

        echo (1 == mysqli_affected_rows($database));

        unset($_POST['updateUserInfo']);
    }

    if (isset($_FILES['newPicture'])) {
        $requestHandled = true;
        $requestType = 1;

        if (0 < $_FILES['newPicture']["error"]) {
            echo false;
        } else {
            $check = getimagesize($_FILES['newPicture']["tmp_name"]);
            if ($check === false) {
                echo false;
                return;
            }

            // 1048576 is 1MB
            if ($_FILES['newPicture']["size"] > 5 * 1048576) {
                echo false;
                return;
            }

            move_uploaded_file($_FILES['newPicture']["tmp_name"], "src/img/user/" . $_SESSION['userID'] .".png");

            SQLRequest($database, "UPDATE users SET picture = 1 WHERE id = " . $_SESSION['userID']);
            echo true;
        }
    }

    if (isset($_POST['removePicture'])) {
        $requestHandled = true;
        $requestType = 1;

        SQLRequest($database, "UPDATE users SET picture = 0 WHERE id = " . $_SESSION['userID']);

        echo true;
    }

    // Update inactive-time
    if (!$requestHandled) {
        header("location: index.php");
    } else if ($requestType == 1) {
        $_SESSION['lastActive'] = time();
        updateUserActive($database, $_SESSION['userID']);
    }

    function usernameTaken($database, $username) {
        return mysqli_num_rows(SQLRequest($database, "SELECT * FROM users WHERE username = '$username'")) > 0;
    }

    function mailTaken($database, $mail) {
        return mysqli_num_rows(SQLRequest($database, "SELECT * FROM users WHERE mail = '$mail'")) > 0;
    }
