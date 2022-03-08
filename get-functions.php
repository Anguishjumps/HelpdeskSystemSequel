<?php

require_once './classes.php';

// Configuration variables for database connection
const SERVERNAME = "localhost";
const USERNAME = "team008";
const PASSWORD = "dbnkKF2ykC";
const DBNAME = "team008";

/**
 * Displays an alert at the top of the page showing the error message
 * 
 * @param String $text  Message to be shown in alert
 */
function showError($text)
{
    echo "
    <script>
        window.scrollTo({ top: 0, behavior: 'smooth' });
        alertBanner('$text');
    </script>";
}

/**
 * Retrieves all personnel from database
 * 
 * @return Array    List of personnel in database
 */
function getPersonnel()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all personnel from database
    $sql = "SELECT ID, FullName, PhoneNo, Dept FROM Personnel";
    $result = $conn->query($sql);

    // Array to store personnel
    $personnels = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get personnel details from each record in results
        while ($row = $result->fetch_assoc()) {
            $personnel = new Personnel();
            // Set personnel attributes
            $personnel->ID = $row["ID"];
            $personnel->FullName = $row["FullName"];
            $personnel->PhoneNo = $row["PhoneNo"];
            // Add personnel to array
            array_push($personnels, $personnel);
        }
    } else {
        // Display error message
        showError("No personnel found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $personnels;
}

/**
 * Retrieves all operators from database
 * 
 * @return Array    List of operators in database
 */
function getOperators()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all operators from database
    $sql = "SELECT ID, FullName, Dept,
    (SELECT COUNT(OperatorID) FROM Ticket WHERE OperatorID = Personnel.ID AND TicketState != 'RESOLVED') AS Workload
    FROM Personnel 
    WHERE Dept = 5  
    ORDER BY Workload ASC";
    $result = $conn->query($sql);

    // Array to store operators
    $operators = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get operator details from each record in results
        while ($row = $result->fetch_assoc()) {
            $personnel = new Personnel();
            // Set operator attributes
            $personnel->ID = $row["ID"];
            $personnel->FullName = $row["FullName"];
            $personnel->Workload = $row["Workload"];
            // Add operator to array
            array_push($operators, $personnel);
        }
    } else {
        // Display error message
        showError("No operators found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $operators;
}

/**
 * Retrieves all problem types from database
 * 
 * @return Array    List of problem types in database
 */
function getProblemTypes()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all problem types from database
    $sql = "SELECT * FROM ProblemType";
    $result = $conn->query($sql);

    // Array to store problem types
    $problemTypes = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get problem type details from each record in results
        while ($row = $result->fetch_assoc()) {
            $problemType = new ProblemType();
            // Set problem type attributes
            $problemType->ID = $row["ID"];
            $problemType->Problem = $row["Problem"];
            // Add problem type to array
            array_push($problemTypes, $problemType);
        }
    } else {
        // Display error message
        showError("No problem types found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $problemTypes;
}

/**
 * Retrieves all software from database
 * 
 * @return Array    List of software in database
 */
function getSoftware()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all software from database
    $sql = "SELECT * FROM Software";
    $result = $conn->query($sql);

    // Array to store software
    $softwares = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get software details from each record in results
        while ($row = $result->fetch_assoc()) {
            $software = new Software();
            // Set software attributes
            $software->ID = $row["ID"];
            $software->SoftwareName = $row["SoftwareName"];
            $software->SoftwareVersion = $row["SoftwareVersion"];
            $software->LicenseNumber = $row["LicenseNumber"];
            $software->PersonID = $row["PersonID"];
            // Add software to array
            array_push($softwares, $software);
        }
    } else {
        // Display error message
        showError("No software found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $softwares;
}

/**
 * Retrieves all hardware from database
 * 
 * @return Array    List of hardware in database
 */
function getHardware()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all hardware from database
    $sql = "SELECT * FROM Hardware";
    $result = $conn->query($sql);

    // Array to store hardware
    $hardwares = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get hardware details from each record in results
        while ($row = $result->fetch_assoc()) {
            $hardware = new Hardware();
            // Set hardware attributes
            $hardware->ID = $row["ID"];
            $hardware->SerialNo = $row["SerialNo"];
            $hardware->Device = $row["Device"];
            $hardware->Make = $row["Make"];
            // Add hardware to array
            array_push($hardwares, $hardware);
        }
    } else {
        // Display error message
        showError("No hardware found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $hardwares;
}

/**
 * Retrieves all specialists from database
 * 
 * @return Array    List of specialists in database
 */
function getSpecialists()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all specialists from database
    $sql = "SELECT PersonID, 
    GROUP_CONCAT((SELECT Problem FROM ProblemType WHERE ID = SpecialtyID)) AS Problem, 
    (SELECT FullName FROM Personnel WHERE ID = PersonID) AS FullName,
    (SELECT COUNT(AssignedSpecialistID) FROM Ticket WHERE AssignedSpecialistID = PersonID AND TicketState != 'RESOLVED') AS Workload,
    (SELECT External FROM SpecialistActiveExternal WHERE SpecialistID = PersonID) AS External
    FROM Specialist
    GROUP BY PersonID
    ORDER BY External, Workload ASC";
    $result = $conn->query($sql);

    // Array to store specialists
    $specialists = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get specialist details from each record in results
        while ($row = $result->fetch_assoc()) {
            $specialist = new Specialist();
            // Set specialist attributes
            $specialist->PersonID = $row["PersonID"];
            $specialist->Problem = $row["Problem"];
            $specialist->FullName = $row["FullName"];
            $specialist->Workload = $row["Workload"];
            $specialist->External = $row["External"];
            // Add specialist to array
            array_push($specialists, $specialist);
        }
    } else {
        // Display error message
        showError("No specialists found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $specialists;
}

/**
 * Retrieves all tickets from database
 * 
 * @return Array    List of ticket in database
 */
function getTickets()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all tickets from database
    $sql = "SELECT Ticket.ID AS ID, CreatedTimestamp, 
    (SELECT Problem FROM ProblemType WHERE Ticket.TypeID=ProblemType.ID) AS Problem, 
    (SELECT SoftwareName FROM Software WHERE Ticket.SoftwareID=Software.ID) AS Software,
    (SELECT Device FROM Hardware WHERE Ticket.HardwareID=Hardware.ID) AS Hardware,
    (SELECT FullName FROM Personnel WHERE Ticket.ReporterID=Personnel.ID) AS Reporter,
    (SELECT FullName FROM Personnel WHERE ID = OperatorID) AS Operator,
    (SELECT FullName FROM Personnel WHERE ID = AssignedSpecialistID) AS AssignedSpecialist,
    (SELECT Explanation FROM Solution WHERE Ticket.FinalSolutionID=Solution.ID) AS Solution,
    TicketDescription, TicketPriority, ResolvedTimestamp, TicketState, AssignedSpecialistID, OperatorID
    FROM Ticket HAVING";
    // If user is specialist
    if ($_SESSION['deptName'] == "Specialist") {
        // Only show tickets that are assigned to user
        $sql .= " AssignedSpecialistID = '" . $_SESSION['userid'] . "' AND";
    }
    // If user has checked "Only show my tickets"
    if ($_SESSION['showOnlyAssigned']) {
        // Only show tickets that are assigned to the user
        $sql .= " OperatorID = '" . $_SESSION['userid'] . "' AND";
    }
    // Only select tickets that have not been solved or were solved within the last 24 hours
    $sql .= " (ResolvedTimestamp IS NULL OR ResolvedTimestamp + INTERVAL 1 DAY >= now()) ORDER BY TicketPriority DESC";
    $result = $conn->query($sql);

    // Array to store tickets
    $tickets = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get ticket details from each record in results
        while ($row = $result->fetch_assoc()) {
            $ticket = new Ticket();
            // Set ticket attributes
            $ticket->ID = $row["ID"];
            $ticket->CreatedTimestamp = $row["CreatedTimestamp"];
            $ticket->Problem = $row["Problem"];
            $ticket->Software = $row["Software"];
            $ticket->Hardware = $row["Hardware"];
            $ticket->Reporter = $row["Reporter"];
            $ticket->TicketDescription = $row["TicketDescription"];
            $ticket->TicketPriority = $row["TicketPriority"];
            $ticket->Solution = $row["Solution"];
            $ticket->ResolvedTimestamp = $row["ResolvedTimestamp"];
            $ticket->TicketState = $row["TicketState"];
            $ticket->AssignedSpecialistID = $row["AssignedSpecialistID"];
            $ticket->AssignedSpecialist = $row["AssignedSpecialist"];
            $ticket->OperatorID = $row["OperatorID"];
            $ticket->Operator = $row["Operator"];
            // Add ticket to array
            array_push($tickets, $ticket);
        }
    }

    // Close database connection
    $conn->close();
    // Return results
    return $tickets;
}


