/**
 * Show ticket modal when ticket card is clicked
 * 
 * @param {Number} ticketID  Unique ID of ticket
 */
function makeTicket() {

    // Show view ticket modal
    var makeTicketModal = document.getElementById("make-ticket-modal");
    makeTicketModal.style.display = "block";

    // Ajax request to get ticket details from database
    $.post('/user/make', result = {
        ID: $("#" + ticketID).data("id")

    }, obj => {

        alert(result["ID"]);
        // let result = JSON.parse(obj);
        Object.keys(result.result).forEach(key => {
            // If object key corresponds to a dropdown menu and values haven't been set
            let input = document.querySelector('#view' + key);
            if (["SoftwareID", "HardwareID", "SolutionID", "OperatorID", "AssignedSpecialistID", "FinalSolutionID"].includes(key) && !result.result[key]) {
                // Set to defaults
                input.value = 0;
                // If object key corresponds to a personnel dropdown
            } else if (["ReporterNo", "OperatorNo", "SpecialistNo"].includes(key)) {
                // Set text to result
                input.textContent = result.result[key];
            } else {
                // Set value to result
                input.value = result.result[key];
            }
        });
        getTicketLogs(ticketID);
    });
};