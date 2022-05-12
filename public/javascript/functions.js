function getProblemName(id, problemTypes) {
    console.log("hello")
        // for (i in problemTypes) {
        //     if (id == problemTypes[i].ID) {

    //     }
    // }

}



/**
 * Check if characters in string are valid and aren't used for script injection
 * 
 * @param {String} string to check characters of 
 * @returns true if string is valid, false otherwise
 */
const validateCharacters = string => !string.match(/[|&;$%@"<>()+]/g);


// Close ticket modals when close button is clicked
function closeModal() {

    var viewTicketModal = document.getElementById("view-ticket-modal");
    viewTicketModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
    if (event.target.className == "modal fade" || event.target.className == "modal-form") {
        createTicketModal.style.display = "none";
        viewTicketModal.style.display = "none";
    }
}

// When user presses "ESC" on keyboard, close modal
window.onkeydown = (event) => {
    if (event.key == "Escape" && (createTicketModal.style.display == "block" || viewTicketModal.style.display == "block")) {
        createTicketModal.style.display = "none";
        viewTicketModal.style.display = "none";
    }
};


/**
 * Show ticket modal when ticket card is clicked
 * 
 * @param {Number} ticketID  Unique ID of ticket
 */
function showTicket(ticketID) {

    // Show view ticket modal
    var viewTicketModal = document.getElementById("view-ticket-modal");
    viewTicketModal.style.display = "block";

    // Ajax request to get ticket details from database
    $.post('/specialist/show', result = {
        ID: $("#" + ticketID).data("id"),
        CreatedTimestamp: $("#" + ticketID).data("createdtimestamp"),
        UserID: $("#" + ticketID).data("reporterid"),
        AssignedSpecialistID: $("#" + ticketID).data("assignedspecialistid"),
        TicketPriority: $("#" + ticketID).data("priority"),
        TicketState: $("#" + ticketID).data("state"),
        TypeID: $("#" + ticketID).data("maintag"),
        TicketDescription: $("#" + ticketID).data("description"),
        SolutionDescription: $("#" + ticketID).data("resolveddescription"),
        ResolveDate: $("#" + ticketID).data("resolvedate"),


    }, obj => {

        // alert(result["AssignedSpecialistID"]);
        Object.keys(result).forEach(key => {
            console.log(key)
                // If object key corresponds to a dropdown menu and values haven't been set
            let input = document.querySelector('#view' + key);
            if ("FinalSolutionID" == key && result[key] == "") {
                // Set to defaults
                input.value = 0;
            } else {
                // Set value to result
                input.value = result[key];
            }
        });
    });
};


/**
 * Show ticket modal when ticket card is clicked
 * 
 * @param {Number} ticketID  Unique ID of ticket
 */
function makeTicket() {

    // Show view ticket modal
    var makeTicketModal = document.getElementById("make-ticket-modal");
    makeTicketModal.style.display = "block";

    // // Ajax request to get ticket details from database
    // $.post('/user/make', result = {
    //     ID: $("#" + ticketID).data("id")

    // }, obj => {

    //     alert(result["ID"]);
    //     // let result = JSON.parse(obj);
    //     Object.keys(result.result).forEach(key => {
    //         // If object key corresponds to a dropdown menu and values haven't been set
    //         let input = document.querySelector('#view' + key);
    //         if (["SoftwareID", "HardwareID", "SolutionID", "OperatorID", "AssignedSpecialistID", "FinalSolutionID"].includes(key) && !result.result[key]) {
    //             // Set to defaults
    //             input.value = 0;
    //             // If object key corresponds to a personnel dropdown
    //         } else if (["ReporterNo", "OperatorNo", "SpecialistNo"].includes(key)) {
    //             // Set text to result
    //             input.textContent = result.result[key];
    //         } else {
    //             // Set value to result
    //             input.value = result.result[key];
    //         }
    //     });
    //     getTicketLogs(ticketID);
    // });
};




// When searchbar is focused and key is pressed
function ticketSearch() {
    console.log("wagwan");
    let search = document.querySelector('#ticket-search').value.toLowerCase();
    let select = document.querySelector('#search-dropdown');
    let selectedOption = select.options[select.selectedIndex].text;
    // Check if "only show assigned" is checked
    let assignedOnly = document.querySelector('#onlyShowAssigned').checked;
    // Get userid
    let userid = document.querySelector('#username').dataset.userid;

    // Get all tickets
    let tickets = document.querySelectorAll('.ticket');
    // If search is empty
    if (search == '') {
        // Redisplay all tickets
        tickets.forEach(ticket => {
            // If only show assigned is checked and user is not assigned to ticket
            if (assignedOnly && ![ticket.dataset.assignedspecialistid, ticket.dataset.operatorid].includes(userid)) {
                // Hide ticket
                ticket.style.display = 'none';
            } else {
                // Display ticket
                ticket.style.display = 'list-item';
            }
        });
        return false;
    }

    // Loop through tickets
    tickets.forEach(ticket => {
        // If ticket field does not contain search
        if (!ticket.dataset[selectedOption.toLowerCase()] ||
            assignedOnly && ![ticket.dataset.assignedspecialistid, ticket.dataset.operatorid].includes(userid) ||
            ticket.dataset[selectedOption.toLowerCase()].toString().toLowerCase().indexOf(search) == -1) {
            // Hide ticket
            ticket.style.display = 'none';
        } else {
            if (assignedOnly && [ticket.dataset.assignedspecialistid, ticket.dataset.operatorid].includes(userid))
            // Display ticket
                ticket.style.display = 'list-item';
        }
    });
}

/**
 * Sets target data when user starts dragging ticket card
 * 
 * @param {Event} event Event for when user starts dragging ticket card
 */
function onDragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.id);
}