/**
 * Retrieves ticket details from specific ticket from database
 * 
 * @param Number    Unique id of ticket
 * 
 * @return Ticket    Ticket details
 */
function getTicketDetails($ID)
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select specific ticket details from database
    $sql = "SELECT *
    FROM Ticket
    WHERE ID = $ID";

    $result = $conn->query($sql);
    
    // If query returns results
    if ($result && $result->num_rows > 0) {
        // Get ticket details from record
        $row = $result->fetch_assoc();
        $ticket = new Ticket();
        // Set ticket attributes
        $ticket->ID = $row['ID'];
        $ticket->CreatedTimestamp = $row['CreatedTimestamp'];
        $ticket->TypeID = $row['TypeID'];
        $ticket->SoftwareID = $row['SoftwareID'];
        $ticket->HardwareID = $row['HardwareID'];
        $ticket->ReporterID = $row['ReporterID'];
        $ticket->TicketDescription = $row['TicketDescription'];
        $ticket->TicketPriority = $row['TicketPriority'];
        $ticket->FinalSolutionID = $row['FinalSolutionID'];
        $ticket->ResolvedTimestamp = $row['ResolvedTimestamp'];
        $ticket->TicketState = $row['TicketState'];
        $ticket->AssignedSpecialistID = $row['AssignedSpecialistID'];
        $ticket->OperatorID = $row['OperatorID'];
    } else {
        // Display error message
        showError("ERROR: No ticket details found in database");
        return null;
    }

    // Close database connection
    $conn->close();
    // Return results
    return $ticket;
}

