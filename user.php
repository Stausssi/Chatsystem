<?php

class user {
    private int $id;
    private string $username;
    private string $name;
    private string $mail;
    private string $password;
    private string $gender = "n";
    private int $age = 0;
    private $picture = null;
    private string $challenge;

    public function getId(): int{
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername($username): void {
        $this->username = $username;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName($name): void {
        $this->name = $name;
    }

    public function getMail(): string {
        return $this->mail;
    }

    public function setMail($mail): void {
        $this->mail = $mail;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword($password): void {
        $this->password = $password;
    }

    public function getGender(): string {
        return $this->gender;
    }

    public function setGender($gender): void {
        $this->gender = $gender;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function setAge($age): void {
        $this->age = $age;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function setPicture($picture): void {
        $this->picture = $picture;
    }

    public function getChallenge(): string {
        return $this->challenge;
    }

    public function setChallenge(string $challenge): void {
        $this->challenge = $challenge;
    }


    public function addToDatabase() {
        require("connect.php");
        $ident = "username, name, mail, password, challenge";
        $values = "'$this->username', '$this->name', '$this->mail', '$this->password', '$this->challenge'";

        if ($this->gender != "n") {
            $ident .= ", `gender`";
            $values .= ", '$this->gender'";
        }

        if ($this->age > 0) {
            $ident .= ", age";
            $values .= ", '$this->age'";
        }

        $request = "INSERT INTO users (" . $ident . ") VALUES(" . $values . ");";
        mysqli_query($database, $request) or die(mysqli_error($database));

        if (1 == mysqli_affected_rows($database)) {
            // Send mail confirmation
            echo "<a href=\"confirm.php?mail=$this->mail&challenge=$this->challenge\">Anmeldung bestätigen</a>";
        }
    }
}
