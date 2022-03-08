<?php
include("header.php");

include_once './classes.php';
include_once './get-functions.php';
// Get all data to be displayed on the tickets
$personnels = getPersonnel();
$operators = getOperators();
$specialists = getSpecialists();
$problemTypes = getProblemTypes();
$softwares = getSoftware();
$hardwares = getHardware();
$tickets = getTickets();
$solutions = getSolutions();
?>

<!-- Including the css file to style the page -->
<link rel="stylesheet" href="css/software-page.css">

<title>Software</title>
</head>

<body>
    <div class="container-fluid">
        <div class="main-row row">
            <!-- We include the navigation bar that's present in every pages, and allows us to switch pages. -->
            <?php
            include("navbar.php");
            ?>
            <main>

                <?php
                $servername = "localhost";
                $username = "team008";
                $password = "dbnkKF2ykC";
                $dbname = "team008";
                // Create connection to the database
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Gets information about the software from the database
                $sql = "SELECT * FROM Software";
                $table = mysqli_query($conn, $sql);

                $conn->close();

                // If request is made back to same page
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
                    // Create connection
                    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }


                    switch ($_POST['form_type']) {
                        case "create-software":
                            // This is to prevent SQL injections.
                            $softwareName = $conn->real_escape_string($_POST['softwareName']);
                            $softwareVersion = $conn->real_escape_string($_POST['softwareVersion']);
                            $licenseNumber = $conn->real_escape_string($_POST['licenseNumber']);
                            $personnelID = $conn->real_escape_string($_POST['personnelID']);
                            // Insert row into the software table in the database then reloading the page.
                            $sql = "INSERT INTO Software 
                (SoftwareName, SoftwareVersion, LicenseNumber, PersonID) 
                VALUES ('"
                                . $softwareName . "', '"
                                . $softwareVersion . "', '"
                                . $licenseNumber . "', "
                                . ($personnelID != 0 ? "'" . $personnelID . "'" : "NULL") . ")";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {
                                // Refresh the page so the user can see the new software table
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                // Display error
                                echo "Error: Could not add the new equipment, input fields were invalid.";
                            }
                            break;



                        case "delete-software":
                            // This is to prevent SQL injections.
                            $removeID = $conn->real_escape_string($_POST['removeID']);
                            // Delete row from the software table in the database then reloading the page.
                            $sql = "DELETE FROM Software WHERE ID = '" . $removeID . "'";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {
                                // Refresh the page so the user can see the new softare table
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                // Display error
                                echo "<Script>alertBanner('Error: This software is used on one or more tickets.')</Script>";
                                echo "<Script>if ( window.history.replaceState ) {
                                    window.history.replaceState( null, null, window.location.href );
                                }</Script>";
                            }
                            break;
                    }
                }

                ?>
                <!-- Used to display error messsages -->
                <div class="alertContainer"></div>

                <center style="padding-top: 40px; font-size: xx-large; font-weight: bolder;">Software</center>
                <div class="wrapper">
                    <!-- Only show button if user is an operator, so we check if user is an Operator. -->
                    <?php if ($_SESSION['deptName'] == "Operator") { ?>
                        <div class="third-row" id="create-button-col">
                            <!-- Button trigger modal to add a software. -->
                            <button onclick="addSoftware()" type="button" data-bs-toggle="modal" data-bs-target="#add-software-modal">Add Software</button>
                        </div>
                    <?php } else { ?>
                        <div class="third-row"></div>
                    <?php } ?>
                    <!-- Search bar -->
                    <div class="third-row" id="search-col">
                        <div class="wrapper">
                            <div class="input-extended input-extended-left">
                                <span class="input-extension-left" id="search-addon"><i class="fas fa-search"></i></span>
                                <!-- Search input -->
                                <input autocomplete="off" onkeyup="softwareSearch();" id="software-search" type="text" class="form-control" placeholder="Search..." aria-label="search" aria-describedby="search-addon">
                                <!-- Select to choose field to search by -->
                                <select id="search-dropdown" aria-label="Default select example" class="input-extension-right">
                                    <option value="0" selected>Software ID</option>
                                    <option value="1">Software Name</option>
                                    <option value="2">Software Version</option>
                                    <option value="3">License Number</option>
                                    <option value="4">User ID</option>
                                </select>
                            </div>
                        </div>
                        <div class="full-row">
                            <!-- Help text below search bar -->
                            <div id="search-help" class="form-text">Search for software by ID, name, version, license number or user ID.</div>
                        </div>
                    </div>
                    <!-- Only show button if user is an operator, so we check if user is an Operator. -->
                    <?php if ($_SESSION['deptName'] == "Operator") { ?>
                        <div class="third-row" id="delete-button-col">
                            <!-- Button trigger modal to delete a software -->
                            <button onclick="removeSoftware()" type="button" style="float: right;" data-bs-toggle="modal" data-bs-target="#delete-software-modal">Remove Software</button>
                        </div>
                    <?php } else { ?>
                        <div class="third-row"></div>
                    <?php } ?>
                </div>

                &nbsp;
                <!-- This is to display the software table. -->
                <div>
                    <table id="soft-table" class="table1">
                        <thead>
                            <tr>
                                <th scope="col">Software ID</th>
                                <th scope="col">Software Name</th>
                                <th scope="col">Softare Version</th>
                                <th scope="col">License number</th>
                                <th scope="col">User ID</th>
                            </tr>
                        </thead>
                        <tbody id="myTable">

                            <?php if (mysqli_num_rows($table) > 0) {
                                //output data of each row from the SQL table.
                                while ($row = mysqli_fetch_array($table)) {
                                    echo "<tr class ='tableRow' onclick='rowClickModal($row[0])'>
            <td class='Soft_ID'>$row[0]</td>
            <td class='Soft_Name'>$row[1]</td>
            <td class='Soft_Version'>$row[2]</td>
            <td class='License_Number'>$row[3]</td>
            <td class='User_ID'>$row[4]</td>
			</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Add Software -->
                <div class="modal fade" id="add-software-modal" tabindex="-1">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the software database -->
                        <form id="new-software-form" name="new-software-form" action="software-page.php" method="POST" class="needs-validation" onsubmit="return checkNewSoftwareForm();">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="create-software">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Software</h5>
                                    <!-- This is a close button on the top right. -->
                                    <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <div class="wrapper">
                                        <div class="half-row">
                                            <label for="softwareName" class="form-label">Software Name</label>
                                            <input type="text" class="form-control" id="softwareName" name="softwareName" maxlength="63" required>
                                        </div>

                                        <div class="half-row">
                                            <label for="softwareVersion" class="form-label">Software Version</label>
                                            <input type="text" class="form-control" id="softwareVersion" name="softwareVersion" maxlength="63" required>
                                        </div>

                                        <div class="half-row">
                                            <label for="licenseNumber" class="form-label">License Number</label>
                                            <input type="text" class="form-control" id="licenseNumber" name="licenseNumber" maxlength="63" required>
                                        </div>

                                        <!-- Dropdown list of all personel to assign to the software. -->
                                        <div class="half-row">
                                            <label for="personnelID" class="form-label">Personnel ID</label>
                                            <select id="personnelID" name="personnelID" class="form-select">
                                                <option value="0">Not assigned</option>
                                                <!-- Add all personnel from database to the select as items -->
                                                <?php foreach ($personnels as $personnel) { ?>
                                                    <option value=<?= $personnel->ID ?>><?= $personnel->ID ?>: <?= $personnel->FullName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="modal-footer">
                                    <button onclick="closeModals()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add Software</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Modal Delete Software -->
                <div class="modal fade" id="delete-software-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the software database -->
                        <form action="software-page.php" method="POST" class="needs-validation" onsubmit="return checkRemoveSoftwareForm();">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="delete-software">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Software</h5>
                                    <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate>
                                        <div class="wrapper">
                                            <div class="half-row">
                                                <label for="removeID" class="form-label">Software ID</label>
                                                <input list= "soft-list" max="99999999999999999999" type="number" class="form-control" id="removeID" name="removeID" required>
                                                <datalist id="soft-list">
                                                <?php foreach ($softwares as $softwares) { ?>
                                                    <option value=<?= $softwares->ID ?>>ID: <?= $softwares->ID ?>, <?= $softwares->SoftwareName ?></option>
                                                <?php } ?>
                                            </datalist>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <hr>
                                <div class="modal-footer">
                                    <button onclick="closeModals()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Delete Software</button>
                                </div>
                            </div>
                    </div>
                </div>


                <!-- Modal to view software info -->
                <div class="modal fade" id="infoModal" tabindex="-1" data-row="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">List of previous problems and their solutions</h5>
                                <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                            </div>
                            <hr>
                            <div class="wrapper" id ="rowClickModalContent"> </div>
                            <hr>
                                <div class="modal-footer">
                                    <button onclick="closeModals()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                        </div>
                    </div>
                </div>



            </main>
        </div>
    </div>

    <!-- JS -->
    <script src="js/software-page.js"></script>

    <?php
    include("footer.php");
    ?>