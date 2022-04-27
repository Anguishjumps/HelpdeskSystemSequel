function getProblemName(id, problemTypes) {
    console.log("hello")
        // for (i in problemTypes) {
        //     if (id == problemTypes[i].ID) {

    //     }
    // }

}


// When searchbar is focused and key is pressed
function ticketSearch() {
    let search = document.querySelector('#ticket-search').value.toLowerCase();
    let select = document.querySelector('#search-dropdown');
    let selectedOption = select.options[select.selectedIndex].text;

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
    ajax.post('./ajax-callable-functions.php', { functionname: 'getTicketDetails', arguments: [ticketID] }, obj => {
        // Reload ticket table
        let ticketDetails = JSON.parse(obj).result;
        // If ticket has not been moved
        if (dropzone.dataset.typeid == ticketDetails.TypeID && dropzone.dataset.state == ticketDetails.TicketState) {
            return false;
        }
        // Problem type has been changed
        if (dropzone.dataset.typeid != ticketDetails.TypeID) {
            ajax.post('./ajax-callable-functions.php', { functionname: 'updateTicketType', arguments: [ticketID, dropzone.dataset.typeid] }, obj => {
                console.log(obj);
            });
        }
        // Ticket state has not been changed
        if (dropzone.dataset.state == ticketDetails.TicketState) {
            dropzone.appendChild(draggableElement);
        }
        // If ticket is not assigned and destination state is not "TODO"
        else if (!(ticketDetails.OperatorID || ticketDetails.AssignedSpecialistID) && dropzone.dataset.state != 'TODO') {
            // Display error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
            alertBanner("ERROR: Ticket must have operator or specialist assigned to move to \"IN PROGRESS\", \"IN REVIEW\" or \"RESOLVED\" columns", "danger");
            return;
            // If ticket has no solution and destination state is "INREVIEW" or "RESOLVED"
        } else if (!ticketDetails["FinalSolutionID"] && ["INREVIEW", "RESOLVED"].includes(dropzone.dataset.state)) {
            // Display error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
            alertBanner("ERROR: Ticket must have solution to move to \"IN REVIEW\" or \"RESOLVED\" columns", "danger");
            return;
        }
        // If destination column is "RESOLVED"
        if (dropzone.dataset.state == "RESOLVED") {
            // Ajax query to set resolved time on ticket
            ajax.post('./ajax-callable-functions.php', { functionname: 'setTicketResolvedDate', arguments: [ticketID] }, obj => {
                console.log(obj);
            });
            // Reload table
            dropzone.appendChild(draggableElement);
        } else {
            // Ajax query to remove resolved time on ticket
            ajax.post('./ajax-callable-functions.php', { functionname: 'removeTicketResolvedDate', arguments: [ticketID] }, obj => {
                console.log(obj);
            });
        }
        // Ajax query to to update ticket state
        ajax.post('./ajax-callable-functions.php', { functionname: 'updateTicketState', arguments: [ticketID, dropzone.dataset.state] }, obj => {
            console.log(obj);
        });
        // Reload table
        dropzone.appendChild(draggableElement);
    });
}