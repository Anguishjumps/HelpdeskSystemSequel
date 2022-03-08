var AddEquipmentModal = document.getElementById("add-equipment-modal");
var RemoveEquipmentModal = document.getElementById("delete-equipment-modal");
var InfoModal = document.getElementById("infoModal");

function addEquipment() {

  // When the user clicks on the button, open the modal
  AddEquipmentModal.style.display = "block";
}

function removeEquipment() {

  // When the user clicks on the button, open the modal
  RemoveEquipmentModal.style.display = "block";
}

function closeModals() {
  RemoveEquipmentModal.style.display = "none";
  AddEquipmentModal.style.display = "none";
  InfoModal.style.display = "none";
  document.querySelector('#rowClickModalContent').innerHTML = "";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
  if (event.target.className == "modal fade" || event.target.className == "modal-form") {
    AddEquipmentModal.style.display = "none";
    RemoveEquipmentModal.style.display = "none";
    InfoModal.style.display = "none";
    document.querySelector('#rowClickModalContent').innerHTML = "";
  }
}

// When the user presses esc, close it.
window.onkeydown = function (event) {
  if (event.key == "Escape" && (AddEquipmentModal.style.display == "block" || RemoveEquipmentModal.style.display == "block" || InfoModal.style.display == "block")) {
    AddEquipmentModal.style.display = "none";
    RemoveEquipmentModal.style.display = "none";
    InfoModal.style.display = "none";
    document.querySelector('#rowClickModalContent').innerHTML = "";
  }
};

// Searchbar
// The function is for the search bar, to output the correct table depending on the search input.
function equipmentSearch() {
  var search = document.getElementById('equipment-search').value.toLowerCase();
  let select = document.querySelector('#search-dropdown');
  let selectedOption = select.options[select.selectedIndex].text;
  // find search-dropdown selected.

  let tableElem = document.getElementsByClassName("Equip_ID");
  let tableElem1 = document.getElementsByClassName("Serial_Numb");
  let tableElem2 = document.getElementsByClassName("Equip_Type");
  let tableElem3 = document.getElementsByClassName("Equip_Make");
  let tableElem4 = document.getElementsByClassName("User_ID");
  let tableRow = document.getElementsByClassName("tableRow");


  // 1st case
  if (selectedOption == "ID") {

    for (let i = 0; i < tableElem.length; i++) {
      if (tableElem[i].innerHTML.toLowerCase() == search) {
        tableRow[i].style.display = "";
      }
      else if (search == "") {
        tableRow[i].style.display = "";
      }
      else {
        tableRow[i].style.display = "None";
      }
    }
    // 2nd case
  } else if (selectedOption == "Serial Number") {

    for (let i = 0; i < tableElem1.length; i++) {
      if (tableElem1[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      }
      else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }
    // 3rd case
  } else if (selectedOption == "Equipment Type") {


    for (let i = 0; i < tableElem2.length; i++) {
      if (tableElem2[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    }
    // 4th case
  } else if (selectedOption == "Make") {


    for (let i = 0; i < tableElem3.length; i++) {
      if (tableElem3[i].innerHTML.toLowerCase().includes(search)) {
        tableRow[i].style.display = "";
      } else if (search == "") {
        tableRow[i].style.display = "";
      } else {
        tableRow[i].style.display = "None";
      }
    } 
    // 5th case
  } else if (selectedOption == "User ID") {


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

function rowClickModal2(equipmentID) {
  var rowData = document.getElementById("infoModal");

  // Ajax request to get the function and output the correct information about the equipment.
  ajax.post('./ajax-callable-functions.php', { functionname: 'addEquipInfo', arguments: [equipmentID] }, obj => {
    let out = JSON.parse(obj);
    let labels = ["Problem", "Solution"];
    let container = document.querySelector('#rowClickModalContent');
    // out is an array of array each array contains a problem if there is one and it's solution if there's one.
    for (var i = 1; i <= out.length - 1; i++) {
      let row = out[i];
      if (row[0] == null) {
        let div = document.createElement('div');
            div.classList.add('full-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[0] + '</label><label class="equipLabel" for="equipInfos">' +  "No problem available" + '</label>';
            container.appendChild(div);
      } else {
        let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[0] + '</label> <label class="equipLabel" for="equipInfos">' +  row[0].trim() + '</label>';
            container.appendChild(div);

        if (row[1] == null) {
          let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[1] + '</label><label class="equipLabel" for="equipInfos">' +  "No solution available" + '</label>';
            container.appendChild(div);

        } else {
          let div = document.createElement('div');
            div.classList.add('half-row');
            div.innerHTML = '<label for="newCreateTypeText">' + labels[1] + '</label><label class="equipLabel" for="equipInfos">' +  row[1].trim() + '</label>';
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

function checkNewEquipmentForm() {
  // Check all inputs for illegal characters
  let form = document.forms["new-equipment-form"];
  let valid = true;
  ["serialNo", "device", "make"].forEach(input => {
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

function checkRemoveEquipmentForm() {
  // Check all inputs for illegal characters
  let form = document.forms["delete-equipment-form"];
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