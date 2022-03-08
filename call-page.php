<?php
include("header.php");

include_once './classes.php';
include_once './get-functions.php';


// Get all data needed for call table using get-functions.php
$personnels = getPersonnel();
$tickets = getTickets();
$allTickets = getAllTickets();
?>

<!-- calling external stylesheet -->
<link rel="stylesheet" href="css/call-page.css">

<!-- if statement to check User authorisation for accessing call page
    Only Operators can view the call page -->
<?php if ($_SESSION['deptName'] == "Operator") { ?>


    <title>Call Page</title>


    </head>

    <body>
        <div class="container-fluid">
            <!-- code to include the navbar on the call page, this will also allow the call page to be accessed via the navbar-->
            <?php
            include("navbar.php");
            ?>
            <main>

                <!-- process of creating a connection to the database to access data within-->
                <?php
                //credentials to enter Database
                $servername = "localhost";
                $username = "team008";
                $password = "dbnkKF2ykC";
                $dbname = "team008";
                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                //sql statement to get all data from PhoneCall table in database
                $sql = "SELECT CallTicket.CallID, PhoneCall.CallerID, CallTicket.TicketID, PhoneCall.CallTime, CallTicket.Reason
                FROM PhoneCall LEFT JOIN CallTicket
                on PhoneCall.ID = CallTicket.CallID ORDER BY PhoneCall.CallTime DESC;";
                $table = mysqli_query($conn, $sql);

                // close connection
                $conn->close();


                // When request is made to add new data to the database. i.e. adding a PhoneCall
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Create connection
                    $TicketIDs = explode(",", $_POST['ticketID']);
                    $x = 0;
                    $validTicketIDs = [];
                    $flag = 0;
                    foreach ($allTickets as $allTicket) {
                        array_push($validTicketIDs, $allTicket->ID);
                    }
                    while ($x < count($TicketIDs)) {
                        if (in_array($TicketIDs[$x], $validTicketIDs)) {
                            $flag = 1;
                        } else {
                            $flag = 0;
                        }
                        $x++;
                    }

                    if ($flag == 1) {
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $callerID = $conn->real_escape_string($_POST['callerID']);
                        $ticketID = $conn->real_escape_string($_POST['ticketID']);
                        $reason = $conn->real_escape_string($_POST['reason']);

                        if (count($TicketIDs) == 1) {
                            // Insert call Details into phoneCall table
                            $sql = "INSERT INTO PhoneCall (CallerID) 
                        VALUES ('" . $callerID . "')";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {
                                $sql1 = "INSERT INTO CallTicket (CallID, ticketID, Reason) 
                        VALUES ((SELECT PhoneCall.ID FROM PhoneCall ORDER BY PhoneCall.ID DESC LIMIT 1) , '" . $ticketID . "', '"
                                    . $reason . "')";

                                if ($conn->query($sql1) === TRUE) {
                                    // Refresh the page so the user can see the added call in call table
                                    echo "<meta http-equiv='refresh' content = '0'>";
                                }
                            } else {
                                // else Display error
                                echo "Error: " . $sql . "<br>" . $conn->error . "<br>Error: " . $sql1 . "<br>" . $conn->error;
                            }
                        } else {
                            $i = 0;

                            // Insert call Details into phoneCall table
                            $sql = "INSERT INTO PhoneCall (CallerID) 
                    VALUES ('" . $callerID . "')";

                            // If query was successful
                            if ($conn->query($sql) === TRUE) {

                                while ($i < count($TicketIDs)) {
                                    // insert call details into CallTicket Table
                                    $sql1 = "INSERT INTO CallTicket (CallID, ticketID, Reason) 
                    VALUES ((SELECT PhoneCall.ID FROM PhoneCall ORDER BY PhoneCall.ID DESC LIMIT 1) , '"
                                        . $TicketIDs[$i] . "', '"
                                        . $reason . "')";

                                    if ($conn->query($sql1) === TRUE) {
                                        // Refresh the page so the user can see the added call in call table
                                        echo "<meta http-equiv='refresh' content = '0'>";
                                    } else {
                                        // else Display error
                                        echo "Error: " . $sql1 . "<br>" . $conn->error;
                                    }
                                    $i++;
                                }
                            } else {
                                // else Display error
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }


                        // Close connection to database
                        $conn->close();
                    } else {
                        // Display error then refresh the page
                        echo "<Script>alertBanner('Error: Invalid TicketID.')</Script>";
                        echo "<Script>if ( window.history.replaceState ) {
                            window.history.replaceState( null, null, window.location.href );
                        }</Script>";
                    }
                }


                ?>

                <div class="alertContainer"></div>
                <center style="padding-top: 40px; font-size: xx-large; font-weight: bolder;">Calls</center>

                <div class="wrapper">
                    <div class="third-row">
                        <!-- Button to add a new call. this will trigger a modal -->
                        <button onclick="addMod()" type="button" data-bs-toggle="modal" data-bs-target="#add-modal">Add Call info</button>
                    </div>
                    <!-- Search bar code with select options -->
                    <div class="third-row">
                        <div class="wrapper">
                            <div class="input-extended input-extended-left">
                                <span class="input-extension-left" id="search-addon"><i class="fas fa-search"></i></span>
                                <!-- Searchbar input. will initiate search on keyup event-->
                                <input autocomplete="off" onkeyup="callSearch();" id="call-search" type="text" class="form-control" placeholder="Search..." aria-label="search" aria-describedby="search-addon">
                                <!-- Select options to choose field to search by -->
                                <select class="input-extension-right" id="search-dropdown" aria-label="Default select example">
                                    <option value="0" selected>ID</option>
                                    <option value="1">Caller ID</option>
                                    <option value="2">Ticket ID</option>
                                </select>
                            </div>
                        </div>
                        <div class="full-row">
                            <!-- Help text below search bar -->
                            <div id="search-help" class="form-text">Search for Calls by ID or Caller ID</div>
                        </div>
                    </div>

                </div>


                &nbsp;
                <!-- The call table -->
                <table class="call-table">
                    <thead style="position: sticky; top: 0;">
                        <tr>
                            <th scope="col">Call ID</th>
                            <th scope="col">Caller ID</th>
                            <th scope="col">Ticket ID</th>
                            <th scope="col">Call Time</th>
                            <th scope="col">Reason</th>
                        </tr>
                    </thead>
                    <!-- Use php to sift through the results of the SQL query result and place results into appropriate table positions -->
                    <tbody id="myTable">
                        <?php if (mysqli_num_rows($table) > 0) {
                            //output data of each row
                            while ($row = mysqli_fetch_array($table)) {
                                echo "<tr class ='tableRow' onclick='rowClickModal($row[0])'>
                                    <td class='call-id'>$row[0]</td>
                                    <td class='caller-id'>$row[1]</td>
                                    <td class='ticket-id'>$row[2]</td>
                                    <td class='call-time'>$row[3]</td>
                                    <td class='reason'>$row[4]</td>
                                    </tr>";
                            }
                        }
                        ?>

                    </tbody>
                </table>

                <!-- Modal To Add call. This will be hidden upon viewing the page and will be displayed when the "add call" button is clicked-->
                <div class="modal fade" id="add-modal" tabindex="-1">
                    <div class="modal-dialog">
                        <!-- Form that contains fields to input to the call into database -->
                        <form action="call-page.php" id="new-call-form" name="new-call-form" method="POST" class="needs-validation" onsubmit="return checkNewCallForm();">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="modal-title">Add Call</h2>
                                    <!-- simple close button in top right corner of modal -->
                                    <button type="button" onclick="closeModals()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                                <hr>
                                <div class="modal-body">
                                    <div class="wrapper">
                                        <div class="half-row">
                                            <label for="callName" class="form-label">Caller ID</label>
                                            <select name="callerID" id="callerID" class="form-select">
                                                <!-- Give user a select option of all personnel IDs from database  -->
                                                <?php foreach ($personnels as $personnel) { ?>
                                                    <option value=<?= $personnel->ID ?>><?= $personnel->ID ?>: <?= $personnel->FullName ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>



                                        <div class="half-row">
                                            <!-- input field for ticket ID -->
                                            <label for="ticketID" class="form-label">Ticket ID</label>
                                            <input list="brow" class="form-control" id="ticketID" name="ticketID">
                                            <datalist id="brow">
                                                <?php foreach ($allTickets as $allTicket) { ?>
                                                    <option value=<?= $allTicket->ID ?>><?= $allTicket->TicketDescription ?></option>
                                                <?php } ?>
                                            </datalist>
                                            <label id="search-help">If multiple problems separate ticket IDs using a comma<br>(eg: "1,4")</label>
                                        </div>
                                    </div>


                                    <label for="reason" class="form-label">Reason</label>
                                    <!-- input fieeld for call reason -->
                                    <input type="text" maxlength = "255" class="form-control" name="reason" id="reason" required>
                                </div>
                                <div class="modal-footer">
                                    <!-- includes the cancel button which will close the modal
                                        and the add call button which will proceed to add call data to database -->
                                    <button onclick="closeModals()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <div style="float:right;">
                                        <button type="submit" class="btn btn-primary">Add Call</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>



                <!-- Modal to view Call info when row is clicked on call table -->
                <div class="modal fade" id="infoModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title" id="CallDetailsID"></h2>
                                <button type="button" onclick="closeRowClickModal()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                            </div>
                            <hr>
                            <!-- labels will be filled with output of sql query with relevant Call information -->
                            <label class="callerDetails1" id="CallerDetailsID" required disabled>""</label>
                            <label class="callerDetails2" id="CallerDetails" required disabled>""</label>
                            <label class="callerDetails3" id="PhoneNo" name="PhoneNo" required disabled>""</label>
                            <!-- labels will be filled with output of sql query with relevant Call information -->
                            <div class="wrapper" id ="rowClickModalContent"> 
                                

                            </div>
                            <hr>
                            <div class="modal-footer">
                                <button onclick="closeRowClickModal()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>


            </main>
        </div>

        <!-- The JavaScrpt pages that are accessed externally -->
        <script src="js/modal.js"></script>
        <script src="js/call-page.js"></script>

        <?php
        include("footer.php");
        ?>

    <?php } else {
    header("Location: ./ticket-list.php");
} ?>