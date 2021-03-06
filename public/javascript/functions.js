function getProblemName(id, problemTypes) {
    console.log("hello")
        // for (i in problemTypes) {
        //     if (id == problemTypes[i].ID) {

    //     }
    // }

}

// function needed to update tickets
function fillNewSol(text) {
    var newSol = document.getElementById("viewSolutionDescription")
    newSol.value = text;
}

// Displays an error banner with a given message.
function alertBanner(message) {
    // If a banner already exists, remove its content.
    if (document.getElementById("individualAlertBanner")) {
        // Stop the timeout for the existing banner.
        clearTimeout(bannerTimeout);
        document.getElementById("individualAlertBanner").remove();
    }
    // Create content for the new banner.
    var wrapper = document.createElement('div');
    wrapper.innerHTML = '<div id="individualAlertBanner" class="alert-banner"><div>' + message + '</div><button onclick="closeAlert(this)" ' +
        'type="button" class="alert-close button-close" data-bs-dismiss="alert" aria-label="Close">' +
        '<img id="closeBannerButton" src="/close.png"></button></div>';

    $(document).ready(function() {
        // Find the container for the banner and add the new content.
        let alertContainer = document.querySelector('.alertContainer');
        alertContainer.append(wrapper);
        // Start a timeout for the banner to dissapear after 3 seconds.
        bannerTimeout = setTimeout(function() {
            alertContainer.innerHTML = "";
        }, 3000);
    })
}


// Close alert
function closeAlert(event) {
    event.parentNode.remove();
}



// Close ticket modals when close button is clicked
function closeModal() {

    var viewTicketModal = document.getElementById("view-ticket-modal");
    viewTicketModal.style.display = "none";
}


/**
 * Show ticket modal when ticket card is clicked
 * 
 * @param {Number} ticketID  Unique ID of ticket
 */
function showTicket(ticketID) {

    // Show view ticket modal
    var viewTicketModal = document.getElementById("view-ticket-modal");
    viewTicketModal.style.display = "block";

    // Post request to get ticket details from html data tags
    $.post('/specialist/show', ticketDetails = {
        //use individual ticket ID passed through in function to identify ticket and read its data tags
        ID: $("#" + ticketID).data("id"),
        CreatedTimestamp: $("#" + ticketID).data("createdtimestamp"),
        UserID: $("#" + ticketID).data("reporterid"),
        AssignedSpecialistID: $("#" + ticketID).data("assignedspecialistid"),
        TicketPriority: $("#" + ticketID).data("priority"),
        TicketState: $("#" + ticketID).data("state"),
        TypeID: $("#" + ticketID).data("maintag"),
        TicketDescription: $("#" + ticketID).data("description"),
        FinalSolutionID: $("#" + ticketID).data("solutionid"),
        SolutionDescription: $("#" + ticketID).data("resolveddescription"),
        ResolveDate: $("#" + ticketID).data("resolvedate"),


    }, obj => {
        //set the values of the view ticket modal to those of the ticket data tags
        Object.keys(ticketDetails).forEach(key => {
            console.log(key)
            let input = document.querySelector('#view' + key);
            // If timestamp then take substring to have only day, month and year
            if ("CreatedTimestamp" == key || "ResolveDate" == key) {
                //substring for timestamp entries
                input.value = ticketDetails[key].substr(0, 15);
            } else {
                // Set value to ticketDetails
                input.value = ticketDetails[key];
            }
        });
    });
};



// When search button is pressed or key is pressed
function ticketSearch() {
    //get input of searchbar
    let search = document.querySelector('#searchbar').value.toLowerCase();

    // Get all tickets
    let tickets = document.querySelectorAll('.ticket');
    // If search is empty
    if (search == '') {
        // Redisplay all tickets
        tickets.forEach(ticket => {

            // Display ticket
            ticket.style.display = 'list-item';

        });
        return false;
    }
    //check if search input is numeric or not
    var hasNumber = /\d/;
    //if search is numeric, look for ticket ids
    if (hasNumber.test(search)) {
        // Loop through tickets
        tickets.forEach(ticket => {
            // If ticket field does not contain search
            if (ticket.dataset.id != search) {
                // Hide ticket
                ticket.style.display = 'none';
            } else {

                // Display ticket
                ticket.style.display = 'list-item';
            }
        });
        //if search is a string search for problem/solution description
    } else {
        // Loop through tickets
        tickets.forEach(ticket => {
            // If ticket field does not contain search
            if (ticket.dataset.description.includes(search) || ticket.dataset.resolveddescription.includes(search)) {
                // Display ticket
                ticket.style.display = 'list-item';
            } else {
                // Hide ticket
                ticket.style.display = 'none';
            }
        });

    }
}


// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
    var viewTicketModal = document.getElementById("view-ticket-modal");
    if (event.target.className == "modal fade" || event.target.className == "modal-form") {
        viewTicketModal.style.display = "none";
    }
}

// When user presses "ESC" on keyboard, close modal
window.onkeydown = (event) => {
    var viewTicketModal = document.getElementById("view-ticket-modal");
    if (event.key == "Escape" && (viewTicketModal.style.display == "block")) {
        viewTicketModal.style.display = "none";
    }
};


function addNewSolution(event) {
    let newCreateTypeCol = document.querySelector('.newSolutionCol');
    // If value is changed to -1
    if (event.value == -1) {
        // Show new solution column
        newCreateTypeCol.style.display = 'block';
    } else {
        // Hide new solution column
        newCreateTypeCol.style.display = 'none';
    }
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
function onDrop(event, sessionID) {
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
    $.post('/specialist/getDetails', ticketData = {
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
        let ticketDetails = ticketData;
        console.log(ticketDetails)
            // If ticket has not been moved
        if (dropzone.dataset.typeid == ticketDetails.NewMainTag && dropzone.dataset.state == ticketDetails.NewState) {
            console.log("oioi");
            return false;
        }
        console.log(dropzone.dataset.typeid)
            // Problem type has been changed
        if (dropzone.dataset.typeid != ticketDetails.MainTag) {
            $.post('/specialist/updateTicketType', ticketData = {
                ID: $("#" + ticketID).data("id"),
                Maintag: dropzone.dataset.typeid

            }, obj => {
                $("#" + ticketID).data("maintag", dropzone.dataset.typeid)
            });
        }
        // Ticket state has not been changed
        if (dropzone.dataset.state == ticketDetails.TicketState) {
            dropzone.appendChild(draggableElement);
        } else if (ticketDetails["SolutionID"] == "NULL" || ticketDetails["SolutionID"] == 0 && ["INREVIEW", "RESOLVED"].includes(dropzone.dataset.state)) {
            // Display error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
            alertBanner("ERROR: Ticket must have solution to move to \"IN REVIEW\" or \"RESOLVED\" columns", "danger");
            return;
        }

        // If ticket is moved from "TODO", assign ticket to current specialist
        if (dropzone.dataset.state != "TODO") {
            $.post('/specialist/setSpecialist', ticketData = {
                ID: $("#" + ticketID).data("id")
            }, obj => {
                console.log("setting specialist");
                $("#" + ticketID).data("assignedspecialistid", sessionID)
            });
            // Reload table
            dropzone.appendChild(draggableElement);
        } else {
            $.post('/specialist/unsetSpecialist', ticketData = {
                ID: $("#" + ticketID).data("id")
            }, obj => {
                console.log("removing specialist");
                $("#" + ticketID).data("assignedspecialistid", "")
            });
        }

        // If destination column is "RESOLVED"
        if (dropzone.dataset.state == "RESOLVED") {
            // Ajax query to set resolved time on ticket
            $.post('/specialist/setTicketResolvedDate', ticketData = {
                ID: $("#" + ticketID).data("id"),
            }, obj => {
                $("#" + ticketID).data("resolvedate", new Date())
            });
            // Reload table
            dropzone.appendChild(draggableElement);
        } else {
            // Ajax query to remove resolved time on ticket
            $.post('/specialist/removeTicketResolvedDate', ticketData = {
                ID: $("#" + ticketID).data("id")
            }, obj => {
                $("#" + ticketID).data("resolvedTimestamp", "")
            });
        }
        // Ajax query to to update ticket state
        $.post('/specialist/updateTicketState', ticketData = {
            ID: $("#" + ticketID).data("id"),
            TicketState: dropzone.dataset.state

        }, obj => {
            $("#" + ticketID).data("state", dropzone.dataset.state)
                //console.log(obj);
        });
        // Reload table
        dropzone.appendChild(draggableElement);
    });
}