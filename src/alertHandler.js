let blur;

let alertBox;
let alertHeader;
let alertMessage;
let alertOptions;

let userName;
let gender;
let age;

function initAlertHandler() {
    blur = $("#blur");
    alertBox = $("#alertBox");
    alertHeader = $("#alertHeader");
    alertMessage = $("#alertMessage");
    alertOptions = $("#alertOptions");
}

function displayAlert(header, msg, variant = 1, roomID = 1, refreshUserInfo = true) {
    blur.removeClass("invisible");
    blur.addClass("visible");

    alertBox.empty();
    alertHeader.empty();
    alertMessage.empty();
    alertOptions.empty();

    alertHeader.append($.parseHTML("<h1>" + header + "</h1>"));
    alertMessage.append($.parseHTML("<p>" + msg + "</p>"));

    switch (variant) {
        case 1:
            alertOptions.append($.parseHTML("<button onclick='removeAlert()' class='alertCenterButton'>OK</button>"));
            break;
        case 2:
            let passwordForm = $.parseHTML("" +
                "<form>" +
                "<input id='roomPassword' type='password' placeholder='Gebe hier das Passwort des Raumes ein' required autocomplete='off'><br>" +
                "<input type='button' value='Abbrechen' onclick='removeAlert()' class='alertLeftButton width40'>" +
                "<input type='submit' value='Abschicken' class='alertRightButton width40'>" +
                "</form>"
            );

            alertOptions.append(passwordForm);

            $(alertOptions).find("form").submit(function (event) {
                event.preventDefault();

                $.ajax({
                    url: "databaseRequests.php",
                    type: "post",
                    data: {
                        changeRoom: roomID,
                        password: $("#roomPassword").val()
                    },

                    success: function (response) {
                        if (!response) {
                            displayAlert("Verifizierung", "Falsches Password!");
                        } else {
                            removeAlert();
                            refreshRooms();
                            refreshMessages(true);
                        }
                    }
                });
            });

            break;
        case 3:
            alertOptions.append($.parseHTML("<button class='alertLeftButton width40'>OK</button>"));
            alertOptions.append($.parseHTML("<button class='alertRightButton width40'>Login</button>"));

            let buttons = $(alertOptions).find("button");

            $(buttons[0]).click(function () {
                removeAlert(true);
            });

            $(buttons[1]).click(function () {
                location.assign("login.php");
            });

            break;
        case 4:
            let roomForm = $.parseHTML("" +
                "<form>" +
                "<input id='roomName' type='text' placeholder='Gebe hier den Namen des Raums ein' required minlength='3' maxlength='20'><br>" +
                "<input id='roomDesc' type='text' placeholder='Gebe hier eine kurze Beschreibung des Raums ein' maxlength='50' autocomplete='off'><br>" +
                "<input id='roomPassword' type='password' placeholder='Gebe hier das Passwort des Raums ein' autocomplete='new-password'><br>" +
                "<input type='button' value='Abbrechen' onclick='removeAlert()' class='alertLeftButton width40'>" +
                "<input type='submit' value='Abschicken' class='alertRightButton width40'>" +
                "</form>"
            );

            alertOptions.append(roomForm);

            $(alertOptions).find("form").submit(function (event) {
                event.preventDefault();

                $.ajax({
                    url: "databaseRequests.php",
                    type: "post",
                    data: {
                        createRoom: true,
                        roomName: $("#roomName").val(),
                        roomDesc: $("#roomDesc").val(),
                        password: $("#roomPassword").val()
                    },

                    success: function (response) {
                        if (response) {
                            displayAlert("Raumverwaltung", "Der Raum wurde erfolgreich erstellt!");
                        } else {
                            displayAlert("Raumverwaltung", "Der Raum konnte nicht erstellt werden. Eventuell existiert schon ein Raum mit dem gleichen Namen");
                        }
                    }
                });
            });
            break;
        case 5:
            let profileForm = $.parseHTML("" +
                "<form>" +
                "<input id='name' type='text' placeholder='Gebe hier deinen Namen ein' required minlength=5 maxlength='50' autocomplete='name'><br>" +
                "<select id='gender'>" +
                "<option value='n'>Bitte wähle ein Geschlecht aus</option>" +
                "<option value='m'>Männlich</option>" +
                "<option value='w'>Weiblich</option>" +
                "<option value='d'>Divers</option>" +
                "</select><br>" +
                "<select id='age'>" +
                "<option value='n'>Bitte wähle dein Alter aus</option>" +
                "</select><br>" +
                "<input type='button' value='Abbrechen' onclick='removeAlert()' class='alertLeftButton width25'>" +
                "<input type='button' value='Bild ändern' onclick='displayAlert(\"Bild bearbeiten\", \"\", 6)' class='alertCenterButton width25'>" +
                "<input type='submit' value='Speichern' class='alertRightButton width25'>" +
                "</form>"
            );

            let select = $(profileForm).find("#age");
            for (let i = 1; i < 100; i++) {
                select[0].options[i] = new Option("" + i, "" + i, false, false);
            }

            alertOptions.append(profileForm);

            // Retrieve user information
            if (refreshUserInfo) {
                $.ajax({
                    url: "databaseRequests.php",
                    type: "post",
                    data: {getUserInfo: true},

                    success: function (response) {
                        response = response.split(",");

                        userName = response[0];
                        gender = response[1];
                        age = response[2];

                        if (gender === "") {
                            gender = "n";
                        }

                        if (age === "") {
                            age = "n";
                        }

                        alertOptions.find("#name").val(userName);
                        alertOptions.find("#gender").val(gender);
                        alertOptions.find("#age").val(age);
                    }
                });
            } else {
                alertOptions.find("#name").val(userName);
                alertOptions.find("#gender").val(gender);
                alertOptions.find("#age").val(age);
            }

            $(alertOptions).find("#name").change(function () {
                userName = $(alertOptions).find("#name").val();
            });

            $(alertOptions).find("#gender").change(function () {
                gender = $(alertOptions).find("#gender").val();
            });

            $(alertOptions).find("#age").change(function () {
                age = $(alertOptions).find("#age").val();
            });

            $(alertOptions).find("form").submit(function (event) {
                event.preventDefault();

                console.log(age);
                $.ajax({
                    url: "databaseRequests.php",
                    type: "post",
                    data: {
                        updateUserInfo: true,
                        userName: userName,
                        userGender: gender,
                        userAge: age
                    },

                    success: function (response) {
                        if (response) {
                            displayAlert("Profil bearbeiten", "Die Änderungen wurden erfolgreich gespeichert!");
                        } else {
                            displayAlert("Profil bearbeiten", "Etwas ist schiefgelaufen! Bitte probiere es später erneut!");
                        }
                    }
                });
            });
            break;
        case 6:
            let pictureForm = $.parseHTML("" +
                "<form enctype='multipart/form-data'>" +
                "<input type='hidden' name='MAX_FILE_SIZE' value='50000'>" +
                "<label for='picture'>Bild hochladen</label>" +
                "<input type='file' id='picture' accept='image/png'>" +
                "<label for='removePicture'>Bild entfernen</label>" +
                "<input id='removePicture' type='checkbox'><br>" +
                "<input type='button' value='Abbrechen' onclick='displayAlert(\"Profil bearbeiten\", \"\", 5, 0, false)' class='alertLeftButton width40'>" +
                "<input type='submit' value='Speichern' class='alertRightButton width40'>" +
                "</form>"
            );

            alertOptions.append(pictureForm);

            $(alertOptions).find("form").submit(function (event) {
                event.preventDefault();

                let file = $("#picture").prop('files')[0];
                let checked = $("#removePicture").is(":checked");

                if (file) {
                    let formData = new FormData();
                    formData.append("newPicture", file);

                    $.ajax({
                        url: "databaseRequests.php",
                        type: "post",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,

                        success: function (response) {
                            if (response) {
                                displayAlert("Profil bearbeiten", "Dein Bild wurde erfolgreich gespeichert!", 5, 0, false);
                            } else {
                                displayAlert("Profil bearbeiten", "Dein Bild konnte nicht gespeichert werden! Bitte stelle sicher, dass es sich um ein Bild handelt und die Datei kleiner als 5MB ist.", 5, 0, false);
                            }
                        }
                    });
                } else if (checked) {
                    $.ajax({
                        url: "databaseRequests.php",
                        type: "post",
                        data: {
                            removePicture: true
                        },

                        success: function (response) {
                            if (response) {
                                displayAlert("Profil bearbeiten", "Dein Bild wurde erfolgreich entfernt!", 5, 0, false);
                            } else {
                                displayAlert("Profil bearbeiten", "Dein Bild konnte nicht entfernt werden!", 5, 0, false);
                            }
                        }
                    });
                }
            });
            break;
        case 7:
            alertOptions.append($.parseHTML("<button class='alertCenterButton'>Zur Hauptseite</button>"));

            alertOptions.find("button").click(function() {
                removeAlert();
                location.assign("index.php");
            });
            break;
    }

    alertBox.append(alertHeader);
    alertBox.append(alertMessage);
    alertBox.append(alertOptions);

    alertBox.removeClass("invisible");
    alertBox.addClass("visible");
}

function removeAlert(reloadPage = false) {
    blur.removeClass("visible");
    blur.addClass("invisible");

    alertBox.removeClass("visible");
    alertBox.addClass("invisible");

    if (reloadPage) {
        location.reload();
    }
}
