<?php
include("header.php");
?>

<link rel="stylesheet" href="css/archive-page.css">
<title>Archive Page</title>


</head>

<body>
  <div class="container-fluid">
    <!-- This will include this page as an option in the navbar, and also add the navbar to the page. -->
    <?php
    include("navbar.php");
    ?>
    <main>

      <?php
      // credentials for database access
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

      // query for all the data needed for the archive table.

      $sql = 'SELECT 
              Ticket.ID,
              Ticket.TicketDescription AS "Problem",
              FinalSolutionID AS "Solution ID",
              (SELECT Explanation From Solution Where Solution.ID = Ticket.FinalSolutionID) AS "Solution",
              ProblemType.Problem,
              CreatedTimestamp AS "Created Date", 
              ResolvedTimestamp AS "Resolved Date" 
              FROM Ticket LEFT JOIN ProblemType 
              ON Ticket.TypeID = ProblemType.ID 
              WHERE FinalSolutionID != "NULL" AND Ticket.TicketState="RESOLVED";';
      $table = mysqli_query($conn, $sql);
      ?>

      <center style="padding-top: 40px; padding-bottom:10px; font-size: xx-large; font-weight: bolder;">Archive</center>



      <div class="wrapper" style="padding-bottom:20px;">
        <div class="third-row"></div>
        <div class="third-row">
          <div class="wrapper">
            <div class="searchbar">
              <div class="input-extended input-extended-left">
                <span class="input-extension-left" id="search-addon"><i class="fas fa-search"></i></span>
                <!-- Search input -->
                <input autocomplete="off" onkeyup="archiveSearch()" id="archive-search" type="text" class="form-control" placeholder="Search..." aria-label="search" aria-describedby="search-addon">
                <!-- Select to choose field to search by -->
                <select class="input-extension-right" id="search-dropdown" aria-label="Default select example">
                  <option value="0" selected>Ticket ID</option>
                  <option value="1">Problem</option>
                  <option value="3">Solution (ID)</option>
                  <option value="2">Problem Type</option>
                  <option value="5">Created Date</option>
                  <option value="6">Resolved Date</option>
                </select>
              </div>
            </div>
            <div class="full-row">
              <!-- Help text below search bar -->
              <div id="search-help" class="form-text">Search for Resolved Tickets</div>
            </div>
          </div>
        </div>
        <div class="third-row"></div>

      </div>

      <table class="table1">
        <thead class="table-dark" style="position: sticky; top: 0;">
          <tr>
            <th scope="col">Ticket ID</th>
            <th scope="col">Problem</th>
            <th scope="col">Solution</th>
            <th scope="col">Problem Type</th>
            <th scope="col">Created Date</th>
            <th scope="col">Resolved Date</th>
          </tr>
        </thead>
        <tbody id="myTable">
          <?php if (mysqli_num_rows($table) > 0) {
            //output data of each row
            while ($row = mysqli_fetch_array($table)) {
              $createDate=substr($row[5],0,-8);
              $resolveDate=substr($row[6],0,-8);
              echo "<tr class ='tableRow' onclick='rowClickArchiveModal($row[0])'>
            <td class = 'TIC-ID'>$row[0]</td>
            <td class = 'PROB'>$row[1]</td>
            <td class = 'SOL'>#$row[2] $row[3]</td>
            <td class = 'PTYPE'>$row[4]</td>
            <td class = 'CREATE-DATE'>$createDate</td>
            <td class = 'RESOLVE-DATE'>$resolveDate</td>
			 </tr>";
            }
          }
          ?>
        </tbody>
      </table>


      <!-- Modal to view Archive info -->
      <div class="modal fade" id="infoModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title">Archive Info</h2>
              <button type="button" onclick="closeRowClickModal()" class="modal-close button-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <br>
            <div class="wrapper" id="archiveProblemSolution">
              <div class="half-row">
                <label class="archiveLabel" id=archiveProblem>""</label>
              </div>
              <div class="half-row">
                <label class="archiveLabel" id=archiveSolution>""</label>
              </div>
            </div>
            
            <div class="wrapper" id="rowClickModalContent">
              

              <!-- Textbox for reporter details Type -->
              <div class="half-row">
                <label>Reporter Details</label>
                <input class="archiveText"  id="archiveReporter" type="text" placeholder="" disabled>
              </div>
              <div class="half-row">
                <label>Reporter Phone Number</label>
                <input class="archiveText"  id="archiveReporterPhone" type="text" placeholder="" disabled>
              </div>
              <!-- Textarea for operator details -->
              <div class="half-row">
                <label>Operator Details</label>
                <input class="archiveText" id="archiveOperator" type="text" placeholder="" disabled>
              </div>
              <div class="half-row">
                <label>Operator Phone Number</label>
                <input class="archiveText" id="archiveOperatorPhone" type="text" placeholder="" disabled>
              </div>
              <!-- Textarea for Specialist details -->
              <div class="half-row">
                <label id ="archiveSpecialistLabel">Specialist Details</label>
                <input class="archiveText" id="archiveSpecialist" type="text" placeholder="" disabled>
              </div>
              <div class="half-row">
                <label id ="archiveSpecialistPhoneLabel">Specialist Phone Number</label>
                <input class="archiveText" id="archiveSpecialistPhone" type="text" placeholder="" disabled>
              </div>

            </div>

            <hr>
            <div class="modal-footer">
              <button onclick="closeRowClickModal()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>

      <!-- JS -->
      <script src="js/archive-page.js"></script>

      <?php
      include("footer.php");
      ?>