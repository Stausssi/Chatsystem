let nickname;
let nickInfo;

let name;
let nameInfo;

let email;
let mailInfo;

let password;
let confirmPassword;
let passwordInfo;

function initInputs() {
    nickname = $("#nick");
    nickname.change(function () {
        validateUsername();
    });
    nickInfo = $("#nickInfo");

    name = $("#name");
    name.change(function () {
        validateName();
    });
    nameInfo = $("#nameInfo");

    email = $("#email");
    email.change(function () {
        validateEmail();
    });
    mailInfo = $("#mailInfo");

    password = $("#password");
    password.change(function () {
        comparePasswords()
    });

    confirmPassword = $("#confirmPassword");
    confirmPassword.change(function () {
        comparePasswords()
    });
    passwordInfo = $("#passwordInfo");

    if (nickname.val().length > 0) {
        validateUsername();
    }

    if (name.val().length > 0) {
        validateName();
    }

    if (email.val().length > 0) {
        validateEmail();
    }

    if (password.val().length > 0) {
        comparePasswords();
    }
}

function validateUsername() {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: {
            validateNickname: true,
            nick: nickname.val().trim()
        },

        success: function (response) {
            if (response) {
                nickname.removeClass("valid");
                nickname.addClass("invalid");
                nickInfo.text("Dieser Benutzername ist bereits vergeben!");
            } else {
                let isValid = false;

                if (validateRegEx("username")) {
                    isValid = true;

                    if (5 <= nickname.val().length && nickname.val().length <= 25) {
                        isValid = true;
                    } else if (nickname.val().length > 0) {
                        isValid = false;
                        nickInfo.text("Der Benutzername muss zwischen 5 und 25 Zeichen lang sein!");
                    } else {
                        nickname.removeClass("valid");
                        nickname.removeClass("invalid");
                        nickInfo.text("");
                        return;
                    }
                } else {
                    nickInfo.text("Der Benutzername darf nur Buchstaben und Zahlen enthalten!");
                }

                if (isValid) {
                    nickname.removeClass("invalid");
                    nickname.addClass("valid");
                    nickInfo.text("");
                } else {
                    nickname.removeClass("valid");
                    nickname.addClass("invalid");
                }
            }
        }
    });
}

function validateName() {
    let isValid = false;

    if (validateRegEx("name")) {
        isValid = true;

        if (5 <= name.val().length && name.val().length <= 50) {
            isValid = true;
        } else if (name.val().length > 0) {
            isValid = false;
            nameInfo.text("Der Name muss zwischen 5 und 50 Zeichen lang sein!");
        } else {
            name.removeClass("valid");
            name.removeClass("invalid");
            nameInfo.text("");
            return;
        }
    } else {
        nameInfo.text("Der Benutzername darf nur Buchstaben und einen Bindestrich enthalten!");
    }

    if (isValid) {
        name.removeClass("invalid");
        name.addClass("valid");
        nameInfo.text("");
    } else {
        name.removeClass("valid");
        name.addClass("invalid");
    }
}

function validateEmail() {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: {
            validateMail: true,
            mail: email.val().trim()
        },

        success: function (response) {
            if (response) {
                email.removeClass("valid");
                email.addClass("invalid");
                mailInfo.text("Es existiert bereits ein Nutzer mit dieser E-Mail Adresse!");
            } else {
                let isValid = false;

                if (email.val().length > 0) {
                    if (validateRegEx("mail") ) {
                        isValid = true;

                        if (email.val().length <= 50) {
                            isValid = true;
                        } else {
                            isValid = false;
                            mailInfo.text("Deine E-Mail-Adresse darf aus maximal 50 Zeichen bestehen!");
                        }
                    } else {
                        mailInfo.text("Bitte gebe eine valide E-Mail-Adresse ein!");
                    }
                } else {
                    email.removeClass("valid");
                    email.removeClass("invalid");
                    mailInfo.text("");
                    return;
                }


                if (isValid) {
                    email.removeClass("invalid");
                    email.addClass("valid");
                    mailInfo.text("");
                } else {
                    email.removeClass("valid");
                    email.addClass("invalid");
                }
            }
        }
    });
}

function comparePasswords() {
    let isValid = null;

    if (password.val().length >= 4) {
        if (confirmPassword.val() === password.val()) {
            isValid = true;
        } else if (confirmPassword.val().length > 0) {
            isValid = false;
            passwordInfo.text("Die beiden Passwörter stimmen nicht überein!")
        }
    } else if (password.val().length > 0){
        isValid = false;
        passwordInfo.text("Dein Passwort muss aus mindestens 4 Zeichen bestehen!");
    }

    if (isValid !== null) {
        if (isValid) {
            password.removeClass("invalid");
            password.addClass("valid");

            confirmPassword.removeClass("invalid");
            confirmPassword.addClass("valid");

            passwordInfo.text("");
        } else {
            password.removeClass("valid");
            password.addClass("invalid");

            confirmPassword.removeClass("valid");
            confirmPassword.addClass("invalid");
        }
    } else {
        password.removeClass("valid");
        password.removeClass("invalid");

        confirmPassword.removeClass("valid");
        confirmPassword.removeClass("invalid");

        passwordInfo.text("");
    }
}

function validateRegEx(type) {
    let text = "";
    let re;

    if (type === "username") {
        re = /^[a-zA-Z0-9]*$/;
        text = nickname.val();
    } else if (type === "name") {
        re = /^[a-zA-Z -]*$/;
        text = name.val();
    } else if (type === "mail") {
        // From: https://hexillion.com/samples/
        re = /^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/;
        text = email.val();
    } else {
        return false;
    }

    return re.test(text.toLowerCase());
}