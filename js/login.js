/**
 * Check if characters in string are valid and aren't used for script injection
 * 
 * @param {String} string to check characters of 
 * @returns true if string is valid, false otherwise
 */
 const validateCharacters = string => !string.match(/[|&;$%@"<>()+]/g);

// Checks the input elements for text content which could be used to ineject scripts to the system.
// Returns true if the values are valid.
// Returns false if the values contain a character from the illegal character set.
function validateLogin() {
    let username = document.forms["loginForm"]["username"].value;
    let password = document.forms["loginForm"]["password"].value;
    [username, password].forEach(input => {
        if (!validateCharacters(input)) {
            alertBanner('ERROR: Illegal character(s) entered in input field');
            return false;
        }
    })
    return true;
}

// Passes the input from the login form to a function checking the details.
// If the input is correct then the user will be logged in and moved to the ticket page.
// If the input is incorrect then an error banner will appear displaying the reason for login failure.
function checkLogin() {
    let usernameInput = document.querySelector('#usernameInput').value;
    let passwordInput = document.querySelector('#passwordInput').value;
    if(validateLogin()){
        ajax.post('./ajax-callable-functions.php', {
        functionname: 'checkLoginDetails',
        arguments: [usernameInput, passwordInput]
        }, obj => {
            // Returned object holds several attributes so we access the error message specifically.
            if (JSON.parse(obj).error) {
                let $errMessage = JSON.parse(obj).error;
                alertBanner($errMessage);
            } else {
                window.location.href = "ticket-list.php";
            }
        });
    }
    else {
        return;
    }
}

let inputs = document.querySelectorAll('input');
inputs.forEach(input => {
    input.addEventListener('keyup', event => {
        if (event.keyCode == 13) {
        checkLogin();
        }
    })
})