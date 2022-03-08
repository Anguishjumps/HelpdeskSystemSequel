var AddSoftwareModal = document.getElementById("add-software-modal");
var RemoveSoftwareModal = document.getElementById("delete-software-modal");
var InfoModal = document.getElementById("infoModal");

function addSoftware() {

  // When the user clicks on the button, open the modal
  AddSoftwareModal.style.display = "block";
}

function removeSoftware() {

  // When the user clicks on the button, open the modal
  RemoveSoftwareModal.style.display = "block";
}

function closeModals() {
  RemoveSoftwareModal.style.display = "none";
  AddSoftwareModal.style.display = "none";
  InfoModal.style.display = "none";
  document.querySelector('#rowClickModalContent').innerHTML = "";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
  if (event.target.className == "modal fade" || event.target.className == "modal-form") {
    AddSoftwareModal.style.display = "none";
    RemoveSoftwareModal.style.display = "none";
    InfoModal.style.display = "none";
    document.querySelector('#rowClickModalContent').innerHTML = "";
  }
}

// When the user presses esc, close it.
window.onkeydown = function (event) {
  if (event.key == "Escape" && (AddSoftwareModal.style.display == "block" || RemoveSoftwareModal.style.display == "block" || InfoModal.style.display == "block")) {
    AddSoftwareModal.style.display = "none";
    RemoveSoftwareModal.style.display = "none";
    InfoModal.style.display = "none";
    document.querySelector('#rowClickModalContent').innerHTML = "";
  }
};

// Searchbar
// The function is for the search bar, to output the correct table depending on the search input.
function softwareSearch() {
  var search = document.getElementById('software-search').value.toLowerCase();
  let select = document.querySelector('#search-dropdown');
  let selectedOption = select.options[select.selectedIndex].text;
  // find search-dropdown selected.

  let tableElem = document.getElementsByClassName("Soft_ID");
  let tableElem1 = document.getElementsByClassName("Soft_Name");
  let tableElem2 = document.getElementsByClassName("Soft_Version");
  let tableElem3 = document.getElementsByClassName("License_Number");
  let tableElem4 = document.getElementsByClassName("User_ID");
  let tableRow = document.getElementsByClassName("tableRow");


  if (selectedOption == "Software ID") {
    // 1st case
    for (let i = 0; i < tableElem.length; i++) {
      if (tableElem[i].innerHTML.toLowerCase() == search) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      }
      else {
        tableRow[i].style.display = "None";
      }
    }

  } else if (selectedOption == "Software Name") {
    // 2nd case
    for (let i = 0; i < tableElem1.length; i++) {
      if (tableElem1[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }

  } else if (selectedOption == "Software Version") {
    // 3rd case
    for (let i = 0; i < tableElem2.length; i++) {
      if (tableElem2[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }

  } else if (selectedOption == "License Number") {
    // 4th case
    for (let i = 0; i < tableElem3.length; i++) {
      if (tableElem3[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }

  } else if (selectedOption == "User ID") {
    // 5th case
    for (let i = 0; i < tableElem4.length; i++) {
      if (tableElem4[i].innerHTML.toLowerCase() == search) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }
  }
}

function rowClickModal(softwareID) {
  var rowData = document.getElementById("infoModal");
  // When the user clicks on the row, open the modal


  // Ajax request to get the function and output the correct information about the software.

  ajax.post('./ajax-callable-functions.php', { functionname: 'addSoftInfo', arguments: [softwareID] }, obj => {
    let out = JSON.parse(obj);
    let labels = ["Problem", "Solution"];
    let container = document.querySelector('#rowClickModalContent');

    for (var i = 0; i <= out.length - 1; i++) {
      let row = out[i];
      if (row[0] == null) {
        let div = document.createElement('div');
            div.classList.add('full-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[0] + '</label><label class="softLabel" for="softInfos">' +  "No problem available" + '</label>';
            container.appendChild(div);
      } else {
        let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[0] + '</label> <label class="softLabel" for="softInfos">' +  row[0].trim() + '</label>';
            container.appendChild(div);

        if (row[1] == null) {
          let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[1] + '</label><label class="softLabel" for="softInfos">' +  "No solution available" + '</label>';
            container.appendChild(div);

        } else {
          let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[1] + '</label><label class="softLabel" for="softInfos">' +  row[1].trim() + '</label>';
            container.appendChild(div);

        }
      }
      if (i + 1 < out.length) {
        let hr = document.createElement('hr');
        hr.classList.add('full-row');
        hr.style.width = '90%';
        hr.style.margin = '2rem auto';
        hr.style.height = 'fit-content';
        container.appendChild(hr);
    }
    }
  });




  InfoModal.style.display = "block";
}

/**
 * Check if characters in string are valid and aren't used for script injection
 * 
 * @param {String} string to check characters of 
 * @returns true if string is valid, false otherwise
 */
const validateCharacters = string => !string.match(/[|&;$%@"<>()+]/g);

function checkNewSoftwareForm() {
  // Check all inputs for illegal characters
  let form = document.forms["new-software-form"];
  let valid = true;
  ["softwareName", "softwareVersion", "licenseNumber"].forEach(input => {
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

function checkRemoveSoftwareForm() {
  // Check all inputs for illegal characters
  let form = document.forms["delete-sotware-form"];
  let valid = true;
  ["removeID"].forEach(input => {
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
