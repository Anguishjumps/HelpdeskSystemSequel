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
<link rel="stylesheet" href="css/equipment-page.css">

<title>Equipment</title>
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

                // Gets information about equipment from the database
                $sql = "SELECT * FROM `Hardware`";
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
                        case "create-equipment":
                            // This is to prevent SQL injections.
                            $serialNo = $conn->real_escape_string($_POST['serialNo']);
                            $device = $conn->real_escape_string($_POST['device']);
                            $make = $conn->real_escape_string($_POST['make']);
                            $personnelID = $conn->real_escape_string($_POST['personnelID']);
                            // Insert row into the equiupment table in the database then reloading the page
                            $sql = "INSERT INTO Hardware 
                (SerialNo, Device, Make, PersonID) 
                VALUES ('"
                                . $serialNo . "', '"
                                . $device . "', '"
                                . $make . "', "
                                . ($personnelID != 0 ? "'" . $personnelID . "'" : "NULL") . ")";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {
                                // Refresh the page so the user can see the new table
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                // Display error
                                echo "Error: Could not add the new equipment, input fields were invalid.";
                            }
                            break;


                        case "delete-equipment":
                            // This is to prevent SQL injections.
                            $removeID = $conn->real_escape_string($_POST['removeID']);
                            // Delete row from the equipment table in the database then reloading the page.
                            $sql = "DELETE FROM Hardware WHERE ID = '" . $removeID . "'";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {
                                // Refresh the page so the user can see the new table
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                // Display error.
                                echo "<Script>alertBanner('Error: This equipment is used on one or more tickets.')</Script>";
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
                <center style="padding-top: 40px; font-size: xx-large; font-weight: bolder;">Equipment</center>
                <div class="wrapper">
                    <!-- Only show button if user is an operator -->
                    <?php if ($_SESSION['deptName'] == "Operator") { ?>
                        <div id="create-button-col" class="third-row">
                            <!-- Button trigger modal to add an equipment. -->
                            <button onclick="addEquipment()" type="button" data-bs-toggle="modal" data-bs-target="#add-equipment-modal">Add Equipment</button>
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
                                <input autocomplete="off" onkeyup="equipmentSearch();" id="equipment-search" type="text" class="form-control" placeholder="Search..." aria-label="search" aria-describedby="search-addon">
                                <!-- Select to choose field to search by -->
                                <select id="search-dropdown" aria-label="Default select example" class="input-extension-right">
                                    <option value="0" selected>ID</option>
                                    <option value="1">Serial Number</option>
                                    <option value="2">Equipment Type</option>
                                    <option value="3">Make</option>
                                    <option value="4">User ID</option>
                                </select>
                            </div>
                        </div>
                        <div class="full-row">
                            <!-- Help text below search bar -->
                            <div id="search-help" class="form-text">Search for Equipment by ID, Serial Number, Equipment Type or Make.</div>
                        </div>
                    </div>
                    <!-- Only show button if user is an operator -->
                    <?php if ($_SESSION['deptName'] == "Operator") { ?>
                        <div class="third-row" id="delete-button-col">
                            <!-- Button trigger modal to remove an equipment. -->
                            <button onclick="removeEquipment()" type="button" style="float: right;" data-bs-toggle="modal" data-bs-target="#delete-equipment-modal">Remove Equipment</button>
                        </div>
                    <?php } else { ?>
                        <div class="third-row"></div>
                    <?php } ?>
                </div>

                &nbsp;
                <!-- This is to display the equipment table. -->
                <div>
                    <table id="equip-table" class="table1">
                        <thead>
                            <tr>
                                <th scope="col">Equipment ID</th>
                                <th scope="col">Serial Number</th>
                                <th scope="col">Equipment Type</th>
                                <th scope="col">Make</th>
                                <th scope="col">User ID</th>
                            </tr>
                        </thead>
                        <tbody id="myTable">
                            <?php if (mysqli_num_rows($table) > 0) {
                                //output data of each row from the SQL table.
                                while ($row = mysqli_fetch_array($table)) {
                                    echo "<tr class ='tableRow' onclick='rowClickModal2($row[0])'>
            <td class='Equip_ID'>$row[0]</td>
            <td class='Serial_Numb'>$row[1]</td>
            <td class='Equip_Type'>$row[2]</td>
            <td class='Equip_Make'>$row[3]</td>
            <td class='User_ID'>$row[4]</td>
			</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Add Equipment -->
                <div class="modal fade" id="add-equipment-modal" tabindex="-1">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the hardware database -->
                        <form id="new-equipment-form" name="new-equipment-form" action="equipment-page.php" method="POST" class="needs-validation" onsubmit="return checkNewEquipmentForm();">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="create-equipment">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Equipment</h5>
                                    <!-- This is a close button on the top right. -->
                                    <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <div class="wrapper">
                                        <div class="half-row">
                                            <label for="serialNo" class="form-label">Serial Number</label>
                                            <input type="number" max="99999999999999999999" class="form-control" id="serialNo" name="serialNo" maxlength="20" required>
                                        </div>

                                        <div class="half-row">
                                            <label for="device" class="form-label">Equipment Type</label>
                                            <input type="text" class="form-control" id="device" name="device" maxlength="63" required>
                                        </div>

                                        <div class="half-row">
                                            <label for="make" class="form-label">Make</label>
                                            <input type="text" class="form-control" id="make" name="make" maxlength="63" required>
                                        </div>

                                        <!-- Dropdown list of all personel to assign to the equipment. -->
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
                                    <button type="submit" class="btn btn-primary">Add Equipment</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Delete Equipment -->
                <div class="modal fade" id="delete-equipment-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the equipment database -->
                        <form action="equipment-page.php" method="POST" class="needs-validation" onsubmit="return checkRemoveEquipmentForm();">
                            <!-- Invisible field used for post back to same page -->
                            <input type="hidden" name="form_type" value="delete-equipment">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Equipment</h5>
                                    <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <form id="delete-equipment-form" name="delete-equipment-form" class="needs-validation">
                                        <div class="wrapper">
                                            <div class="half-row">
                                                <label for="removeID" class="form-label">Equipment ID</label>
                                                <input list="equip-list" max="99999999999999999999" type="number" class="form-control" id="removeID" name="removeID" required>
                                                <datalist id="equip-list">
                                                <?php foreach ($hardwares as $hardwares) { ?>
                                                    <option value=<?= $hardwares->ID ?>>ID: <?= $hardwares->ID ?>, <?= $hardwares->SerialNo ?>, <?= $hardwares->Device ?></option>
                                                <?php } ?>
                                            </datalist>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <hr>
                                <div class="modal-footer">
                                    <button onclick="closeModals()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Delete Equipment</button>
                                </div>
                            </div>
                    </div>
                </div>


                <!-- Modal to view equipment info -->
                <div class="modal fade" id="infoModal" tabindex="-1" data-row="">
                    <div>
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
    <script src="js/equipment-page.js"></script>


    <?php
    include("footer.php");
    ?>