<?php

    if (isset($_POST['verifySession'])) {
        session_start();

        // Regenerate Session ID every 15 Minutes
        if (!isset($_SESSION['createdAt'])) {
            $_SESSION['createdAt'] = time();
        } else if (time() - $_SESSION['createdAt'] > 15 * 60) {
            session_regenerate_id();
            $_SESSION['createdAt'] = time();
        }

        // Auto Logout after 15 minutes of inactivity
        if (isset($_SESSION['lastActive']) && isset($_SESSION['loggedIn'])) {
            if ($_SESSION['loggedIn'] && (time() - $_SESSION['lastActive']) > 15 * 60) {
                echo "afk: true";
            } else {
                echo "afk: " . (time() - $_SESSION['lastActive']);
            }
        }

        echo ",";

        // Log user out if their ip changed
        if (isset($_SESSION['ip'])) {
            if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
                echo "ip: true";
            }
        }

        unset($_POST['verifySession']);
    }

    function SQLRequest($database, $request) {
        $result = mysqli_query($database, $request) or die (mysqli_error($database));
        return $result ? $result : null;
    }

    function getUserCount($database, $roomID) {
        return mysqli_fetch_assoc(SQLRequest($database, "SELECT users FROM rooms WHERE id = $roomID"))['users'];
    }

    function setUserCount($database, $userCount, $roomID) {
        SQLRequest($database, "UPDATE rooms SET users = $userCount WHERE id = $roomID");
    }

    function decreaseUserCount($database, $roomID) {
        $userCount = getUserCount($database, $roomID) - 1;
        SQLRequest($database, "UPDATE rooms SET users = $userCount WHERE id = $roomID");
    }

    function increaseUserCount($database, $roomID) {
        $userCount = getUserCount($database, $roomID) + 1;

        SQLRequest($database, "UPDATE rooms SET users = $userCount WHERE id = $roomID");
    }

    function updateUserActive($database, $userID) {
        $time = time();
        SQLRequest($database, "UPDATE users SET lastActive = $time WHERE id = $userID");
    }

    function userLogout($database, $userID, $roomID) {
        SQLRequest($database, "UPDATE users SET active = 0, inRoom = 0 WHERE id = $userID");

        if ($roomID > 0) {
            // Decrease User count of room
            decreaseUserCount($database, $roomID);
        }
    }