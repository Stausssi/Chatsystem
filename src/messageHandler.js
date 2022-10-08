let chatBox;
let textInput;
let lastMessage;
let messageNotif;

function initMessages() {
    chatBox = $("#messages");

    textInput = $("#textInput");
    textInput.keypress(function (e) {
        if (e.which === 13) {
            $("#sendMsg").click();
            return false;
        }

    });

    messageNotif = $("#notifier");
    messageNotif.width(chatBox.width());

    window.setInterval("refreshMessages()", 1500);

    refreshMessages(true);
}

function sendMessage() {
    let message = textInput.val().trim();

    if (message.length > 0) {
        $.ajax({
            url: "databaseRequests.php",
            type: "post",
            data: {message: message, sendMessage: true},

            success: function () {
                refreshMessages();
            }
        });
    }
    textInput.val("");
}

function refreshMessages(scrollToBottom = false) {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: {refreshMessages: true},

        success: function (response) {
            messageNotif.empty();

            chatBox.empty();
            chatBox.append(messageNotif);
            chatBox.append($.parseHTML(response));

            let newMessage;
            let count = 0;

            while ($("#message" + (count + 1)).length > 0) {
                count += 1;
                newMessage = $("#message" + count);
            }

            if (count > 0) {
                if (!lastMessage) {
                    lastMessage = newMessage;
                }

                scrollToBottom = scrollToBottom || chatBox.scrollTop() === chatBox[0].scrollHeight - chatBox[0].clientHeight;

                if (lastMessage.attr("id") !== newMessage.attr("id") || scrollToBottom) {
                    if (newMessage.hasClass("ownMessage") || scrollToBottom) {
                        newMessage[0].scrollIntoView();
                        lastMessage = newMessage;
                    } else {
                        notifyUser("Neue Nachrichten vorhanden!");
                    }
                }
            } else {
                notifyUser("Aktuell sind noch keine Nachrichten vorhanden!");
            }
        }
    });
}

function notifyUser(message) {
    messageNotif.append($.parseHTML("<p>" + message + "</p>"));
}