/**
 * Prevent browser default actions for when a HTML element is dragged
 * 
 * @param {Event} event Event for when user drags ticket card 
 */
function onDragOver(event) {
    event.preventDefault();
    // Convert event target to correct dropzone
    let dropzone = event.target;
    // If dropzone is table cell
    if (dropzone.tagName == "TD") {
        // Convert dropzone to first child of table cell
        dropzone = dropzone.firstElementChild;
    }
    // While dropzone is not an unordered list (the destination column)
    while (dropzone.tagName != "UL") {
        // Convert dropzone to its parent node
        dropzone = dropzone.parentNode;
    }
}

/**
 * Validate whether ticket move is valid and move ticket to new column, updating ticket state
 * in the database
 * 
 * @param {Event} event Event for when the user drops a ticket card 
 */
function onDrop(event) {
    // If Firefox 1.0+
    if (typeof window["InstallTrigger"] !== 'undefined') {
        alert("Error: Your browser does not support drag and drop.");
        return false;
    }
    if (event.preventDefault) { event.preventDefault(); }
    if (event.stopPropagation) { event.stopPropagation(); }
    // Get ticket ID
    const id = event.dataTransfer.getData('text');
    const draggableElement = document.getElementById(id);

    // Convert event target to correct dropzone
    let dropzone = event.target;
    // If dropzone is table cell
    if (dropzone.tagName == "TD") {
        // Convert dropzone to first child of table cell
        dropzone = dropzone.firstElementChild;
    }
    // While dropzone is not an unordered list (the destination column)
    while (dropzone.tagName != "UL") {
        // Convert dropzone to its parent node
        dropzone = dropzone.parentNode;
    }

    console.log(dropzone);

    // Clear event data
    event.dataTransfer.clearData();

    // Ajax request for getting ticket details
    let ticketID = id.replace('ticket', '');
    $.post('/specialist/getDetails', result = {
        ID: $("#" + ticketID).data("id"),
        NewState: dropzone.dataset.typeid,
        State: $("#" + ticketID).data("state"),
        NewMainTag: dropzone.dataset.typeid,
        MainTag: $("#" + ticketID).data("maintag"),
        SecondaryTag: $("#" + ticketID).data("secondarytag"),
        AssignedSpecialistID: $("#" + ticketID).data("assignedspecialistid"),
        SolutionID: $("#" + ticketID).data("solutionid"),
        UserID: $("#" + ticketID).data("reporterid")

    }, obj => {
        // Reload ticket table
        let ticketDetails = result;
        console.log(ticketDetails)
            // If ticket has not been moved
        if (dropzone.dataset.typeid == ticketDetails.NewMainTag && dropzone.dataset.state == ticketDetails.NewState) {
            console.log("oioi");
            return false;
        }
        console.log(dropzone.dataset.typeid)
            // Problem type has been changed
        if (dropzone.dataset.typeid != ticketDetails.MainTag) {
            $.post('/specialist/updateTicketType', result = {
                ID: $("#" + ticketID).data("id"),
                Maintag: dropzone.dataset.typeid

            }, obj => {
                console.log(result);
            });
        }
        // Ticket state has not been changed
        if (dropzone.dataset.state == ticketDetails.TicketState) {
            dropzone.appendChild(draggableElement);
        }
        // If ticket is not assigned and destination state is not "TODO"
        // else if (!(ticketDetails.AssignedSpecialistID) && dropzone.dataset.state != 'TODO') {
        //     // Display error message
        //     window.scrollTo({ top: 0, behavior: 'smooth' });
        //     alertBanner("ERROR: Ticket must have operator or specialist assigned to move to \"IN PROGRESS\", \"IN REVIEW\" or \"RESOLVED\" columns", "danger");
        //     return;
        //     // If ticket has no solution and destination state is "INREVIEW" or "RESOLVED"
        // } 
        else if (!ticketDetails["SolutionID"] && ["INREVIEW", "RESOLVED"].includes(dropzone.dataset.state)) {
            // Display error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
            alertBanner("ERROR: Ticket must have solution to move to \"IN REVIEW\" or \"RESOLVED\" columns", "danger");
            return;
        }
        // If destination column is "RESOLVED"
        if (dropzone.dataset.state == "RESOLVED") {
            // Ajax query to set resolved time on ticket
            $.post('/specialist/setTicketResolvedDate', result = {
                ID: $("#" + ticketID).data("id")
            }, obj => {
                console.log(1);
            });
            // Reload table
            dropzone.appendChild(draggableElement);
        } else {
            // Ajax query to remove resolved time on ticket
            $.post('/specialist/removeTicketResolvedDate', result = {
                ID: $("#" + ticketID).data("id")
            }, obj => {
                console.log(2);
            });
        }
        // Ajax query to to update ticket state
        $.post('/specialist/updateTicketState', result = {
            ID: $("#" + ticketID).data("id"),
            TicketState: dropzone.dataset.state

        }, obj => {
            //console.log(obj);
        });
        // Reload table
        dropzone.appendChild(draggableElement);
    });
}