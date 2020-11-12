function addNotification(type, text) {
    const notification = document.getElementById("notification");
    const alert = document.getElementById("alert");
    notification.innerHTML = text;
    if(type === "error") {
        alert.className = "alert alert-danger";
    } else if(type === "success") {
        alert.className = "alert alert-success";
    }

    $("#alert").show().delay(5000).fadeOut();
}