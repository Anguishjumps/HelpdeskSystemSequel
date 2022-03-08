<?php

require_once './classes.php';

// Configuration variables for database connection
const SERVERNAME = "localhost";
const USERNAME = "team008";
const PASSWORD = "dbnkKF2ykC";
const DBNAME = "team008";

// Begin php session (if not already started)
session_start();

header('Content-Type: application/json');

// Array to store result
$aResult = array();

// If functionname has not been declared in request
if (!isset($_POST['functionname'])) {
    // Show error
    $aResult['error'] = 'No function name!';
}
// If arguments has not been declared in request
$_POST['arguments'] = explode(',', $_POST['arguments']);
if (!isset($_POST['arguments'])) {
    // Show error
    $aResult['error'] = 'No function arguments!';
}

// If there is no error in request
if (!isset($aResult['error'])) {
    switch ($_POST['functionname']) {
        case 'updateTicketState':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 2)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = updateTicketState($_POST['arguments'][0], $_POST['arguments'][1]);
            }
            break;

        case 'updateTicketType':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 2)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = updateTicketType($_POST['arguments'][0], $_POST['arguments'][1]);
            }
            break;

        case 'setTicketResolvedDate':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = setTicketResolvedDate($_POST['arguments'][0]);
            }
            break;

        case 'getTicketDetails':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = getTicketDetails($_POST['arguments'][0]);
            }
            break;

        case 'getTicketLogs':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = getTicketLogs($_POST['arguments'][0]);
            }
            break;

        case 'logOut':
            // If user is logged in
            if (isset($_SESSION['username'])) {
                // Unset user variables
                unset($_SESSION['userid']);
                unset($_SESSION['username']);
                unset($_SESSION['dept']);
                unset($_SESSION['deptName']);
                unset($_SESSION['showOnlyAssigned']);
                // Show success
                $aResult['result'] = 'Logged out successfully';
            } else {
                // Show error
                $aResult['error'] = 'Username not set';
            }
            break;

        case 'checkLoginDetails':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 2)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = checkLoginDetails($_POST['arguments'][0], $_POST['arguments'][1]);
            }
            break;

        case 'getPhoneNo':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = getPhoneNo($_POST['arguments'][0]);
            }
            break;

        case 'toggleShowAssigned':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $_SESSION['showOnlyAssigned'] = $_POST['arguments'][0];
            }
            break;

        case 'addComment':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 2)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = addComment($_POST['arguments'][0], $_POST['arguments'][1]);
            }
            break;

        case 'addCallInfo':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = addCallInfo($_POST['arguments'][0]);
            }
            break;


        case 'addSoftInfo':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                $aResult = addSoftInfo($_POST['arguments'][0]);
            }
            break;

        case 'addArchiveTicketInfo':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                // Call function with arguments
                $aResult = addArchiveTicketInfo($_POST['arguments'][0]);
            }
            break;

        case 'addEquipInfo':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                $aResult = addEquipInfo($_POST['arguments'][0]);
            }
            break;

        case 'removeTicketResolvedDate':
            // If number of arguments is incorrect
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 1)) {
                // Show error
                $aResult['error'] = 'Error in arguments!';
            } else {
                $aResult = removeTicketResolvedDate($_POST['arguments'][0]);
            }
            break;
    }
}

echo json_encode($aResult);

