const authorsTable = document.getElementById("authors-table");

function deleteAuthor(rawId) {
    const d = confirm("You are about to delete author, this action can't be reverted. Be aware that you are about to kill a person in cold blood. Sooo do it?")
    if(d === true) {
        const rawIdArr = rawId.split("-");
        const authorId = rawIdArr[rawIdArr.length - 1];
        const endpoint = "/author-delete/" + authorId;
        postAjax(endpoint, "", postDeleteAuthor);
    }
}

function postDeleteAuthor(data) {
    if(parseInt(data.error) === 1) {
        console.error(data.message);
        addNotification("error", "Could not delete author")
    } else {
        const row = document.getElementById("row-" + data.author_id);
        authorsTable.tBodies[0].removeChild(row);
        addNotification("success", data.message);
    }
}