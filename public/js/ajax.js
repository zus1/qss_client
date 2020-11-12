function getAjax(endpoint, callback="")  {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if(callback !== "") {
                callback(JSON.parse(this.responseText));
            }
        }
    };

    const http = getHttpParams();
    let base = http.protocol + "//" + http.host;
    if(http.port) {
       base = base + ":" + http.port;
    }

    xhttp.open("GET", base + endpoint, true);
    xhttp.send();
}

function postAjax(endpoint, data="", callback) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            callback(JSON.parse(this.responseText));
        }
    };

    const http = getHttpParams();
    xhttp.open("POST", endpoint, true);
    if(data !== "") {
        xhttp.send(data);
    } else {
        xhttp.send();
    }
}

function getHttpParams() {
    return {
        "host": window.location.hostname,
        "protocol": window.location.protocol,
        "port" : window.location.port,
    }
}