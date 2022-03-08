var AddModal = document.getElementById("add-modal");
var infoModal = document.getElementById("infoModal");


function addMod() {

    // When the user clicks on the button, open the modal
    AddModal.style.display = "block";
}


function closeModals() {
    AddModal.style.display = "none";
}






function rowClickModal(callID) {
    var phoneNo = document.getElementById("PhoneNo");
    var CallerDetailsID = document.getElementById("CallerDetailsID");
    var CallerDetails = document.getElementById("CallerDetails");
    var CallDetailsID = document.getElementById("CallDetailsID");


    // When the user clicks on the button, open the modal

    // Ajax request to add comment to ticket
    ajax.post('./ajax-callable-functions.php', { functionname: 'addCallInfo', arguments: [callID] }, obj => {
        let out = JSON.parse(obj);
        console.log(obj);
        let labels = ["Call Reason", "Problem", "Solution", "Ticket Priority", "Ticket State", "Reporter Details", "Operator Details", "Specialist Details"]
        let container = document.querySelector('#rowClickModalContent');

        for (var i = 0; i <= out.length - 1; i++) {

            let div = document.createElement('div');
            div.classList.add('full-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[0] + '</label><label class ="rowClickLabels" type="text" placeholder="" disabled>' + out[i][4] + ' </label>';
            container.appendChild(div);


            div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[1] + '</label><label class ="rowClickLabels" type="text" placeholder="" disabled>' + out[i][0] + ' </label>';
            container.appendChild(div);

            if (out[i][1] != null) {

                div = document.createElement('div');
                div.classList.add('half-row');
                div.innerHTML = '<label for="newCreateTypeText">' + labels[2] + '</label><label class ="rowClickLabels" type="text" placeholder="" disabled>' + out[i][1] + ' </label>';
                container.appendChild(div);
            } else {
                div = document.createElement('div');
                div.classList.add('half-row');
                div.innerHTML = '<label for="newCreateTypeText">' + labels[2] + '</label><label class ="rowClickLabels" type="text" placeholder="" disabled>No Solution Available </label>';
                container.appendChild(div);
            }

            div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[3] + '</label><input type="text" placeholder="" value="' + out[i][2] + '" disabled>';
            container.appendChild(div);


            div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[4] + '</label><input type="text" placeholder="" value="' + out[i][3] + '" disabled>';
            container.appendChild(div);


            div = document.createElement('div');
            div.classList.add('full-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[5] + '</label><input type="text" placeholder="" value="#' + out[i][5] + ' ' + out[i][8] + ' (' + out[i][11] + ') " disabled>';
            container.appendChild(div);


            div = document.createElement('div');
            div.classList.add('full-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[6] + '</label><input type="text" placeholder="" value="#' + out[i][7] + ' ' + out[i][10] + ' " disabled>';
            container.appendChild(div);

            if (!(out[i][6] == null || out[i][9] == null)) {

                div = document.createElement('div');
                div.classList.add('full-row');
                div.innerHTML = '<label for="newCreateTypeText">' + labels[7] + '</label><input type="text" placeholder="" value="#' + out[i][6] + ' ' + out[i][9] + ' " disabled>';
                container.appendChild(div);
            }

            if (i + 1 < out.length) {
                let hr = document.createElement('hr');
                hr.classList.add('full-row');
                hr.style.width = '90%';
                hr.style.margin = '2rem auto';
                container.appendChild(hr);
            }

            CallerDetailsID.textContent = "Caller ID: " + out[i][14];
            CallerDetails.textContent = "Caller Details: " + out[i][13];
            phoneNo.textContent = "Caller Phone Number: " + out[i][12];
            CallDetailsID.textContent = "#" + out[i][15] + " Call Information";

        }
    });

    infoModal.style.display = "block";

}


// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
    if (event.target.className == "modal fade" || event.target.className == "modal-form") {
        infoModal.style.display = "none";
        AddModal.style.display = "none";
        document.querySelector('#rowClickModalContent').innerHTML = "";
    }
}

// When user presses "ESC" on keyboard, close modal
window.onkeydown = (event) => {
    if (event.key == "Escape" && (infoModal.style.display == "block" || AddModal.style.display == "block")) {
        infoModal.style.display = "none";
        AddModal.style.display = "none";
        document.querySelector('#rowClickModalContent').innerHTML = "";
    }
};




function closeRowClickModal() {
    infoModal.style.display = "none";
    document.querySelector('#rowClickModalContent').innerHTML = "";
}