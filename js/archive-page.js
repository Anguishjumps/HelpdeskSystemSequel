// Searchbar
// The script is for the search bar, to output the correct table depending on the search input.
function archiveSearch() {
    var search = document.getElementById('archive-search').value.toLowerCase();
    let select = document.querySelector('#search-dropdown');
    let selectedOption = select.options[select.selectedIndex].text;

    // find search-dropdown selected.
    let tableElem = document.getElementsByClassName("TIC-ID");
    let tableElem1 = document.getElementsByClassName("Prob");
    let tableElem2 = document.getElementsByClassName("SOL");
    let tableElem3 = document.getElementsByClassName("PTYPE");
    let tableElem4 = document.getElementsByClassName("CREATE-DATE");
    let tableElem5 = document.getElementsByClassName("RESOLVE-DATE");
    let tableRow = document.getElementsByClassName("tableRow");


    if (selectedOption == "Ticket ID") {

        for (let i = 0; i < tableElem.length; i++) {
            if (tableElem[i].innerHTML.toLowerCase() == search) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Problem") {

        for (let i = 0; i < tableElem1.length; i++) {
            if (tableElem1[i].innerHTML.toLowerCase().includes(search)) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Solution (ID)") {

        for (let i = 0; i < tableElem2.length; i++) {
            if (tableElem2[i].innerHTML.toLowerCase().includes(search)) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Problem Type") {

        for (let i = 0; i < tableElem3.length; i++) {
            if (tableElem3[i].innerHTML.toLowerCase().includes(search)) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Created Date") {

        for (let i = 0; i < tableElem4.length; i++) {
            if (tableElem4[i].innerHTML.toLowerCase().includes(search)) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    } else if (selectedOption == "Resolved Date") {

        for (let i = 0; i < tableElem5.length; i++) {
            if (tableElem5[i].innerHTML.toLowerCase().includes(search)) {
                tableRow[i].style.display = "";
            } else if (search == "") {
                tableRow[i].style.display = "";
            } else {
                tableRow[i].style.display = "None";
            }
        }
    }
}


function rowClickArchiveModal(ticketID) {
    var rowData = document.getElementById("infoModal");
    var archiveProblem = document.getElementById("archiveProblem");
    var archiveSolution = document.getElementById("archiveSolution");
    var archiveReporter = document.getElementById("archiveReporter");
    var archiveReporterPhone = document.getElementById("archiveReporterPhone");
    var archiveOperator = document.getElementById("archiveOperator");
    var archiveOperatorPhone = document.getElementById("archiveOperatorPhone");
    var archiveSpecialist = document.getElementById("archiveSpecialist");
    var archiveSpecialistPhone = document.getElementById("archiveSpecialistPhone");

    var archiveSpecialistLabel = document.getElementById("archiveSpecialistLabel");


    // When the user clicks on the button, open the modal



    // Ajax request to add comment to ticket
    ajax.post('./ajax-callable-functions.php', { functionname: 'addArchiveTicketInfo', arguments: [ticketID] }, obj => {
        archiveProblem.textContent = "Problem: ";
        archiveProblem.textContent += JSON.parse(obj).result['TicketDescription'] + '\n';
        archiveSolution.textContent = "Solution: ";
        archiveSolution.textContent += JSON.parse(obj).result['Explanation'] + '\n';


        archiveReporter.value = "#" + JSON.parse(obj).result['ReporterID'] + " " + JSON.parse(obj).result['Reporter'] +
            ' (' + JSON.parse(obj).result['Job'] + ') ';
        archiveReporterPhone.value = JSON.parse(obj).result['PhoneNo'];


        archiveOperator.value = "#" + JSON.parse(obj).result['OperatorID'] + ' ' + JSON.parse(obj).result['Operator'];
        archiveOperatorPhone.value = JSON.parse(obj).result['OperatorPhoneNo'];


        if (JSON.parse(obj).result['AssignedSpecialistID'] == null || JSON.parse(obj).result['AssignedSpecialist'] == null) {
            archiveSpecialist.style.display = "none";
            archiveSpecialistLabel.style.display = "none";
            archiveSpecialistPhoneLabel.style.display = "none";
            archiveSpecialistPhone.style.display = "none";



        } else {
            archiveSpecialistLabel.style.display = "block";
            archiveSpecialistPhoneLabel.style.display = "block";
            archiveSpecialistPhone.style.display = "block";
            archiveSpecialist.style.display = "block";
            if (JSON.parse(obj).result['SpecialistJob'] != "External Specialist") {
                archiveSpecialist.value = "#" + JSON.parse(obj).result['AssignedSpecialistID'] + ' ' + JSON.parse(obj).result['AssignedSpecialist'];
            } else {
                archiveSpecialist.value = "#" + JSON.parse(obj).result['AssignedSpecialistID'] + ' ' + JSON.parse(obj).result['AssignedSpecialist'] +
                    ' (' + JSON.parse(obj).result['SpecialistJob'] + ') ';

            }
            archiveSpecialistPhone.value = JSON.parse(obj).result['SpecialistPhoneNo'];
        }
        // console.log(JSON.parse(obj));
    });

    infoModal.style.display = "block";
}


// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
    if (event.target.className == "modal fade") {
        infoModal.style.display = "none";
    }
}


// When user presses "ESC" on keyboard, close modal
window.onkeydown = (event) => {
    if (event.key == "Escape" && (infoModal.style.display == "block")) {
        infoModal.style.display = "none";
    }
};


function closeRowClickModal() {
    infoModal.style.display = "none";
}