let colorPicker;

function initColorPicker() {
    colorPicker = $("#colorPicker");
}

function changeColor() {
    $.ajax({
        url: "databaseRequests.php",
        type: "post",
        data: {color: colorPicker.val(), changeColor: true},
    });
}