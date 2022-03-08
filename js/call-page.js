// Searchbar
// The script is for the search bar, to output the correct table depending on the search input.
function callSearch() {
    var search = document.getElementById('call-search').value.toLowerCase();
    let select = document.querySelector('#search-dropdown');
    let selectedOption = select.options[select.selectedIndex].text;

    // find search-dropdown selected.
    let tableElem = document.getElementsByClassName("call-id");
    let tableElem1 = document.getElementsByClassName("caller-id");
    let tableElem2 = document.getElementsByClassName("ticket-id");
    let tableElem3 = document.getElementsByClassName("call-time");
    let tableElem4 = document.getElementsByClassName("reason");
    let tableRow = document.getElementsByClassName("tableRow");

    if (selectedOption == "ID") {

        for (let i = 0; i < tableElem.length; i++) {
            if (tableElem[i].innerHTML.toLowerCase() == search) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Caller ID") {

        for (let i = 0; i < tableElem1.length; i++) {
            if (tableElem1[i].innerHTML.toLowerCase() == search) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Ticket ID") {

        for (let i = 0; i < tableElem2.length; i++) {
            if (tableElem2[i].innerHTML.toLowerCase() == search) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    }
}


/**
 * Check if characters in string are valid and aren't used for script injection
 * 
 * @param {String} string to check characters of 
 * @returns true if string is valid, false otherwise
 */
const validateCharacters = string => !string.match(/[|&;$%@"<>()+]/g);

function checkNewCallForm() {
    // Check all inputs for illegal characters
    let form = document.forms["new-call-form"];
    let valid = true;
    ["reason"].forEach(input => {
        if (!validateCharacters(form[input].value)) {
            alertBanner('ERROR: Illegal character(s) entered in input field.');
            closeModals();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            valid = false;
            return;
        }
    });
    return valid;
}