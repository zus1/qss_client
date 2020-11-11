const booksTable = document.getElementById("books-table");
const authorId = document.getElementById("author_id").value;

function deleteBook(rawId) {
    const d = confirm("You are about to delete a book, this action can't be reverted. Are you sure about this? Like rely sure? Like for sure sure??")
    if(d === true) {
        const rawIdArr = rawId.split("-");
        const bookId = rawIdArr[rawIdArr.length - 1];
        const endpoint = "/book-delete/author/" + authorId + "/book/" + bookId;
        console.log(endpoint);
        postAjax(endpoint, "", postDeleteBook);
    }
}

function postDeleteBook(data) {
    if(parseInt(data.error) === 1) {
        console.error(data.message);
        addNotification("error", "Could not delete book")
    } else {
        const row = document.getElementById("row-" + data.book_id);
        booksTable.tBodies[0].removeChild(row);
        addNotification("success", data.message);
    }
}