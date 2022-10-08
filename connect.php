<?php
    $database = mysqli_connect("localhost", "root", "", "chat") or die("Couldn't connect chat database!");
    return $database;