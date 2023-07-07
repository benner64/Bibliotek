function SearchBooks(){
    let bookSearcherElement = document.querySelector('#bookSearcher');
    let datalistElement = document.querySelector('#recommendations');
    let searchText = bookSearcherElement.value; 

    if(searchText != "")
    {
        fetch('/Book/Search/' + searchText)
        .then(response => {
            return response.json();
        })
        .then(books => {
            console.log(books);
            datalistElement.innerHTML = "";
            books.forEach(book => {
                datalistElement.insertAdjacentHTML('beforeend', `<option value="` + book.Name + `"><a href="/Book/Update/` + book.Id + `">` + book.Name + `</a></option>`);
            });
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    console.log("Hello World!");
    let test = document.querySelector('#bookSearcher');
    test.addEventListener("keyup", () => SearchBooks());
  });