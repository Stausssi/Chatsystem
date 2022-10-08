let rooms;

function initRoomHandler() {
    rooms = $("#rooms");

    window.setInterval("refreshRooms()", 1500);

    refreshRooms();
}

function changeRoom(roomID, hasPassword = false) {
    if (hasPassword) {
        displayAlert("Verifizierung", "Bitte gebe das Passwort ein", 2, roomID);
    } else {
        $.ajax({
            url: "databaseRequests.php",
            type: "post",
            data: { changeRoom: roomID },

            success: function (response) {
                if (response) {
                    refreshRooms();
                    refreshMessages(true);
                }
            }
        });
    }
}

function refreshRooms() {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: {refreshRooms: true},

        success: function (response) {
            rooms.empty();
            rooms.append($.parseHTML(response));
        }
    });
}