/**
 * Updates the state field of the ticket specified in the database
 * 
 * @param Number    $ID unique identifier of ticket
 * @param State     $state of the ticket to be updated to
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function updateTicketState($ID, $state)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update ticket state in database
    $sql = "UPDATE Ticket
    SET TicketState = '$state'
    WHERE ID = $ID";

    // Array to store result 
    $functionResult = array();

    // If query was successful
    if ($conn->query($sql) === TRUE) {
        // Insert record to TicketLog table in database
        $sql = "INSERT INTO TicketLog 
            (TicketID, LogType, Text, OriginPersonnelID )
            VALUES 
            ('$ID', 'UpdateState', '$state', '" . $_SESSION['userid'] . "');";

        // If query is unsuccessful
        if ($conn->query($sql) !== TRUE) {
            // Display error
            $functionResult['error'] = $conn->error;
        }
    } else {
        // Display error
        $functionResult['error'] = $conn->error;
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Updates the type field of the ticket specified in the database
 * 
 * @param Number    $ID unique identifier of ticket
 * @param Number    $typeID unique ID of the problem type to be updated to
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function updateTicketType($ID, $typeID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update ticket state in database
    $sql = "UPDATE Ticket
    SET TypeID = $typeID
    WHERE ID = $ID";

    // Array to store result 
    $functionResult = array();

    // If query was successful
    if ($conn->query($sql) === TRUE) {
        // Insert record to TicketLog table in database
        $sql = "INSERT INTO TicketLog 
            (TicketID, LogType, Text, OriginPersonnelID )
            VALUES
            ('$ID', 'UpdateProblemType', $typeID, '" . $_SESSION['userid'] . "');";

        // If query is unsuccessful
        if ($conn->query($sql) !== TRUE) {
            // Display error
            $functionResult['error'] = $conn->error;
        }
    } else {
        // Display error
        $functionResult['error'] = $conn->error;
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Set the resolved date of the specified ticket in the database
 * 
 * @param Number    $ID unique identifier of ticket
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function setTicketResolvedDate($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result 
    $functionResult = array();

    // Update ticket resolved data with current timestamp
    $sql = "UPDATE Ticket
    SET ResolvedTimestamp = CURRENT_TIMESTAMP
    WHERE ID = $ID";

    // If query was unsuccessful
    if ($conn->query($sql) !== TRUE) {
        $functionResult['error'] = $conn->error;
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Get the details of the specified ticket in the database
 * 
 * @param Number    $ID unique identifier of ticket
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function getTicketDetails($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select ticket details from database
    $sql = "SELECT *, (SELECT PhoneNo FROM Personnel WHERE ID = ReporterID) AS ReporterNo
    , (SELECT PhoneNo FROM Personnel WHERE ID = OperatorID) AS OperatorNo
    , (SELECT PhoneNo FROM Personnel WHERE ID = AssignedSpecialistID) AS SpecialistNo
    FROM Ticket
    WHERE ID = $ID";
    $result = $conn->query($sql);

    // Array to store result 
    $functionResult = array();

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        $functionResult['result'] = $result->fetch_assoc();
    } else {
        // Return error
        $functionResult['error'] = "No ticket details results found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Get all the logs of the specified ticket in the database
 * 
 * @param Number    $ID unique identifier of ticket
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function getTicketLogs($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get Logs from ticket
    $sql = "SELECT *, 
    (SELECT FullName FROM Personnel WHERE ID = OriginPersonnelID) AS OriginPersonnel, 
    (SELECT FullName FROM Personnel WHERE ID = AssignedPersonnelID) AS AssignedPersonnel, 
    CASE 
    WHEN LogType = 'UpdateProblemType' THEN (SELECT Problem FROM ProblemType WHERE ID = Text) 
    WHEN LogType = 'UpdateSoftware' THEN (SELECT SoftwareName FROM Software WHERE ID = Text) 
    WHEN LogType = 'UpdateHardware' THEN (SELECT CONCAT(Make, ' ', Device) FROM Hardware WHERE ID = Text) 
    WHEN LogType = 'AddSolution' THEN (SELECT Explanation FROM Solution WHERE ID = Text) 
    ELSE Text
    END AS Text 
    FROM TicketLog 
    WHERE TicketID = $ID
    ORDER BY LogTimestamp DESC";
    $result = $conn->query($sql);

    // Array to store result 
    $functionResult = array();
    $ticketLogs = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get ticketLog details from each record in results
        while ($row = $result->fetch_assoc()) {
            $ticketLog = new TicketLog();
            // Set ticketLog attributes
            $ticketLog->ID = $row["ID"];
            $ticketLog->TicketID = $row["TicketID"];
            $ticketLog->LogTimestamp = $row["LogTimestamp"];
            $ticketLog->Text = $row["Text"];
            $ticketLog->LogType = $row["LogType"];
            $ticketLog->OriginPersonnelID = $row["OriginPersonnelID"];
            $ticketLog->OriginPersonnel = $row["OriginPersonnel"];
            $ticketLog->AssignedPersonnelID = $row["AssignedPersonnelID"];
            $ticketLog->AssignedPersonnel = $row["AssignedPersonnel"];
            // Add ticketLog to array
            array_push($ticketLogs, $ticketLog);
        }
        // Return results
        $functionResult['result'] = $ticketLogs;
    } else {
        // Return error
        $functionResult['error'] = "No ticket log results found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Check the login details given by the user
 * 
 * @param String    $loginUsername username given by the user
 * @param String    $loginPassword password given by the user
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function checkLoginDetails($loginUsername, $loginPassword)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set the login attempt if not set
    if(!isset($_SESSION['attempt'])){
        $_SESSION['attempt'] = 0;
    }

    // Check if the timeout is up for the user so they can attempt login again
    if(isset($_SESSION['attempt-again'])){
        $now = time();
        if ($now >= $_SESSION['attempt-again']){
          $_SESSION['attempt'] = 0;
          unset($_SESSION['attempt-again']);
        }
      }

    // Check if there are 3 attempts already
    if($_SESSION['attempt'] == 3){
        $timeLeft = $_SESSION['attempt-again'] - time();
        $format =  "Please try again in %d seconds";
        $_SESSION['error'] = sprintf($format, $timeLeft);
        return $_SESSION;
    }

    // Select user from database
    $sql = "SELECT ID, Username, PasswordHash, Dept, (SELECT DeptName FROM Departments WHERE ID = Dept) AS DeptName,     
            CASE
                WHEN Dept = 6 AND (SELECT External FROM SpecialistActiveExternal WHERE SpecialistID = ID) = 1 AND (SELECT COUNT(ID) FROM Ticket WHERE AssignedSpecialistID = Personnel.ID AND TicketState != 'RESOLVED') < 1 THEN 0
                ELSE 1
            END AS AllowedAccess     
            FROM Personnel
            WHERE Username = '$loginUsername'";
    $result = $conn->query($sql);

    // Array to store result
    $functionResult = array();

    // If query returns results
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // If password is correct
        if (password_verify($loginPassword, $row['PasswordHash'])) {
            // If External Specialist is not currently active.
            if($row['AllowedAccess'] == 0){
                $functionResult['error'] = "User access outdated";
                return $functionResult;
            }
            unset($_SESSION['attempt']);
            // If user is operator or specialist
            if (in_array($row['DeptName'], ["Operator", "Specialist"])) {
                // Set user session variables
                $_SESSION['userid'] = $row['ID'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['dept'] = $row['Dept'];
                $_SESSION['deptName'] = $row['DeptName'];
            } else {
                // Return error (Wrong department)
                $functionResult['error'] = "User must be Operator or Specialist";
            }
        } else {
            // Return error (Wrong password (correct username))
            $functionResult['error'] = "Incorrect username or password";
            // Add attempt 
            $_SESSION['attempt'] += 1;
            // Set the timeout if the number of incorrect attempts is 3
            if($_SESSION['attempt'] == 3){
                // User cannot login for 60 seconds
                $_SESSION['attempt-again'] = time() + 60;
            }
        }
    } else {
        // Return error (Wrong username)
        $functionResult['error'] = "No users found with this username";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Get phone number of specified personnel
 * 
 * @param Number    $personnelID Unique ID of personnel to search for phone number of
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function getPhoneNo($personnelID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array();

    // Select phone number from database with specified personnelID
    $sql = "SELECT PhoneNo
    FROM Personnel 
    WHERE ID = '$personnelID'";
    $result = $conn->query($sql);

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        $functionResult['result'] = $result->fetch_assoc();
    } else {
        // Return error
        $functionResult['error'] = "No phone number results found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Add comment to ticket
 * 
 * @param Number    $ID Unique ID ticket to add comment to
 * @param String    $text comment to add to ticket
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function addComment($ID, $text)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array();

    $text = $conn->real_escape_string($text);

    // Insert row into TicketLog table in db
    $sql = "INSERT INTO TicketLog 
    (TicketID, LogType, OriginPersonnelID, Text) 
    VALUES 
    ('$ID', 'Comment', '" . $_SESSION['userid'] . "', '$text');";

    // If query was unsuccessful
    if ($conn->query($sql) !== TRUE) {
        // Return error
        $functionResult['error'] = "Ticket comment could not be added";
    } else {
        // Get newly inserted row from db
        $sql = "SELECT *, (SELECT FullName FROM Personnel WHERE ID = OriginPersonnelID) AS OriginPersonnel FROM TicketLog WHERE ID = $conn->insert_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // Return error
            $functionResult['result'] = $result->fetch_assoc();
        } else {
            $functionResult['error'] = "New ticket comment could not be found";
        }
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

function removeTicketResolvedDate($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result 
    $functionResult = array();

    $ticketDetails = getTicketDetails($ID);
    if (!array($ticketDetails)[0]['result']['ResolvedTimestamp']) return $functionResult;

    // Update ticket resolved data with current timestamp
    $sql = "UPDATE Ticket
    SET ResolvedTimestamp = NULL
    WHERE ID = $ID";

    // If query was unsuccessful
    if ($conn->query($sql) !== TRUE) {
        $functionResult['error'] = $conn->error;
    }

    // Close connection to database
    $conn->close();
    // Return functionResult
    return $functionResult;
}

/**
 * Add call details to callpage modal
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function addCallInfo($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array();

    // find ticket description and solution in db
    $sql = "SELECT  
    Ticket.TicketDescription,
    Solution.Explanation,  
    Ticket.TicketPriority ,
    Ticket.TicketState,
    CallTicket.Reason,
    Ticket.ReporterID, 
    Ticket.AssignedSpecialistID, 
    Ticket.OperatorID,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.ReporterID) AS Reporter,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.AssignedSpecialistID) AS AssignedSpecialist,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.OperatorID) AS Operator,
    (SELECT Job FROM Personnel WHERE Personnel.ID = Ticket.ReporterID) AS Job,
    (SELECT PhoneNo FROM Personnel WHERE Personnel.ID = PhoneCall.CallerID) as PhoneNo,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = PhoneCall.CallerID) as CallerName,
    PhoneCall.CallerID,
    PhoneCall.ID
    From PhoneCall left Join CallTicket
    on PhoneCall.ID=CallTicket.CallID
    LEFT JOIN Ticket 
    on CallTicket.TicketID = Ticket.ID Left JOIN Solution 
    ON Ticket.FinalSolutionID = Solution.ID
    WHERE PhoneCall.ID=$ID;";

    $result = $conn->query($sql);

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        while ($row = mysqli_fetch_array($result)) {
            $functionResult[] = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]
            , $row[8], $row[9], $row[10], $row[11], $row[12],$row[13],$row[14],$row[15]);
        }
    } else {
        // Return error
        $functionResult['error'] = "No software details found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult

    return $functionResult;
}



/**
 * Add Archive details to Archive page modal
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function addArchiveTicketInfo($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array();

    // find ticket description and solution in db
    $sql = "SELECT  Ticket.TicketDescription, Solution.Explanation,  
    Ticket.ReporterID, Ticket.AssignedSpecialistID, Ticket.OperatorID,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.ReporterID) AS Reporter,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.AssignedSpecialistID) AS AssignedSpecialist,
    (SELECT FullName FROM Personnel WHERE Personnel.ID = Ticket.OperatorID) AS Operator,
    (SELECT Job FROM Personnel WHERE Personnel.ID = Ticket.ReporterID) AS Job,
    (SELECT PhoneNo FROM Personnel WHERE Personnel.ID = Ticket.ReporterID) as PhoneNo,
    (SELECT Job FROM Personnel WHERE Personnel.ID = Ticket.AssignedSpecialistID) AS SpecialistJob,
    (SELECT PhoneNo FROM Personnel WHERE Personnel.ID = Ticket.OperatorID) as OperatorPhoneNo,
    (SELECT PhoneNo FROM Personnel WHERE Personnel.ID = Ticket.AssignedSpecialistID) as SpecialistPhoneNo
    FROM Ticket Left JOIN Solution 
    ON Ticket.FinalSolutionID = Solution.ID
    WHERE Ticket.ID=$ID;";

    $result = $conn->query($sql);

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        $functionResult['result'] = $result->fetch_assoc();
    } else {
        // Return error
        $functionResult['error'] = "No Archived details found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult

    return $functionResult;
}

/**
 * Add software details to softpage modal
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function addSoftInfo($SoftID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array();

    // find ticket description and solution in db
    $sql = "SELECT Ticket.TicketDescription, Solution.Explanation 
    From Software left Join Ticket 
    on Software.ID = Ticket.SoftwareID
    Left JOIN Solution 
    ON Ticket.FinalSolutionID = Solution.ID
    Where Software.ID=$SoftID;";


    $result = $conn->query($sql);

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        while ($row = mysqli_fetch_array($result)) {
            $functionResult[] = array($row[0], $row[1]);
        }
    } else {
        // Return error
        $functionResult['error'] = "No software details found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult

    return $functionResult;
}

/**
 * Add equipment details to equippage modal
 * 
 * @return Array    $functionResult contains result or error generated by function
 */
function addEquipInfo($HardID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store result
    $functionResult = array(array());

    // find ticket description and solution in db
    $sql = "SELECT Ticket.TicketDescription, Solution.Explanation 
    From Hardware left Join Ticket 
    on Hardware.ID = Ticket.HardwareID
    Left JOIN Solution 
    ON Ticket.FinalSolutionID = Solution.ID
    Where Hardware.ID=$HardID;";


    $result = $conn->query($sql);

    // If query returns results
    if ($result->num_rows > 0) {
        // Return result
        while ($row = mysqli_fetch_array($result)) {
            $functionResult[] = array($row[0], $row[1]);
        }
    } else {
        // Return error
        $functionResult['error'] = "No equipment details found";
    }

    // Close connection to database
    $conn->close();
    // Return functionResult

    return $functionResult;
}