/**
 * Retrieves all solutions from database
 * 
 * @return Array    List of solutions in database
 */
function getSolutions()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all solutions from database
    $sql = "SELECT * FROM Solution";
    $result = $conn->query($sql);

    $solutions = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get solution details from each record in results
        while ($row = $result->fetch_assoc()) {
            $solution = new Solution();
            // Set solution attributes
            $solution->ID = $row["ID"];
            $solution->ProviderID = $row["ProviderID"];
            $solution->Explanation = $row["Explanation"];
            // Add solution to array
            array_push($solutions, $solution);
        }
    } else {
        // Display error message
        showError("No solutions found in database");
    }

    // Close database connection
    $conn->close();
    // Return results
    return $solutions;
}


/**
 * Retrieves all tickets from database
 * 
 * @return Array    List of ticket in database
 */
function getAllTickets()
{
    // Create connection
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select all tickets from database
    $sql = "SELECT Ticket.ID AS ID,
    (SELECT Problem FROM ProblemType WHERE Ticket.TypeID=ProblemType.ID) AS Problem, 
    (SELECT FullName FROM Personnel WHERE Ticket.ReporterID=Personnel.ID) AS Reporter,
    (SELECT FullName FROM Personnel WHERE ID = OperatorID) AS Operator,
    (SELECT FullName FROM Personnel WHERE ID = AssignedSpecialistID) AS AssignedSpecialist,
    TicketDescription, TicketPriority, TicketState, AssignedSpecialistID, OperatorID
    FROM Ticket
    ORDER BY Ticket.ID DESC";
    
    $result = $conn->query($sql);

    // Array to store tickets
    $tickets = [];

    // If query returns results
    if ($result->num_rows > 0) {
        // Get ticket details from each record in results
        while ($row = $result->fetch_assoc()) {
            $ticket = new Ticket();
            // Set ticket attributes
            $ticket->ID = $row["ID"];
            $ticket->Problem = $row["Problem"];
            $ticket->Reporter = $row["Reporter"];
            $ticket->TicketDescription = $row["TicketDescription"];
            $ticket->TicketPriority = $row["TicketPriority"];
            $ticket->TicketState = $row["TicketState"];
            $ticket->AssignedSpecialistID = $row["AssignedSpecialistID"];
            $ticket->AssignedSpecialist = $row["AssignedSpecialist"];
            $ticket->OperatorID = $row["OperatorID"];
            $ticket->Operator = $row["Operator"];
            // Add ticket to array
            array_push($tickets, $ticket);
        }
    }

    // Close database connection
    $conn->close();
    // Return results
    return $tickets;
}
