let userCount;

function initUserCount() {
    userCount = $("#userCount");

    refreshUserCount();

    window.setInterval("refreshUserCount()", 5000);
}

function refreshUserCount() {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: { refreshUserCount: true },
        
        success: function (response) {
            userCount.text("Aktuell " + (response === "1" ? "ist" : "sind") + " " + response + " Nutzer online!");
        },

        error: function () {
            console.log("Something went wrong while refreshing the user count!");
        }
    });
}