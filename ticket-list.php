<?php
include("header.php");

require_once './classes.php';
require_once './get-functions.php';

// Get all data to be displayed on the tickets
$personnels = getPersonnel();
$operators = getOperators();
$specialists = getSpecialists();
$problemTypes = getProblemTypes();
$softwares = getSoftware();
$hardwares = getHardware();
$tickets = getTickets();
$solutions = getSolutions();

/**
 * Inserts a row into the ticketLog table in the database with the details specified
 * 
 * @param Number $ID                    Unique ID of ticket that log is related to
 * @param String $LogType               Type of the log (e.g: Comment, Create, UpdateReporter,...)
 * @param String $Text                  Text associated with the log
 * @param Number $OriginPersonnelID     The ID of the user that caused the log
 * @param Number $AssignedPersonnelID   The ID of the user that has been assigned to the ticket
 */
function sendTicketLog($ID, $LogType, $Text, $OriginPersonnelID, $AssignedPersonnelID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $Text = $conn->real_escape_string($Text);

    // Insert row into TicketLog table in db
    $sql = "INSERT INTO TicketLog 
    (TicketID, LogType, OriginPersonnelID"
        . ($Text ? ", Text" : '')
        . ($AssignedPersonnelID ? ', AssignedPersonnelID' : '')
        . ") VALUES ('$ID', '$LogType', '$OriginPersonnelID'" . ($Text ? ", '$Text'" : '')
        . ($AssignedPersonnelID ? ", '$AssignedPersonnelID'" : '')
        . ");";

    // If query was unsuccessful
    if ($conn->query($sql) !== TRUE) {
        // Show error message
        showError("ERROR: Could not send ticket log");
    }

    // Close connection to database
    $conn->close();
}

// If request is made back to same page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    $personnelIDs = array_column($personnels, 'ID');
    $operatorIDs = array_column($operators, 'ID');
    $specialistIDs = array_column($specialists, 'PersonID');

    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    switch ($_POST['form_type']) {
        case "create-ticket":
            $typeID = null;
            $assignedSpecialistID = null;

            // If user is adding a new problem type
            if ($_POST['typeID'] == -1) {
                $newCreateTypeText = $conn->real_escape_string($_POST['newCreateTypeText']);
                // Check if problem already exists
                $sql = "SELECT Problem FROM ProblemType WHERE Problem = '$newCreateTypeText'";
                $result = $conn->query($sql);

                // If query returns results
                if ($result->num_rows > 0) {
                    // Return error
                    $error = true;
                    showError('ERROR: Problem type already exists');
                } else {
                    // Insert row into problemType table
                    $sql = "INSERT INTO ProblemType (Problem) VALUES ('$newCreateTypeText')";

                    // If query was successful
                    if ($conn->query($sql) === TRUE) {
                        // Set type ID of ticket to new type ID
                        $typeID = $conn->insert_id;
                    } else {
                        // Display error
                        showError('ERROR: Could not add problem type');
                    }
                }

                // If user is choosing an existing problem type
            } else if ($_POST['typeID'] != 0) {
                // Set type ID of ticket to chosen problem type
                $typeID = $_POST['typeID'];
            }

            // Check assigned specialist ID is valid
            $assignedSpecialistID = $conn->real_escape_string($_POST['assignedSpecialistID']);
            if ($_POST['assignedSpecialistID'] > 0 && !in_array($assignedSpecialistID, $specialistIDs)) {
                showError("ERROR: Invalid specialist");
                break;
            }

            // If user is adding a new external specialist type
            if ($_POST['assignedSpecialistID'] == -1) {
                // Protect inputs from sql injection
                $newCreateExternalSpecialistText = $conn->real_escape_string($_POST['newCreateExternalSpecialistText']);
                if (strlen($newCreateExternalSpecialistText) < 3) {
                    showError('ERROR: Please enter a valid external specialist name');
                    break;
                }
                $newCreateExternalSpecialistPhoneNo = $conn->real_escape_string($_POST['newCreateExternalSpecialistPhoneNo']);
                if (strlen($newCreateExternalSpecialistPhoneNo) < 8) {
                    showError('ERROR: Please enter a valid external specialist phone number');
                    break;
                }
                $newCreateExternalSpecialistPassword = password_hash($conn->real_escape_string($_POST['newCreateExternalSpecialistPassword']), PASSWORD_DEFAULT);
                if (strlen($conn->real_escape_string($_POST['newCreateExternalSpecialistPassword'])) < 5) {
                    showError('ERROR: New external specialist password at least 5 characters long');
                    break;
                }
                $newCreateExternalSpecialistSpeciality = $conn->real_escape_string($_POST['newCreateExternalSpecialistSpeciality']);
                // Insert row into personnel table
                // Generate username from fullname
                $username = substr(preg_replace('/\s/', '', $newCreateExternalSpecialistText), 0, 15);

                // Check if solution already exists
                $sql = "SELECT username FROM Personnel WHERE username = '$username'";
                $result = $conn->query($sql);

                // If query returns results
                if ($result->num_rows > 0) {
                    // Return error
                    showError('ERROR: External specialist already exists');
                    break;
                } else {
                    // Add external specialist to personnel, specialist and specialistActiveExternal tables
                    $sql = "INSERT INTO Personnel (Username, FullName, Job, Dept, PhoneNo, PasswordHash) VALUES 
                        ('$username', '$newCreateExternalSpecialistText', 'External Specialist', '6', '$newCreateExternalSpecialistPhoneNo', '$newCreateExternalSpecialistPassword');";

                    // Add external specialist to personnel, specialist and specialistActiveExternal tables
                    if ($conn->query($sql) === TRUE) {
                        $assignedSpecialistID = $conn->insert_id;

                        $sql = "INSERT INTO Specialist (PersonID, SpecialtyID) VALUES 
                        ('$assignedSpecialistID','$newCreateExternalSpecialistSpeciality');";

                        if ($conn->query($sql) !== TRUE) {
                            showError('ERROR: Could not add external specialist');
                            break;
                        }

                        $sql = "INSERT INTO SpecialistActiveExternal (SpecialistID, External) VALUES 
                        ('$assignedSpecialistID','1');";

                        if ($conn->query($sql) !== TRUE) {
                            showError('ERROR: Could not add external specialist');
                            break;
                        }
                    } else {
                        // Display error
                        showError('ERROR: Could not add external specialist');
                        break;
                    }
                }
                // If user is choosing an existing specialist
            } else if ($assignedSpecialistID > 0) {
                // Set type ID of ticket to chosen specialist
                $assignedSpecialistID = $conn->real_escape_string($_POST['assignedSpecialistID']);
            }

            // Protect inputs from sql injection
            $ticketPriority = $conn->real_escape_string($_POST['ticketPriority']);
            if ($ticketPriority < 1 || $ticketPriority > 5) {
                showError("ERROR: Invalid ticket priority");
                break;
            }
            $typeID = $conn->real_escape_string($_POST['typeID']);
            $ticketDescription = $conn->real_escape_string($_POST['ticketDescription']);
            // Check reporter ID is valid
            $reporterID = $conn->real_escape_string($_POST['reporterID']);
            if (!in_array($reporterID, $personnelIDs)) {
                showError("ERROR: Invalid reporter");
                break;
            }
            // Check operator ID is valid
            $operatorID = $conn->real_escape_string($_POST['operatorID']);
            if (!in_array($operatorID, $operatorIDs)) {
                showError("ERROR: Invalid operator");
                break;
            }
            $softwareID = $conn->real_escape_string($_POST['softwareID']);
            $hardwareID = $conn->real_escape_string($_POST['hardwareID']);
            if (!$softwareID && !$hardwareID) {
                showError("ERROR: Ticket must have either hardware or software assigned");
                break;
            }

            // Insert row into ticket table
            $sql = "INSERT INTO Ticket 
                (TicketPriority, TypeID, TicketDescription, ReporterID, OperatorID "
                . ($_POST['assignedSpecialistID'] ? ', AssignedSpecialistID' : '')
                . ($_POST['softwareID'] ? ', SoftwareID' : '')
                . ($_POST['hardwareID'] ? ', HardwareID' : '')
                . ") VALUES ('$ticketPriority', '$typeID', '$ticketDescription', '$reporterID', '"
                . $_SESSION['userid']
                . ($assignedSpecialistID ? "', '" . $assignedSpecialistID : '')
                . ($softwareID ? "', '" . $softwareID : '')
                . ($hardwareID ? "', '" . $hardwareID : '') . '\')';

            // If query was successful
            if ($conn->query($sql) === TRUE) {
                // If query was not successful
                if ($conn->query($sql) !== TRUE) {
                    // Display error
                    showError('ERROR: Could not update external specialist');
                }
                // Refresh the page so the user can see the new ticket
                echo '<script>
                window.location.href = "";
                </script>';
            } else {
                // Display error
                showError('ERROR: Invalid value in input field');
            }
            break;

        case "update-ticket":
            $typeID = null;
            $assignedSpecialistID = null;
            // Get details of ticket
            $ID = $conn->real_escape_string($_POST['ID']);
            $TicketDetails = getTicketDetails($ID);
            if (!$TicketDetails) {
                showError("ERROR: Invalid value in input field");
                break;
            };
            // Prevent sql injection
            $ticketPriority = $conn->real_escape_string($_POST['ticketPriority']);
            if ($ticketPriority < 1 || $ticketPriority > 5) {
                showError("ERROR: Invalid ticket priority");
                break;
            }
            $typeID = $conn->real_escape_string($_POST['typeID']);
            $ticketDescription = $conn->real_escape_string($_POST['ticketDescription']);
            $ticketState = $conn->real_escape_string($_POST['ticketState']);
            if (!in_array($ticketState, ["TODO", "INPROGRESS", "INREVIEW", "RESOLVED"])) {
                showError("ERROR: Invalid ticket state");
                break;
            }
            // Check reporter ID is valid
            $reporterID = $conn->real_escape_string($_POST['reporterID']);
            if (!in_array($reporterID, $personnelIDs)) {
                showError("ERROR: Invalid reporter");
                break;
            }
            // Check operator ID is valid
            $operatorID = $conn->real_escape_string($_POST['operatorID']);
            if (!in_array($operatorID, $operatorIDs)) {
                showError("ERROR: Invalid operator");
                break;
            }
            // Check assigned specialist ID is valid
            $assignedSpecialistID = $conn->real_escape_string($_POST['assignedSpecialistID']);
            if ($_POST['assignedSpecialistID'] > 0 && !in_array($assignedSpecialistID, $specialistIDs)) {
                showError("ERROR: Invalid specialist");
                break;
            }
            $softwareID = $conn->real_escape_string($_POST['softwareID']);
            $hardwareID = $conn->real_escape_string($_POST['hardwareID']);
            if (!$softwareID && !$hardwareID) {
                showError("ERROR: Ticket must have either hardware or software assigned");
                break;
            }
            $finalSolutionID = $conn->real_escape_string($_POST['finalSolutionID']);

            // If user has changed the state of the ticket
            if ($ticketState && $TicketDetails->TicketState != $ticketState) {
                if ($ticketState != "RESOLVED") {
                    // Remove resolved time on ticket
                    // Create connection
                    if ($TicketDetails->ResolvedTimestamp) {
                        // Remove resolved timestamp from ticket
                        $sql = "UPDATE Ticket
                        SET ResolvedTimestamp = NULL
                        WHERE ID = $ID";

                        // If query was unsuccessful
                        if ($conn->query($sql) !== TRUE) {
                            showError('ERROR: Could not update ticket');
                        }
                    };
                    // If query was not successful
                    if ($conn->query($sql) !== TRUE) {
                        // Display error
                        showError('ERROR: Could not update external specialist');
                    }
                };
                // If ticket is not assigned and destination state is not "TODO"
                if (!($TicketDetails->OperatorID || $TicketDetails->AssignedSpecialistID) && !$ticketState == "TODO") {
                    // Display error message
                    showError("ERROR: Ticket must have operator or specialist assigned to move to \"IN PROGRESS\", \"IN REVIEW\" or \"RESOLVED\" columns");
                    break;
                    // If ticket has no solution and destination state is "INREVIEW" or "RESOLVED"
                } else if ((!$TicketDetails->FinalSolutionID && !$finalSolutionID) && in_array($ticketState, ["INREVIEW", "RESOLVED"])) {
                    // Display arror message
                    showError("ERROR: Ticket must have solution to move to \"IN REVIEW\" or \"RESOLVED\" columns");
                    break;
                }
            }

            // If user is adding a new solution
            $solutionID = null;
            if ($finalSolutionID == -1) {
                $newSolutionText = $conn->real_escape_string($_POST['newSolutionText']);

                // Check if solution already exists
                $sql = "SELECT Explanation FROM Solution WHERE Explanation = '$newSolutionText'";
                $result = $conn->query($sql);

                // If query returns results
                if ($result->num_rows > 0) {
                    // Return error
                    showError('ERROR: Solution already exists');
                    break;
                } else {
                    // Insert row into solution table
                    $sql = "INSERT INTO Solution
                    (ProviderID, Explanation)
                    VALUES ('"
                        . ($assignedSpecialistID ? $assignedSpecialistID : $operatorID) . "' , '"
                        . $newSolutionText . "')";

                    // If query was successful
                    if ($conn->query($sql) === TRUE) {
                        // Set type ID of ticket to new type ID
                        $solutionID = $conn->insert_id;
                    } else {
                        // Display error
                        showError('ERROR: Could not add new solution');
                    }
                }
                // If user is choosing an existing solution
            } else if ($finalSolutionID != 0) {
                // Set solution ID of ticket to chosen solution
                $solutionID = $finalSolutionID;
            }

            // If user is adding a new problem type
            $finalTypeID = null;
            if ($typeID == -1) {
                $newTypeText = $conn->real_escape_string($_POST['newTypeText']);
                // Check if problem already exists
                $sql = "SELECT Problem FROM ProblemType WHERE Problem = '$newTypeText'";
                $result = $conn->query($sql);

                // If query returns results
                if ($result->num_rows > 0) {
                    // Return error
                    $error = true;
                    showError('ERROR: Problem type already exists');
                } else {
                    // Insert row into problemType table
                    $sql = "INSERT INTO ProblemType (Problem) VALUES ('$newTypeText')";

                    // If query was successful
                    if ($conn->query($sql) === TRUE) {
                        // Set type ID of ticket to new type ID
                        $finalTypeID = $conn->insert_id;
                    } else {
                        // Display error
                        showError('ERROR: Could not add problem type');
                    }
                }
                // If user is choosing an existing problem type
            } else if ($typeID != 0) {
                // Set type ID of ticket to chosen problem type
                $finalTypeID = $typeID;
            }

            // If user is adding a new external specialist type
            if ($_POST['assignedSpecialistID'] == -1) {
                // Protect inputs from sql injection
                $newExternalSpecialistText = $conn->real_escape_string($_POST['newExternalSpecialistText']);
                if (strlen($newExternalSpecialistText) < 3) {
                    showError('ERROR: Please enter a valid external specialist name');
                    break;
                }
                $newExternalSpecialistPhoneNo = $conn->real_escape_string($_POST['newExternalSpecialistPhoneNo']);
                if (strlen($newExternalSpecialistPhoneNo) < 8) {
                    showError('ERROR: Please enter a valid external specialist phone number');
                    break;
                }
                $newExternalSpecialistPassword = password_hash($conn->real_escape_string($_POST['newExternalSpecialistPassword']), PASSWORD_DEFAULT);
                if (strlen($conn->real_escape_string($_POST['newExternalSpecialistPassword'])) < 5) {
                    showError('ERROR: New external specialist password at least 5 characters long');
                    break;
                }
                $newExternalSpecialistSpeciality = $conn->real_escape_string($_POST['newExternalSpecialistSpeciality']);
                // Generate username from fullname
                $username = substr(preg_replace('/\s/', '', $newExternalSpecialistText), 0, 15);

                // Check if solution already exists
                $sql = "SELECT username FROM Personnel WHERE username = '$username'";
                $result = $conn->query($sql);

                // If query returns results
                if ($result->num_rows > 0) {
                    // Return error
                    showError('ERROR: External specialist already exists');
                    break;
                } else {
                    // Add external specialist to personnel, specialist and specialistActiveExternal tables
                    $sql = "INSERT INTO Personnel (Username, FullName, Job, Dept, PhoneNo, PasswordHash) VALUES 
                        ('$username', '$newExternalSpecialistText', 'External Specialist', '6', '$newExternalSpecialistPhoneNo', '$newExternalSpecialistPassword');";

                    // Add external specialist to personnel, specialist and specialistActiveExternal tables
                    if ($conn->query($sql) === TRUE) {
                        $assignedSpecialistID = $conn->insert_id;

                        $sql = "INSERT INTO Specialist (PersonID, SpecialtyID) VALUES 
                        ('$assignedSpecialistID','$newExternalSpecialistSpeciality');";

                        if ($conn->query($sql) !== TRUE) {
                            showError('ERROR: Could not add external specialist');
                            break;
                        }

                        $sql = "INSERT INTO SpecialistActiveExternal (SpecialistID, External) VALUES 
                        ('$assignedSpecialistID','1');";

                        if ($conn->query($sql) !== TRUE) {
                            showError('ERROR: Could not add external specialist');
                            break;
                        }
                    } else {
                        // Display error
                        showError('ERROR: Could not add external specialist');
                        break;
                    }
                }
                // If user is choosing an existing specialist
            } else if ($assignedSpecialistID > 0) {
                // Set type ID of ticket to chosen specialist
                $assignedSpecialistID = $conn->real_escape_string($_POST['assignedSpecialistID']);
            }

            /* 
                For each field on the ticket: if it has been changed, add a row to the TicketLog 
                table showing the updates that have been made to the ticket
            */
            if ($typeID && $TicketDetails->TypeID != $typeID) {
                sendTicketLog($ID, 'UpdateProblemType', $typeID, $_SESSION['userid'], NULL);
            }
            if ($conn->real_escape_string($TicketDetails->TicketDescription) != $ticketDescription) {
                sendTicketLog($ID, 'UpdateDescription', $ticketDescription, $_SESSION['userid'], NULL);
            }
            if ($TicketDetails->ReporterID != $reporterID) {
                sendTicketLog($ID, 'UpdateReporter', NULL, $_SESSION['userid'], $reporterID);
            }
            if ($TicketDetails->TicketPriority != $ticketPriority) {
                sendTicketLog($ID, 'UpdatePriority', $ticketPriority, $_SESSION['userid'], NULL);
            }
            if ($TicketDetails->TicketState != $ticketState) {
                sendTicketLog($ID, 'UpdateState', $ticketState, $_SESSION['userid'], NULL);
            }
            // If specialist is updated
            if (!($TicketDetails->AssignedSpecialistID || $assignedSpecialistID) && $TicketDetails->AssignedSpecialistID != $assignedSpecialistID) {
                sendTicketLog($ID, 'UpdateSpecialist', NULL, $_SESSION['userid'], $assignedSpecialistID);
            }
            if (!(!$TicketDetails->OperatorID && !$operatorID) && $TicketDetails->OperatorID != $operatorID) {
                sendTicketLog($ID, 'UpdateOperator', NULL, $_SESSION['userid'], $operatorID);
            }
            if (!(!$TicketDetails->SoftwareID && !$softwareID) && $TicketDetails->SoftwareID != $softwareID) {
                sendTicketLog($ID, 'UpdateSoftware', $softwareID, $_SESSION['userid'], NULL);
            }
            if (!(!$TicketDetails->HardwareID && !$hardwareID) && $TicketDetails->HardwareID != $hardwareID) {
                sendTicketLog($ID, 'UpdateHardware', $hardwareID, $_SESSION['userid'], NULL);
            }
            if ($solutionID && $TicketDetails->FinalSolutionID != $solutionID) {
                sendTicketLog($ID, 'AddSolution', $solutionID, $_SESSION['userid'], NULL);
            }

            // Update ticket record in database with new fields
            $sql = "UPDATE Ticket SET
                TicketPriority = '$ticketPriority', 
                TypeID = '$finalTypeID', 
                TicketDescription = '$ticketDescription', 
                TicketState = '$ticketState', 
                ReporterID = '$reporterID', 
                AssignedSpecialistID = " . ($assignedSpecialistID ? "'" . $assignedSpecialistID . "'" : 'NULL') . ", 
                OperatorID = " . ($operatorID ? "'" . $operatorID . "'" : 'NULL') . ", 
                SoftwareID = " . ($softwareID ? "'" . $softwareID . "'" : 'NULL') . ", 
                FinalSolutionID = " . ($solutionID ? "'" . $solutionID . "'" : 'NULL') . ", 
                HardwareID = " . ($hardwareID ? "'" . $hardwareID . "'" : 'NULL') .
                ($ticketState == "RESOLVED" ? ", ResolvedTimestamp = CURRENT_TIMESTAMP" : "") .
                " WHERE ID = " . $ID;

            // If query was successful
            if ($conn->query($sql) === TRUE) {
                // Refresh page so ticket is updated on page
                echo '<script>
                window.location.href = "";
                </script>';
            } else {
                // Display error
                showError('ERROR: Invalid value in input field');
                // showError($conn->error);
            }

            break;
    }

    // Close connection to database
    $conn->close();
}

?>
<link rel="stylesheet" href="css/ticket-list.css">

<title>Ticket List</title>

</head>

<body>
    <!-- Container for whole page -->
    <div class="container-fluid">
        <div class="main-row wrapper">
            <!-- Navigation bar -->
            <?php
            include("navbar.php");
            ?>
            <main>
                <div class="alertContainer"></div>
                <center style="padding-top: 40px; font-size: xx-large; font-weight: bolder;">Ticket List</center>

                <div class="wrapper">
                    <div class="third-row" id="create-button-col">
                        <!-- Only show button if user is an operator -->
                        <?php if ($_SESSION['deptName'] == "Operator") { ?>
                            <!-- Open create ticket modal -->
                            <button onclick="createTicket()" type="button" data-bs-toggle="modal" data-bs-target="#create-ticket-modal">
                                Create ticket
                            </button>
                        <?php } ?>
                    </div>
                    <div class="third-row search-row" id="search-col">
                        <!-- Search bar -->
                        <div class="wrapper">
                            <div class="input-extended input-extended-left search-input">
                                <span class="input-extension-left" id="search-addon"><i class="fas fa-search"></i></span>
                                <!-- Search input -->
                                <input autocomplete="off" onkeyup="ticketSearch();" id="ticket-search" type="text" placeholder="Search..." aria-label="search" aria-describedby="search-addon">
                                <!-- Select to choose field to search by -->
                                <select onchange="ticketSearch();" class="input-extension-right" id="search-dropdown" aria-label="Default select example">
                                    <option value="ID" selected>ID</option>
                                    <option value="description">Description</option>
                                    <option value="Software">Software</option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Reporter">Reporter</option>
                                    <option value="Operator">Operator</option>
                                    <option value="Specialist">Specialist</option>
                                </select>
                            </div>
                        </div>
                        <!-- Help text below search bar -->
                        <div class="full-row">
                            <div id="search-help">Search for ticket by ID, description or personnel</div>
                        </div>
                    </div>
                    <div class="third-row" id="only-show-assigned-col">
                        <!-- Only show checkbox if user is operator -->
                        <?php if ($_SESSION['deptName'] == "Operator") { ?>
                            <div class="only-show-assigned">
                                <!-- If checked, will only show tickets that are assigned to the user -->
                                <input onclick="onlyShowAssigned(this);" type="checkbox" <?= $_SESSION['showOnlyAssigned'] ?> id="onlyShowAssigned">
                                <label for="onlyShowAssigned">
                                    Only show my tickets
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Create ticket modal -->
                <div id="create-ticket-modal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the ticket database -->
                        <form id="create-form" name="create-form" action="ticket-list.php" method="POST" class="modal-form" onsubmit="return checkCreateForm(true);">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="create-ticket">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Create ticket</h3>
                                    <button type="button" onclick="closeModal();" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <div class="wrapper">
                                        <!-- Ticket ID (automatically generated so cannot be edited) -->
                                        <div class="half-row">
                                            <label for="problemID">Ticket ID</label>
                                            <input id="problemID" name="problemID" type="number" placeholder="" value="" required disabled>
                                        </div>
                                        <!-- Dropdown list of reporters to assign to the ticket -->
                                        <div class="half-row">
                                            <label for="reporterID">Reporter</label>
                                            <select id="reporterID" name="reporterID">
                                                <!-- Add all personnel from database to the select as items -->
                                                <?php foreach ($personnels as $personnel) { ?>
                                                    <option value=<?= $personnel->ID ?>><?= $personnel->FullName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of priorities for the ticket -->
                                        <div class="half-row">
                                            <label for="ticketPriority">Priority</label>
                                            <select id="ticketPriority" name="ticketPriority">
                                                <option value=1>1 (Lowest)</option>
                                                <option value=2>2</option>
                                                <option value=3>3</option>
                                                <option value=4>4</option>
                                                <option value=5>5 (Highest)</option>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of problem types -->
                                        <div class="half-row">
                                            <label for="typeID">Problem type</label>
                                            <select onchange="addNewCreateType(this);" id="typeID" name="typeID">
                                                <!-- Add all problem types from database to select as items -->
                                                <option value=-1>New</option>
                                                <?php $i = 0;
                                                foreach ($problemTypes as $problemType) { ?>
                                                    <option <?= $i == 0 ? "selected" : "" ?> value=<?= $problemType->ID ?>><?= $problemType->Problem ?></option>
                                                <?php if ($i == 0) $i++;
                                                } ?>
                                            </select>
                                        </div>
                                        <!-- Textbox to add new problem Type -->
                                        <div class="full-row newCreateTypeCol">
                                            <label for="newCreateTypeText">New Type Explanation</label>
                                            <input id="newCreateTypeText" name="newCreateTypeText" type="text" placeholder="">
                                        </div>
                                        <!-- Dropdown list of operators -->
                                        <div class="half-row">
                                            <label for="operatorID">Phone Operator</label>
                                            <select id="operatorID" name="operatorID">
                                                <!-- Add all operators from database to select as items -->
                                                <?php foreach ($operators as $operator) { ?>
                                                    <option value=<?= $operator->ID ?>><?= $operator->Workload ?>) <?= $operator->FullName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of specialists -->
                                        <div class="half-row">
                                            <label for="assignedSpecialistID">Assigned Specialist</label>
                                            <select onchange="addCreateExternalSpecialist(this);" id="assignedSpecialistID" name="assignedSpecialistID">
                                                <option value=0>Not assigned</option>
                                                <!-- Option to add external specialist -->
                                                <option value=-1>New External Specialist</option>
                                                <!-- Add all specialists from database to select as items -->
                                                <?php foreach ($specialists as $specialist) { ?>
                                                    <option value=<?= $specialist->PersonID ?>><?= $specialist->Workload ?>) <?= $specialist->Problem ?> - <?= $specialist->FullName ?><?= $specialist->External ? ' (External)' : '' ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Textbox to add new external specialist -->
                                        <div class="full-row newCreateExternalSpecialistCol">
                                            <div class="half-row">
                                                <label for="newCreateExternalSpecialistText">New External Specialist Name</label>
                                                <input id="newCreateExternalSpecialistText" name="newCreateExternalSpecialistText" type="text" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newCreateExternalSpecialistPhoneNo">New External Specialist Phone No</label>
                                                <input id="newCreateExternalSpecialistPhoneNo" name="newCreateExternalSpecialistPhoneNo" type="number" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newCreateExternalSpecialistPassword">New External Specialist Password</label>
                                                <input id="newCreateExternalSpecialistPassword" name="newCreateExternalSpecialistPassword" type="text" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newCreateExternalSpecialistSpeciality">New External Specialist Speciality</label>
                                                <select id="newCreateExternalSpecialistSpeciality" name="newCreateExternalSpecialistSpeciality">
                                                    <!-- Add all problem types from database to select as items -->
                                                    <?php $i = 0;
                                                    foreach ($problemTypes as $problemType) { ?>
                                                        <option <?= $i == 0 ? "selected" : "" ?> value=<?= $problemType->ID ?>><?= $problemType->Problem ?></option>
                                                    <?php if ($i == 0) $i++;
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Textarea for description -->
                                        <div class="full-row">
                                            <label for="ticketDescription">Description</label>
                                            <textarea id="ticketDescription" name="ticketDescription" type="text" placeholder="" maxlength="255" required></textarea>
                                        </div>
                                        <!-- Dropdown list of software -->
                                        <div class="software-input half-row">
                                            <label for="softwareID">Software</label>
                                            <select id="softwareID" name="softwareID">
                                                <option selected value=0>Problem is not software related</option>
                                                <!-- Add all software from database to select as items -->
                                                <?php foreach ($softwares as $software) { ?>
                                                    <option value=<?= $software->ID ?>><?= $software->SoftwareName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of hardware -->
                                        <div class="hardware-input half-row">
                                            <label for="hardwareID">Hardware</label>
                                            <select id="hardwareID" name="hardwareID">
                                                <option selected value=0>Problem is not hardware related</option>
                                                <!-- Add all hardware from database to select as items -->
                                                <?php foreach ($hardwares as $hardware) { ?>
                                                    <option value=<?= $hardware->ID ?>><?= $hardware->Make ?> <?= $hardware->Device ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button group to either cancel or submit ticket -->
                                <hr>
                                <div class="modal-footer">
                                    <button type="button" onclick="closeModal();" class="button-close-bottom" data-bs-dismiss="modal">Close</button>
                                    <button type="submit">Create ticket</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- View ticket modal -->
                <div id="view-ticket-modal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="update-form" name="update-form" action="ticket-list.php" method="POST" class="modal-form" onsubmit="return checkUpdateForm(true);">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="update-ticket">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">View ticket</h3>
                                    <button type="button" onclick="closeModal();" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <div class="wrapper">
                                        <!-- Ticket ID (automatically generated so cannot be edited) -->
                                        <div class="half-row">
                                            <label for="viewID">Ticket ID</label>
                                            <input id="viewID" name="ID" type="number" placeholder="" value="" required disabled>
                                        </div>
                                        <!-- Created timestamp of the ticket (not editable) -->
                                        <div class="half-row">
                                            <label for="viewCreatedTimestamp">Created Timestamp</label>
                                            <input id="viewCreatedTimestamp" name="createdTimestamp" type="text" placeholder="" disabled>
                                        </div>
                                        <!-- Dropdown list of reporters -->
                                        <div class="full-row">
                                            <label for="viewReporterID">Reporter</label>
                                            <div class="input-extended input-extended-right">
                                                <select onchange="getPhoneNo(this);" id="viewReporterID" name="reporterID" class="personnel-select " <?= $_SESSION['deptName'] == 'Operator' ? '' : 'disabled' ?>>
                                                    <!-- Add all reporters from database as items to select -->
                                                    <?php foreach ($personnels as $personnel) { ?>
                                                        <option value=<?= $personnel->ID ?>><?= $personnel->FullName ?></option>
                                                    <?php } ?>
                                                </select>
                                                <!-- Selected reporter phone number (not editable) -->
                                                <label class="input-extension-right" for="reporterID" id="viewReporterNo"></label>
                                            </div>
                                        </div>
                                        <!-- Dropdown list of operators -->
                                        <div class="full-row">
                                            <label for="viewOperatorID">Phone Operator</label>
                                            <div class="input-extended input-extended-right">
                                                <select onchange="getPhoneNo(this);" id="viewOperatorID" name="operatorID" class="personnel-select " <?= $_SESSION['deptName'] == 'Operator' ? '' : 'disabled' ?>>
                                                    <!-- Add all operators from database as items to select -->
                                                    <?php foreach ($operators as $operator) { ?>
                                                        <option value=<?= $operator->ID ?>><?= $operator->Workload ?>) <?= $operator->FullName ?></option>
                                                    <?php } ?>
                                                </select>
                                                <!-- Selected operator phone number (not editable) -->
                                                <label class="input-extension-right" for="operatorID" id="viewOperatorNo"></label>
                                            </div>
                                        </div>
                                        <!-- Dropdown list of specialist -->
                                        <div class="full-row">
                                            <label for="viewAssignedSpecialistID">Assigned Specialist</label>
                                            <div class="input-extended input-extended-right">
                                                <select onchange="getPhoneNo(this); addExternalSpecialist(this)" id="viewAssignedSpecialistID" name="assignedSpecialistID" <?= ($_SESSION['deptName'] == "Specialist" ? "disabled" : "") ?> class="personnel-select ">
                                                    <option selected value=0>Not assigned</option>
                                                    <!-- Option to add external specialist -->
                                                    <option value=-1>New External Specialist</option>
                                                    <!-- Add all specialists from database as items to select -->
                                                    <?php foreach ($specialists as $specialist) { ?>
                                                        <option value=<?= $specialist->PersonID ?>><?= $specialist->Workload ?>) <?= $specialist->Problem ?> - <?= $specialist->FullName ?><?= $specialist->External ? ' (External)' : '' ?></option>
                                                    <?php } ?>
                                                </select>
                                                <!-- Selected specialist phone number (not editable) -->
                                                <label class="input-extension-right" for="specialistID" id="viewSpecialistNo"></label>
                                            </div>
                                        </div>
                                        <!-- Textbox to add new external specialist -->
                                        <div class="full-row newExternalSpecialistCol">
                                            <div class="half-row">
                                                <label for="newExternalSpecialistText">New External Specialist Name</label>
                                                <input id="newExternalSpecialistText" name="newExternalSpecialistText" type="text" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newExternalSpecialistPhoneNo">New External Specialist Phone No</label>
                                                <input id="newExternalSpecialistPhoneNo" name="newExternalSpecialistPhoneNo" type="number" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newExternalSpecialistPassword">New External Specialist Password</label>
                                                <input id="newExternalSpecialistPassword" name="newExternalSpecialistPassword" type="text" placeholder="">
                                            </div>
                                            <div class="half-row">
                                                <label for="newExternalSpecialistSpeciality">New External Specialist Speciality</label>
                                                <select id="newExternalSpecialistSpeciality" name="newExternalSpecialistSpeciality">
                                                    <!-- Add all problem types from database to select as items -->
                                                    <?php $i = 0;
                                                    foreach ($problemTypes as $problemType) { ?>
                                                        <option <?= $i == 0 ? "selected" : "" ?> value=<?= $problemType->ID ?>><?= $problemType->Problem ?></option>
                                                    <?php if ($i == 0) $i++;
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Dropdown list of priorities for the ticket -->
                                        <div class="half-row">
                                            <label for="viewTicketPriority">Priority</label>
                                            <select id="viewTicketPriority" name="ticketPriority">
                                                <option value=1>1 (Lowest)</option>
                                                <option value=2>2</option>
                                                <option value=3>3</option>
                                                <option value=4>4</option>
                                                <option value=5>5 (Highest)</option>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of states for the ticket -->
                                        <div class="half-row">
                                            <label for="viewTicketState">State</label>
                                            <select id="viewTicketState" name="ticketState">
                                                <option value="TODO">TODO</option>
                                                <option value="INPROGRESS">IN PROGRESS</option>
                                                <option value="INREVIEW">IN REVIEW</option>
                                                <option value="RESOLVED">RESOLVED</option>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of problem types -->
                                        <div class="half-row">
                                            <!-- Need to include an option here for "other" and allow custom input -->
                                            <label for="viewTypeID">Problem type</label>
                                            <select onchange="addNewType(this);" id="viewTypeID" name="typeID">
                                                <option value=-1>New</option>
                                                <!-- Add all problem types from database as items to select -->
                                                <?php foreach ($problemTypes as $problemType) { ?>
                                                    <!-- Option to add new problem type -->
                                                    <option value=<?= $problemType->ID ?>><?= $problemType->Problem ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Textbox to add new problem Type -->
                                        <div class="full-row newTypeCol">
                                            <label for="newTypeText">New Type Explanation</label>
                                            <input id="newTypeText" name="newTypeText" type="text" placeholder="">
                                        </div>
                                        <!-- Text area for ticket description -->
                                        <div class="full-row">
                                            <label for="description">Description</label>
                                            <textarea type="text" id="viewTicketDescription" name="ticketDescription" placeholder="" maxlength="255" required></textarea>
                                        </div>
                                        <!-- Dropdown list of software -->
                                        <div class="software-input half-row">
                                            <label for="viewSoftwareID">Software</label>
                                            <select id="viewSoftwareID" name="softwareID">
                                                <option value=0>Problem is not software related</option>
                                                <!-- Add all software from database as items to select -->
                                                <?php foreach ($softwares as $software) { ?>
                                                    <option value=<?= $software->ID ?>><?= $software->SoftwareName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of hardware -->
                                        <div class="hardware-input half-row">
                                            <label for="viewHardwareID">Hardware</label>
                                            <select id="viewHardwareID" name="hardwareID">
                                                <option value=0>Problem is not hardware related</option>
                                                <!-- Add all hardware from database as items to select -->
                                                <?php foreach ($hardwares as $hardware) { ?>
                                                    <option value=<?= $hardware->ID ?>><?= $hardware->Make ?> <?= $hardware->Device ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Dropdown list of solutions -->
                                        <div class="full-row">
                                            <label for="viewFinalSolutionID">Solution</label>
                                            <select onchange="addNewSolution(this);" id="viewFinalSolutionID" name="finalSolutionID">
                                                <option selected value=0>Solution has not been provided</option>
                                                <!-- Option to add new solution -->
                                                <option value=-1>New</option>
                                                <!-- Add all solutions from database as items to select -->
                                                <?php foreach ($solutions as $solutions) { ?>
                                                    <option value=<?= $solutions->ID ?>><?= $solutions->Explanation ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- Textbox to add new solution description -->
                                        <div class="full-row newSolutionCol">
                                            <label for="newSolutionText">New Solution Explanation</label>
                                            <input id="newSolutionText" name="newSolutionText" type="text" placeholder="">
                                        </div>
                                        <!-- Resolved timestamp (not editable as automatically generated) -->
                                        <div class="full-row">
                                            <label for="viewResolvedTimestamp">Resolved Timestamp</label>
                                            <input id="viewResolvedTimestamp" name="resolvedTimestamp" disabled type="text" placeholder="">
                                        </div>
                                        <!-- List of all logs related to the ticket -->
                                        <div class="full-row logbox-col">
                                            <label for="logbox">Log</label>
                                            <div id="comment-item">
                                                <div class="input-extended comment-input-group">
                                                    <span class="input-extension-left">
                                                        <i class="fas fa-user-circle comment-user-icon"></i>
                                                    </span>
                                                    <input id="comment" type="text" placeholder="Add a comment.." autocomplete="off" maxlength="255">
                                                    <button class="input-extension-right" type="button" id="comment-submit" onclick="addComment()">Submit</button>
                                                </div>
                                            </div>
                                            <ul id="logbox">
                                                <!-- Comment box -->
                                                <li id="comment-item">
                                                    <div class="input-extended comment-input-group">
                                                        <span class="input-extension-left">
                                                            <i class="fas fa-user-circle comment-user-icon"></i>
                                                        </span>
                                                        <input id="comment" type="text" placeholder="Add a comment.." onkeydown="commentCheck(event);" autocomplete="off">
                                                        <button class="input-extension-right" type="button" id="comment-submit" onclick="addComment();">Submit</button>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button group to either cancel or submit ticket -->
                                <hr>
                                <div class="modal-footer">
                                    <button type="button" onclick="closeModal();" class="modal-close button-close-bottom" data-bs-dismiss="modal">Close</button>
                                    <button type="submit">Update ticket</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table that contains tickets -->
                <table>
                    <thead>
                        <!-- Table headers -->
                        <tr>
                            <th>PROBLEM TYPE</th>
                            <th>TODO</th>
                            <th>IN PROGRESS</th>
                            <th>IN REVIEW</th>
                            <th>RESOLVED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- For each type of problem, generate a row -->
                        <?php foreach ($problemTypes as $problemType) { ?>
                            <tr>
                                <th scope="wrapper"><?= $problemType->Problem ?></th>
                                <!-- For each state, generate tickets -->
                                <?php foreach (["TODO", "INPROGRESS", "INREVIEW", "RESOLVED"] as $state) { ?>
                                    <td ondragover="onDragOver(event);" ondrop="onDrop(event);">
                                        <ul class="ticket-list" data-state="<?= $state ?>" data-typeID="<?= $problemType->ID ?>">
                                            <!-- For each ticket, display as card in relevant row and column -->
                                            <?php foreach ($tickets as $ticket) {
                                                if ($ticket->TicketState == $state && $ticket->Problem == $problemType->Problem) { ?>
                                                    <li onclick="showTicket(<?= $ticket->ID ?>)" class="ticket" id="ticket<?= $ticket->ID ?>" draggable="true" ondragstart="onDragStart(event);" data-ID="<?= $ticket->ID ?>" data-Description="<?= $ticket->TicketDescription ? $ticket->TicketDescription : '' ?>" data-Reporter="<?= $ticket->Reporter ? $ticket->Reporter : '' ?>" data-Operator="<?= $ticket->Operator ? $ticket->Operator : '' ?>" data-AssignedSpecialist="<?= $ticket->AssignedSpecialist ? $ticket->AssignedSpecialist : '' ?>" data-OperatorID="<?= $ticket->OperatorID ? $ticket->OperatorID : '' ?>" data-AssignedSpecialistID="<?= $ticket->AssignedSpecialistID ? $ticket->AssignedSpecialistID : '' ?>" data-software="<?= $ticket->Software ?>" data-hardware="<?= $ticket->Hardware ?>">
                                                        <div class="ticket-body">
                                                            <p class="ticket-id">#<?= $ticket->ID ?></p>
                                                            <hr>
                                                            <h4 class="ticket-reporter"><?= $ticket->Reporter ?></h4>
                                                            <p class="ticket-description"><?= $ticket->TicketDescription ?></p>
                                                            <span class="ticket-priority" <?php
                                                                                            switch ($ticket->TicketPriority) {
                                                                                                case 1:
                                                                                                    echo 'style="color:lightgreen">&#10122';
                                                                                                    break;
                                                                                                case 2:
                                                                                                    echo 'style="color:greenyellow">&#10123';
                                                                                                    break;
                                                                                                case 3:
                                                                                                    echo 'style="color:#f8d900">&#10124';
                                                                                                    break;
                                                                                                case 4:
                                                                                                    echo 'style="color:orange">&#10125';
                                                                                                    break;
                                                                                                default:
                                                                                                    echo 'style="color:red">&#10126';
                                                                                                    break;
                                                                                            }
                                                                                            ?> </span>
                                                        </div>
                                                    </li>
                                            <?php }
                                            } ?>
                                        </ul>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>

    <!-- JS -->
    <script src="js/ticket-list.js"></script>

    <?php
    include("footer.php");
    ?>