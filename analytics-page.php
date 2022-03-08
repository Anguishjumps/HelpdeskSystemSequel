
<!DOCTYPE html>
<html lang="en">
<?php
include("header.php");
?>

<?php if($_SESSION['deptName'] != 'Operator') header("Location: ./ticket-list.php"); ?>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Analytics Page</title>
  <script src='https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js'></script>
  </head>
  <body>
  <?php 
    $con = new mysqli("localhost","team008","dbnkKF2ykC","team008");

    //Total number of tickets
    $query = $con->query("
      SELECT 
      TicketState as states,
      Count(ID) as numberOfTicketStates
      FROM Ticket
      GROUP BY TicketState
    ");

    foreach($query as $data)
    {
      $states[] = $data['states'];
      $ticketCount[] = $data['numberOfTicketStates'];
    }
    
    //Problem types and count
    $query = $con->query("
    SELECT ProblemType.Problem as problemTypes, COUNT(Tickets.TypeID) as numberOfProblems 
    FROM ProblemType 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TypeID IS NOT NULL ) 
    Tickets ON ProblemType.ID = Tickets.TypeID 
    GROUP BY ProblemType.Problem
    ");

    foreach($query as $data)
    {
      $problems[] = $data['problemTypes'];
      $problemCount[] = $data['numberOfProblems'];
    }

    //Reporters and reported tickets
    $query = $con->query("
    SELECT Personnel.FullName as reporter, COUNT(Tickets.ReporterID) as numberOfReporters 
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE ReporterID IS NOT NULL ) 
    Tickets ON Personnel.ID = Tickets.ReporterID 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $reporter[] = $data['reporter'];
      $reporterCount[] = $data['numberOfReporters'];
    }

    //Specialist and total assigned tickets
    $query = $con->query("
    SELECT Personnel.FullName as specialist, COUNT(Tickets.AssignedSpecialistID) as numberOfSpecialists
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE AssignedSpecialistID IS NOT NULL ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job 
    LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialist[] = $data['specialist'];
      $specialistCount[] = $data['numberOfSpecialists'];
    }

    //Software Issue Count
    $query = $con->query("
    SELECT Software.SoftwareName as Software, COUNT(Tickets.SoftwareID) as numberOfIssues FROM Software LEFT JOIN ( SELECT * FROM Ticket ) Tickets ON Software.ID = Tickets.SoftwareID GROUP BY Software.SoftwareName    ");

    foreach($query as $data)
    {
      if (strpos($data['Software'], ' ') !== false) {
        $data['Software'] = explode(" ",$data['Software']);
    }
      $Software[] = $data['Software'];
      $issueCount[] = $data['numberOfIssues'];
    }


    //Specialist tickets in TODO
    $query = $con->query("
    SELECT Personnel.FullName as specialistInTODO, COUNT(Tickets.AssignedSpecialistID) as specialistInTODOCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"TODO\" ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInTODO[] = $data['specialistInTODO'];
      $specialistInTODOCount[] = $data['specialistInTODOCount'];
    }

    
    //Specialists and Resolved Tickets
    $query = $con->query("
    SELECT Personnel.FullName as specialistInResolved, COUNT(Tickets.AssignedSpecialistID) as specialistInResolvedCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInResolved[] = $data['specialistInResolved'];
      $specialistInResolvedCount[] = $data['specialistInResolvedCount'];
    }

    //?Specialists and INPROGRESS Tickets
    $query = $con->query("
    SELECT Personnel.FullName as specialistInINPROGRESS, COUNT(Tickets.AssignedSpecialistID) as specialistInINPROGRESSCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INPROGRESS\" ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInINPROGRESS[] = $data['specialistInINPROGRESS'];
      $specialistInINPROGRESSCount[] = $data['specialistInINPROGRESSCount'];
    }

    //Specialists and INREVIEW Tickets
    $query = $con->query("
    SELECT Personnel.FullName as specialistInREVIEW, COUNT(Tickets.AssignedSpecialistID) as specialistInREVIEWCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INREVIEW\" ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInREVIEW[] = $data['specialistInREVIEW'];
      $specialistInREVIEWCount[] = $data['specialistInREVIEWCount'];
    }

    //Average Ticket Resolution Time
    $query = $con->query("
    SELECT AVG(DATEDIFF(ResolvedTimestamp,CreatedTimestamp)) as avgResolutionTime
    FROM Ticket 
    WHERE ResolvedTimeStamp IS NOT NULL
    ");

    foreach($query as $data)
    {
      $avgResolutionTime = $data['avgResolutionTime'];
    }

    //Hardware Issues Count
    $query = $con->query("
    SELECT Hardware.Device as Hardware, COUNT(Tickets.HardwareID) as numberOfHardwareIssues
    FROM Hardware LEFT JOIN 
      ( SELECT * FROM Ticket WHERE HardwareID IS NOT NULL ) 
    Tickets ON Hardware.ID = Tickets.HardwareID 
    GROUP BY Hardware.Device
    ");

    foreach($query as $data)
    {
      $Hardware[] = $data['Hardware'];
      $numberOfHardwareIssues[] = $data['numberOfHardwareIssues'];
    }

    //Days of the month and number of tickets 
    $query = $con->query("
    SELECT DAY(CreatedTimestamp) AS Day, COUNT(TypeID) AS Tickets from Ticket group by Day
    ");

    foreach($query as $data)
    {
      $Day[] = $data['Day'];
      $Tickets[] = $data['Tickets'];
    }

    //Table for software, hardware, make and issue
    $query = "SELECT Hardware.Device, Hardware.Make, Software.SoftwareName, Ticket.TicketDescription, Ticket.TicketState
    FROM Hardware, Software, Ticket 
    WHERE Ticket.HardwareID=Hardware.ID 
    AND Ticket.SoftwareID=Software.ID";

    $query2 = "SELECT Departments.DeptName, COUNT(Ticket.ReporterID) 
    FROM Personnel, Ticket, Departments 
    WHERE Personnel.ID = Ticket.ReporterID AND Departments.ID = Personnel.Dept 
    GROUP BY Departments.DeptName";

    $conn = mysqli_connect("localhost","team008","dbnkKF2ykC","team008");
    $table = mysqli_query($conn, $query);
    $reportertable = mysqli_query($conn, $query2);

    //Longest unresolved ticket
    $query = $con->query("
    SELECT ID as ticketID, MAX(DATEDIFF(CURRENT_TIMESTAMP,CreatedTimestamp)) as unresolvedDays FROM Ticket WHERE ResolvedTimeStamp IS NULL
    ");

    foreach($query as $data)
    {
      $ticketID = $data['ticketID'];
      $unresolvedDays = $data['unresolvedDays'];
    }

    //Average unresolved ticket time
    $query = $con->query("
    SELECT AVG(DATEDIFF(CURRENT_TIMESTAMP,CreatedTimestamp)) as avgOpenTime FROM Ticket WHERE ResolvedTimeStamp IS NULL
    ");

    foreach($query as $data)
    {
      $avgOpenTime = $data['avgOpenTime'];
    }

    //Person and number of most unesolved tickets
    $query = $con->query("
    SELECT Personnel.FullName as mostUnresolved, COUNT(Ticket.ID) as numberOfUnresolved FROM Personnel, Ticket 
    WHERE Ticket.TicketState!=\"RESOLVED\" AND Personnel.ID=Ticket.AssignedSpecialistID 
    GROUP BY Personnel.FullName 
    ORDER BY COUNT(Ticket.ID) DESC LIMIT 1
    ");

    foreach($query as $data)
    {
      $mostUnresolved = $data['mostUnresolved'];
      $numberOfUnresolved = $data['numberOfUnresolved'];
    }

    //Operators and ticket counts
    $query = $con->query("
    SELECT Personnel.FullName as OperatorName, COUNT(Tickets.OperatorID) as issuesReportedd
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE OperatorID IS NOT NULL ) 
    Tickets ON Personnel.ID = Tickets.OperatorID 
    WHERE Personnel.Job = \"Helpdesk Operator\" 
    GROUP BY Personnel.FullName   ");

    foreach($query as $data)
    {
      $OperatorName[] = $data['OperatorName'];
      $issuesReportedd[] = $data['issuesReportedd'];
    }

    //Unassigned Tickets
    $query = $con->query("
    SELECT COUNT(Ticket.ID) as ticketUnassigned FROM Ticket WHERE Ticket.AssignedSpecialistID IS NULL ");

    foreach($query as $data)
    {
      $numberOfUnassignedTickets = $data['ticketUnassigned'];
    }

    

    //External specialists and Resolved Tickets
    $query = $con->query("
    SELECT Personnel.FullName as extSpecialists, Personnel.FullName as Name, COUNT(Tickets.AssignedSpecialistID) as extSpecialistNotResovledCount 
    FROM Personnel LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState != \"RESOLVED\" ) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%External%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $extSpecialists[] = $data['extSpecialists'];
      $extSpecialistNotResovledCount[] = $data['extSpecialistNotResovledCount'];
      $extFullName = $data['Name'];
    }
    

     //External Specialists and Unresolved Tickets
     $query = $con->query("
     SELECT Personnel.FullName as extSpecialists, COUNT(Tickets.AssignedSpecialistID) as extSpecialistResovledCount 
     FROM Personnel LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" ) 
     Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
     WHERE Personnel.Job LIKE \"%External%\" 
     GROUP BY Personnel.FullName

     ");
 
     foreach($query as $data)
     {
       $extSpecialistsResolved[] = $data['extSpecialists'];
       $extSpecialistResovledCount[] = $data['extSpecialistResovledCount'];
     }

     //Dept with most unresolved tickets
     $query = $con->query("
     SELECT Departments.DeptName as dept, COUNT(Ticket.ReporterID) as problems FROM Personnel, Ticket, Departments 
     WHERE Personnel.ID = Ticket.ReporterID AND Departments.ID = Personnel.Dept AND Ticket.TicketState!='RESOLVED' 
     GROUP BY Departments.DeptName 
     ORDER BY COUNT(Ticket.ReporterID) DESC LIMIT 1

     ");
 
     foreach($query as $data)
     {
       $dept = $data['dept'];
       $mostOpenProb = $data['problems'];
     }
  ?>
  <div class="container-fluid">
    <div class="row">
      <?php
        include("navbar.php");
      ?>
      <main class="col-md-10 ml-sm-auto col-lg-10 pt-3 px-4">
        <center style="padding-top: 20px; font-size: xx-large; font-weight: bolder;"> Analytics </center>
        <div>
          <div style="width: 29%; float: left; padding-left: 5px; padding-top:30px; height: 100%;">
            <div style="border-style:solid; padding-bottom:10px; padding-top:20px;  border-radius:5px; background-color:#ffe6d4">
              <canvas id="myChart"></canvas>
              <center style="padding-top: 10px;"><b>Current Ticket States</b></center>
              <ul style="font-size:14px">
                <li id="trying"><?php echo "  Average resolution ticket time:<b> " . round((str_replace("\"","",json_encode($avgResolutionTime))),2) . "</b> days" ?></li>
                <li><?php echo "  Average unresolved ticket duration:<b> " . round((str_replace("\"","",json_encode($avgOpenTime))),2) . "</b> days" ?></li>
                <li><?php echo "  Oldest unresolved ticket:<b> " . round((str_replace("\"","",json_encode($unresolvedDays))),2) . "</b> days" ?></li>
                <li><?php echo "<b>" . str_replace("\"","",json_encode($mostUnresolved)) . "</b> has the most unresolved tickets:<b> " . round((str_replace("\"","",json_encode($numberOfUnresolved))),0) . "</b>" ?></li>
                <li><?php echo "Number of unassigned tickets: <b>" . round((str_replace("\"","",json_encode($numberOfUnassignedTickets))),0) . "</b>" ?></li>
                <li><?php echo "<b>" . str_replace("\"","",json_encode($dept)) . "</b> dept has the most unresolved tickets:<b> " . round((str_replace("\"","",json_encode($mostOpenProb))),0) . "</b>" ?></li>

              </ul>
            </div>
          </div>
          <div style="float: right; margin-top: 30px; padding-bottom:19px; width: 69%; height: 100%; background-color:pink; border-style:solid; border-radius:5px; background-color:#ffd5b5">
              <center>
              <button id="ButtoN" type="button" style="margin-left:3px" onclick="revert()" disabled> Revert </button>
              <button id="lastMonth" type="button" style="margin-left:3px" onclick="lastMonth()"> Last Month </button>
              <button id="lastWeek" type="button" style="margin-left:3px" onclick="lastWeek()" > Last Week </button>
              </center>
              <br>
                <div style="float:right; width:48%;">
                  <div style="width: 99%; float:top; border-style:inset; border-radius:5px; background-color:white;">
                    <u id="TicketDistributionHeading"><center>Ticket Distribution/Specialist</center></u>
                    <div style="height:90%;  margin-top:15px">
                    <canvas id="myChart6"></canvas>
                    </div>
                  </div>
                  <div style="width: 99%; float:bottom; border-style:inset; border-radius:5px; background-color:white;">
                    <u id="ExternalHeading"><center>Ticket Distribution/ External Specialist</center></u>
                    <div style="height:90%;  margin-top:15px">
                    <canvas id="myChart10"></canvas>
                    </div>
                  </div>
                </div>
                <div style="float: left; width:48%;">
                  <div style="width: 99%; float:top; border-style:inset; border-radius:5px; background-color:white">
                    <u id="AssignedTicketsHeading"><center>Assigned Tickets/Specialist</center></u>
                    <div style="height:90%;  margin-top:15px">
                    <canvas id="myChart4"></canvas>
                    </div>
                  </div>
                  <div style="width: 99%; float:bottom; border-style:inset; border-radius:5px; background-color:white">
                    <u id="ProblemTypeHeading"><center>Tickets/Problem Type</center></u>
                    <div style="height:90%;  margin-top:15px">
                    <canvas id="myChart2"></canvas>
                    </div>
                </div>

            </div>
          </div>
          <div>
            <div style="float: right; padding-top: 30px; width: 69%; padding-bottom:10px">
              <div style="border-style:solid; padding:20px; height: 90%; border-radius:5px; background-color:#ffd5b5">
                <div float="top">
                  <div style="width: 100%; border-style:inset; border-radius:5px; float:top; background-color: white;">
                    
                  <u id="SoftwareHeading"><center>Tickets/Software</center></u>
                  <div style="height:90%;  margin-top:15px">
                        <canvas id="myChart5"></canvas>
                  </div>
                  </div>
                  </div>
                  <div style="float:bottom;">         
                  <br><div>
                    <center><u><b>Software Issues Encountered and Their Respective Devices and Models</b></u></center>
                  </div>
                    <div style="overflow:auto; height:220px; width:100%; margin-top:10px;">
                      <table style="border: 3px; background-color:orange; width:100%;">
                        <thead  style="position: sticky; top: 0; color: white; background-color:orange">
                          <tr>
                            <th scope="col">Device</th>
                            <th scope="col">Make</th>
                            <th scope="col">Software</th>
                            <th scope="col">Issue</th>
                          </tr>
                        </thead>
                        <tbody id="myTable">
                        <?php if (mysqli_num_rows($table) > 0) {
                        //output data of each row
                          while ($row = mysqli_fetch_array($table)) {
                          if ($row[4] == "RESOLVED"){
                            echo "<tr>
                            <td style=\"background-color:#b8fca2\">$row[0]</td>
                            <td style=\"background-color:#b8fca2\">$row[1]</td>
                            <td style=\"background-color:#b8fca2\">$row[2]</td>
                            <td style=\"background-color:#b8fca2\">$row[3]</td>
                            </tr>";

                          } else {
                            echo "<tr>
                            <td>$row[0]</td>
                            <td>$row[1]</td>
                            <td>$row[2]</td>
                            <td>$row[3]</td>
                            </tr>";
                          }
                          }
                        }
                        ?>
                        </tbody>
                      </table>
                    </div>
                  </div>     
                </div>
              </div>
            <div style="width: 29%; float: left; padding-left: 5px; padding-top:30px">
              <div style="border-style:solid; padding:20px; height: 100%; border-radius:5px; background-color:#ffe6d4;">
                <div style=" width:95%; border-style:inset; border-radius:5px; background-color: white; margin-left:10px;">
                  <u id="MonthHeading"><center>Tickets/Day of the Month</center></u>
                    <div>
                      <canvas id="myChart8" style="background-color:white;"></canvas>
                    </div>
                </div>
                <div id="bottomInside" style="float:bottom; margin-left:10px; margin-right:10px">
                  <div style=" width:95%; border-style:inset; border-radius:5px; background-color: white; margin-left:10px; margin-top:10px">
                  <u id="OperatorHeading"><center>Calls/Operator</center></u>
                      <canvas id="myChart9" style="background-color:white;"></canvas>
                </div>
                  <div style=" width:100%; border-style:inset; border-radius:5px; background-color: white;   margin-top:10px">
                  <u id="HardwareHeading"><center>Tickets/Hardware</center></u>
                      <canvas id="myChart7"></canvas>
                  </div>
                   <div style="overflow:auto; height:162px; width:100%; margin-top:10px;">
                      <table style="border: 3px; background-color:orange; width:100%;">
                        <thead  style="position: sticky; top: 0; color: white; background-color:orange">
                          <tr>
                            <th scope="col">Department</th>
                            <th scope="col">Problems Reported</th>
                          </tr>
                        </thead>
                        <tbody id="myTable">
                        <?php if (mysqli_num_rows($reportertable) > 0) {
                        //output data of each row
                          while ($row = mysqli_fetch_array($reportertable)) {
                          if ($row[1] > 0){
                            echo "<tr>
                            <td>$row[0]</td>
                            <td><center>$row[1]<center></td>
                            </tr>";
                          }
                          }
                        } else {
                          echo "<tr>
                            <td> - </td>
                            <td> - </td>
                            </tr>";
                        }
                        ?>
                        </tbody>
                      </table>
                    </div>
                </div> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>


<script>

  //Construction of graphs and Pie Chart

  //Total tickets pie chart
  const labels = <?php echo json_encode($states) ?>;
  const data = {
    labels: labels,
    datasets: [{
      label: 'Ticket States',
      data: <?php echo json_encode($ticketCount) ?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
      ],
      borderColor: [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
      ],
      borderWidth: 1
    }]
  };

  //Problem Type count
  const labels2 = <?php echo json_encode($problems) ?>;
  const data2 = {
    labels: labels2,
    datasets: [{
      label: 'Problem Types',
      data: <?php echo json_encode($problemCount) ?>,
      backgroundColor: [
        'rgba(153, 102, 255, 0.2)',
      ],
      borderColor: [
 
        'rgb(153, 102, 255)',
      ],
      borderWidth: 1
    }]
  };

  //Reporter and reported tickets
  const labels3 = <?php echo json_encode($reporter) ?>;
  const data3 = {
    labels: labels3,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($reporterCount) ?>,
      backgroundColor: [
        'rgba(75, 135, 174, 0.2)',
      ],
      borderColor: [
        'rgb(75, 135, 174)',
      ],
      borderWidth: 1
    }]
  };

  //Specialist and assigned tickets
  const labels4 = <?php echo json_encode($specialist) ?>;
  const data4 = {
    labels: labels4,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($specialistCount) ?>,
      backgroundColor: [
        'rgba(255, 159, 64, 0.2)',
      ],
      borderColor: [
        'rgb(255, 159, 64)',
      ],
      borderWidth: 1
    },
  ]
  }
  
  //Software issues and count
  const labels5 = <?php echo json_encode($Software) ?>;
  const data5 = {
    labels: labels5,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($issueCount) ?>,
      backgroundColor: [

        'rgba(75, 192, 192, 0.2)',
      ],
      borderColor: [
        'rgb(75, 192, 192)',
      ],
      borderWidth: 1
    }]
  };

  //Assigned specialist ticket distribution
  const labels6 = <?php echo json_encode($specialist) ?>;
  const data6 = {
    labels: labels6,
    datasets: [{
      label:"To-Do",
      data: <?php echo json_encode($specialistInTODOCount) ?>,
      backgroundColor:"rgba(255,99, 132,0.2)",
      borderColor: "rgba(255, 99, 132)",
      borderWidth: 1
    },
    {
      label: 'In-Progress',
      data: <?php echo json_encode($specialistInINPROGRESSCount) ?>,
      backgroundColor: [
        'rgba(255, 205, 86, 0.2)',
      ],
      borderColor: [
        'rgb(201, 203, 207)'
      ],
      borderWidth: 1
    },
    {
      label:"In-Review",
      data: <?php echo json_encode($specialistInREVIEWCount) ?>,
      backgroundColor:"rgba(153, 102, 255, 0.2)",
      borderColor:"rgba(153, 102, 255)",
      borderWidth: 1
    },
    {
      label:"Resolved",
      data: <?php echo json_encode($specialistInResolvedCount) ?>,
      backgroundColor:"rgba(54, 162, 235, 0.2)",
      borderColor: "rgba(54, 162, 235)",
      borderWidth: 1
    }]
  }
  
  //Hardware issues count
  const labels7 = <?php echo json_encode($Hardware) ?>;
  const data7 = {
    labels: labels7,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($numberOfHardwareIssues) ?>,
      backgroundColor: [

        'rgba(38, 252, 95, 0.2)',
      ],
      borderColor: [
        'rgb(38, 252, 95)',
      ],
      borderWidth: 1
    }]
  };
  
  //Days of the month and tickets created
  const labels8 = <?php echo json_encode($Day) ?>;
  const data8 = {
    labels: labels8,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($Tickets) ?>,
      fill: true,
      backgroundColor: [

        'rgba(255, 99, 132, 0.2)',
      ],
      borderColor: [
        'rgb(255, 99, 132)',
      ],
      borderWidth: 1
    }]
  };


  //Operator and ticket count
  const labels9 = <?php echo json_encode($OperatorName) ?>;
  const data9 = {
    labels: labels9,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($issuesReportedd) ?>,
      backgroundColor: [

        'rgba(227, 216, 64, 0.2)',
      ],
      borderColor: [
        'rgb(227, 216, 64)',
      ],
      borderWidth: 1
    }]
  };

  //External Specialist Ticket Count
  const labels10 = <?php echo json_encode($extSpecialists) ?>;
  const data10 = {
    labels: labels10,
    datasets: [{
      label: 'Unresolved Tickets',
      data: <?php echo json_encode($extSpecialistNotResovledCount) ?>,
      backgroundColor: [

        'rgba(227, 216, 64, 0.2)',
      ],
      borderColor: [
        'rgb(227, 216, 64)',
      ],
      borderWidth: 1
    },{
      label:"Resolved",
      data: <?php echo json_encode($extSpecialistResovledCount) ?>,
      backgroundColor:"rgba(54, 162, 235, 0.2)",
      borderColor: "rgba(54, 162, 235)",
      borderWidth: 1
    }]
  };

  ;

  const config = {
    type: 'pie',
    data: data,
    options: {

    },
  }
  const config2 = {
    type: 'line',
    data: data2,
    options: {

    },
  }
  const config3 = {
    type: 'line',
    data: data3,
    options: {
        
    },
  }
  const config4 = {
    type: 'bar',
    data: data4,
    options: {

    },
  }
  const config5 = {
    type: 'bar',
    data: data5,
    options: {
      plugins: {
        tooltip: {
          callbacks: {
            title: (context) => {
              //console.log(context[0].label);
              return context[0].label.replaceAll(',',' ');
            }
          }
        }
      }

    }
  }
  const config6 = {
    type: 'bar',
    data: data6,
    options: {

    },
  }
  const config7 = {
    type: 'line',
    data: data7,
    options: {

    },
  }
  const config8 = {
    type: 'line',
    data: data8,
    options: {

    },
  }

  const config9 = {
    type: 'bar',
    data: data9,
    options: {

    },
  }

  const config10 = {
    type: 'bar',
    data: data10,
    options: {

    },
  }
  ;

  var myChart = new Chart(
    document.getElementById('myChart'),
    config
  );
  var myChart2 = new Chart(
    document.getElementById('myChart2'),
    config2
  );
  var myChart3 = new Chart(
    document.getElementById('myChart3'),
    config3
  );
  var myChart4 = new Chart(
    document.getElementById('myChart4'),
    config4
  );
  var myChart5 = new Chart(
    document.getElementById('myChart5'),
    config5
  );
  var myChart6 = new Chart(
    document.getElementById('myChart6'),
    config6
  );
  var myChart7 = new Chart(
    document.getElementById('myChart7'),
    config7
  );
  var myChart8 = new Chart(
    document.getElementById('myChart8'),
    config8
  );
  var myChart9 = new Chart(
    document.getElementById('myChart9'),
    config9
  );
  var myChart10 = new Chart(
    document.getElementById('myChart10'),
    config10
  );



//Function to revert back to complete data
function revert(){
  
  myChart.data.datasets[0].data = <?php echo json_encode($ticketCount) ?>;
  myChart2.data.datasets[0].data = <?php echo json_encode($problemCount) ?>;
  //myChart3.data.datasets[0].data = <?php echo json_encode($reporterCount)?>;
  myChart4.data.datasets[0].data = <?php echo json_encode($specialistCount) ?>;
  myChart5.data.datasets[0].data = <?php echo json_encode($issueCount) ?>;
  myChart6.data.datasets[0].data = <?php echo json_encode($specialistInTODOCount)?>;
  myChart6.data.datasets[1].data = <?php echo json_encode($specialistInINPROGRESSCount)?>;
  myChart6.data.datasets[2].data = <?php echo json_encode($specialistInREVIEWCount)?>;
  myChart6.data.datasets[3].data = <?php echo json_encode($specialistInResolvedCount)?>;
  myChart7.data.datasets[0].data = <?php echo json_encode($numberOfHardwareIssues) ?>;
  myChart9.data.datasets[0].data = <?php echo json_encode($issuesReportedd)?>;
  myChart10.data.datasets[0].data = <?php echo json_encode($extSpecialistNotResovledCount)?>;
  myChart10.data.datasets[1].data = <?php echo json_encode($extSpecialistResovledCount)?>;
  
  myChart.update();
  myChart2.update();
 // myChart3.update();
  myChart4.update();
  myChart5.update();
  myChart6.update();
  myChart7.update();
  myChart9.update();
  myChart10.update();

  document.getElementById("ButtoN").disabled = true;
  document.getElementById("lastMonth").disabled = false;
  document.getElementById("lastWeek").disabled = false;

  SoftwareHeading.innerHTML = "<center>Tickets/Software</center>"
  TicketDistributionHeading.innerHTML = "<center>Ticket Distribution/Specialist</center>"
  AssignedTicketsHeading.innerHTML = "<center>Assigned Tickets/Specialists</center>"
  ProblemTypeHeading.innerHTML = "<center>Tickets/Problem Type</center>"
  HardwareHeading.innerHTML = "<center>Tickets/Hardware</center>"
  OperatorHeading.innerHTML = "<center>Calls/Operator</center>"
  ExternalHeading.innerHTML = "<center>Ticket Distribution/External Specialist</center>" 
 
}

//Function to display data from the last week
function lastMonth(){
  <?php

  //Software Issues Last Month (Zero error handled)
  $query = $con->query("
  SELECT Software.SoftwareName as Software, COUNT(Tickets.SoftwareID) as softwareIssuesLastMonth
  FROM Software 
    LEFT JOIN ( SELECT * FROM Ticket WHERE SoftwareID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 30) 
  Tickets ON Software.ID = Tickets.SoftwareID 
  GROUP BY Software.SoftwareName
      ");
      foreach($query as $data)
      {
        $softwareIssuesLastMonth[] = $data['softwareIssuesLastMonth'];
      }


     
    //Problem Types Last Month (Zero Error Handled)
    $query = $con->query("
    SELECT ProblemType.Problem as problemTypes, COUNT(Tickets.TypeID) as numberOfProblems 
    FROM ProblemType 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TypeID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 30) 
    Tickets ON ProblemType.ID = Tickets.TypeID 
    GROUP BY ProblemType.Problem
    ");

    foreach($query as $data)
    {

      $problemCountLastMonth[] = $data['numberOfProblems'];
    }

    //Hardware Issues Last Month (Zero Error Handled)
    $query = $con->query("
    SELECT Hardware.Device as Hardware, COUNT(Tickets.HardwareID) as numberOfHardwareIssues
    FROM Hardware LEFT JOIN 
      ( SELECT * FROM Ticket WHERE HardwareID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 30) 
    Tickets ON Hardware.ID = Tickets.HardwareID 
    GROUP BY Hardware.Device
    ");

    foreach($query as $data)
    {
      $numberOfHardwareIssuesLastMonth[] = $data['numberOfHardwareIssues'];
    }


    //Specialist and the assigned tickets Last Month (Zero Error Handled)
    $query = $con->query("
    SELECT Personnel.FullName as specialist, COUNT(Tickets.AssignedSpecialistID) as numberOfSpecialists
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE AssignedSpecialistID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 30) 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job 
    LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");


    foreach($query as $data)
    {
      $specialistCountLastMonth[] = $data['numberOfSpecialists'];
    }

    //Reporters and the tickets Last Month (Zero Error Handled)
    $query = $con->query("
    SELECT Personnel.FullName, COUNT(Tickets.ReporterID) as numberOfReporters 
    FROM Personnel 
    LEFT JOIN 
    ( SELECT * FROM Ticket 
      WHERE ReporterID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30 ) 
    Tickets ON Personnel.ID = Tickets.ReporterID 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $reporterCountLastMonth[] = $data['numberOfReporters'];
    }

    //Issues reported last month (Zero Error Handled)
    $query = $con->query("
    SELECT Personnel.FullName as OperatorName, COUNT(Tickets.OperatorID) as issuesReportedd
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE ReporterID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 30  ) 
    Tickets ON Personnel.ID = Tickets.OperatorID 
    WHERE Personnel.Job = \"Helpdesk Operator\" 
    GROUP BY Personnel.FullName   ");

    foreach($query as $data)
    {
      $OperatorName[] = $data['OperatorName'];
      $issuesReporteddLastMonth[] = $data['issuesReportedd'];
    }

    //Specialist TODO Tickets in the Last Month
    $query = $con->query("
    SELECT Personnel.FullName as specialistInTODO, COUNT(Tickets.AssignedSpecialistID) as specialistInTODOCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"TODO\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInTODO[] = $data['specialistInTODO'];
      $specialistInTODOCountLastMonth[] = $data['specialistInTODOCount'];
    }

    //Specialist INPROGRESS Tickets in the Last Month
    $query = $con->query("
    SELECT Personnel.FullName as specialistInINPROGRESS, COUNT(Tickets.AssignedSpecialistID) as specialistInINPROGRESSCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INPROGRESS\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInINPROGRESS[] = $data['specialistInINPROGRESS'];
      $specialistInINPROGRESSCountLastMonth[] = $data['specialistInINPROGRESSCount'];
    }

    //Specialist INREVIEW Tickets in the Last Month
    $query = $con->query("
    SELECT Personnel.FullName as specialistInREVIEW, COUNT(Tickets.AssignedSpecialistID) as specialistInREVIEWCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INREVIEW\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInREVIEW[] = $data['specialistInREVIEW'];
      $specialistInREVIEWCountLastMonth[] = $data['specialistInREVIEWCount'];
    }

       //Specialist RESOLVED Tickets in the Last Month
       $query = $con->query("
       SELECT Personnel.FullName, COUNT(Tickets.AssignedSpecialistID) as specialistInResolvedCount
       FROM Personnel 
       LEFT JOIN 
         ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" AND DATEDIFF(NOW(), CreatedTimestamp) < 30 ) 
       Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
       WHERE Personnel.Job LIKE \"%Repairs%\" 
       GROUP BY Personnel.FullName
       ");
   
       foreach($query as $data)
       {
        
         $specialistInResolvedCountLastMonth[] = $data['specialistInResolvedCount'];
       }

       //External Specialist Resolved Tickets Last Month
       $query = $con->query("
       SELECT Personnel.FullName as extSpecialists, COUNT(Tickets.AssignedSpecialistID) as extSpecialistResovledCount 
       FROM Personnel LEFT JOIN 
        ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" AND DATEDIFF(NOW(), ResolvedTimestamp) < 30) 
       Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
       WHERE Personnel.Job LIKE \"%External%\" 
       GROUP BY Personnel.FullName
  
       ");
   
       foreach($query as $data)
       {
         
         $extSpecialistResovledCountLastMonth[] = $data['extSpecialistResovledCount'];
       }

       //External Specialist Not Resolved Tickets Last Month
       $query = $con->query("
       SELECT Personnel.FullName as extSpecialists, COUNT(Tickets.AssignedSpecialistID) as extSpecialistResovledCount 
       FROM Personnel LEFT JOIN 
        ( SELECT * FROM Ticket WHERE TicketState != \"RESOLVED\") 
       Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
       WHERE Personnel.Job LIKE \"%External%\" 
       GROUP BY Personnel.FullName
  
       ");
   
       foreach($query as $data)
       {
         
         $extSpecialistNotResovledCountLastMonth[] = $data['extSpecialistResovledCount'];
       }
?>

  //Updating chart values
  myChart2.data.datasets[0].data = <?php echo json_encode($problemCountLastMonth)?>;
  //myChart3.data.datasets[0].data = <?php echo json_encode($reporterCountLastMonth)?>;
  myChart4.data.datasets[0].data = <?php echo json_encode($specialistCountLastMonth)?>;
  myChart5.data.datasets[0].data = <?php echo json_encode($softwareIssuesLastMonth) ?>;
  myChart6.data.datasets[0].data = <?php echo json_encode($specialistInTODOCountLastMonth)?>;
  myChart6.data.datasets[1].data = <?php echo json_encode($specialistInINPROGRESSCountLastMonth)?>;
  myChart6.data.datasets[2].data = <?php echo json_encode($specialistInREVIEWCountLastMonth)?>;
  myChart6.data.datasets[3].data = <?php echo json_encode($specialistInResolvedCountLastMonth)?>;
  myChart7.data.datasets[0].data = <?php echo json_encode($numberOfHardwareIssuesLastMonth) ?>;
  myChart9.data.datasets[0].data = <?php echo json_encode($issuesReporteddLastMonth)?>;
  myChart10.data.datasets[0].data = <?php echo json_encode($extSpecialistNotResovledCountLastMonth)?>;
  myChart10.data.datasets[1].data = <?php echo json_encode($extSpecialistResovledCountLastMonth)?>;

  
  myChart2.update();
  //myChart3.update();
  myChart4.update();
  myChart5.update();
  myChart6.update();
  myChart7.update();
  myChart9.update();
  myChart10.update();

  //Setting buttons to be active/inactive
  document.getElementById('lastMonth').disabled = true;
  document.getElementById('ButtoN').disabled = false;
  document.getElementById('lastWeek').disabled = false;

  //Changing graph headings
  SoftwareHeading.innerHTML = "<center>Tickets/Software (in the last Month)</center>"
  TicketDistributionHeading.innerHTML = "<center>Ticket Distribution/Specialist (in the last month)</center>"
  AssignedTicketsHeading.innerHTML = "<center>Assigned Tickets/Specialists (in the last Month)</center>"
  ProblemTypeHeading.innerHTML = "<center>Tickets/Problem Types (in the last Month)</center>"
  HardwareHeading.innerHTML = "<center>Tickets/Hardware (in the last Month)</center>"
  OperatorHeading.innerHTML = "<center>Calls/Operator (in the last Month)</center>"
  ExternalHeading.innerHTML = "<center><center>Ticket Distribution/External Specialist (in the last Month)</center>"
}


//Function for displaying data from the last week
function lastWeek(){

<?php

  //Problem Type Count Last Week
  $query = $con->query("
    SELECT ProblemType.Problem as problemTypes, COUNT(Tickets.TypeID) as numberOfProblems 
    FROM ProblemType 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TypeID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 7) 
    Tickets ON ProblemType.ID = Tickets.TypeID 
    GROUP BY ProblemType.Problem
    ");

    foreach($query as $data)
    {
      $problems[] = $data['problemTypes'];
      $problemCountLastWeek[] = $data['numberOfProblems'];
    }

  
    //Reporter Count Last Week
    $query = $con->query("
    SELECT Personnel.FullName as reporter, COUNT(Tickets.ReporterID) as numberOfReporters 
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE ReporterID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7) 
    Tickets ON Personnel.ID = Tickets.ReporterID 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $reporter[] = $data['reporter'];
      $reporterCountLastWeek[] = $data['numberOfReporters'];
    }

        //Specialist and total assigned tickets Last Week
        $query = $con->query("
        SELECT Personnel.FullName as specialist, COUNT(Tickets.AssignedSpecialistID) as numberOfSpecialists
        FROM Personnel 
        LEFT JOIN 
          ( SELECT * FROM Ticket WHERE AssignedSpecialistID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 7)  
        Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
        WHERE Personnel.Job 
        LIKE \"%Repairs%\" 
        GROUP BY Personnel.FullName
        ");
    
        //Software Issue Count Last Week
        foreach($query as $data)
        {
          $specialist[] = $data['specialist'];
          $specialistCountLastWeek[] = $data['numberOfSpecialists'];
        }

        
        $query = $con->query("
      SELECT Software.SoftwareName as Software, COUNT(Tickets.SoftwareID) as numberOfIssuesLastWeek 
      FROM Software 
        LEFT JOIN ( SELECT * FROM Ticket WHERE SoftwareID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 7) 
      Tickets ON Software.ID = Tickets.SoftwareID 
      GROUP BY Software.SoftwareName    ");

    //Issue Count Last Week (Zero Error Handled)
    foreach($query as $data)
    {

      $issueCountLastWeek[] = $data['numberOfIssuesLastWeek'];

      
      $query = $con->query("
    SELECT Personnel.FullName as OperatorName, COUNT(Tickets.OperatorID) as issuesReportedd
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE ReporterID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7 ) 
    Tickets ON Personnel.ID = Tickets.OperatorID 
    WHERE Personnel.Job = \"Helpdesk Operator\" 
    GROUP BY Personnel.FullName   ");

    
    
    foreach($query as $data)
    {
      $OperatorName[] = $data['OperatorName'];
      $issuesReporteddLastWeek[] = $data['issuesReportedd'];
    }
    }


    //Hardware Issues Last Week (Zero Error Handled)
    $query = $con->query("
    SELECT Hardware.Device as Hardware, COUNT(Tickets.HardwareID) as numberOfHardwareIssues
    FROM Hardware LEFT JOIN 
      ( SELECT * FROM Ticket WHERE HardwareID IS NOT NULL AND DATEDIFF(NOW(), CreatedTimestamp) < 7 OR TicketState!=\"RESOLVED\" OR DATEDIFF(NOW(), ResolvedTimestamp) < 7) 
    Tickets ON Hardware.ID = Tickets.HardwareID 
    GROUP BY Hardware.Device
    ");

    
    foreach($query as $data)
    {
      $numberOfHardwareIssuesLastWeek[] = $data['numberOfHardwareIssues'];
    }

    //Specialist TODO Tickets in the Last Week
    $query = $con->query("
    SELECT Personnel.FullName as specialistInTODO, COUNT(Tickets.AssignedSpecialistID) as specialistInTODOCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"TODO\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    
    foreach($query as $data)
    {
      $specialistInTODO[] = $data['specialistInTODO'];
      $specialistInTODOCountLastWeek[] = $data['specialistInTODOCount'];
    }

    //Specialist INPROGRESS Tickets in the Last Week
    $query = $con->query("
    SELECT Personnel.FullName as specialistInINPROGRESS, COUNT(Tickets.AssignedSpecialistID) as specialistInINPROGRESSCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INPROGRESS\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    
    foreach($query as $data)
    {
      $specialistInINPROGRESS[] = $data['specialistInINPROGRESS'];
      $specialistInINPROGRESSCountLastWeek[] = $data['specialistInINPROGRESSCount'];
    }


    //Specialist INREVIEW Tickets in the Last Week
    $query = $con->query("
    SELECT Personnel.FullName as specialistInREVIEW, COUNT(Tickets.AssignedSpecialistID) as specialistInREVIEWCount
    FROM Personnel 
    LEFT JOIN 
      ( SELECT * FROM Ticket WHERE TicketState = \"INREVIEW\") 
    Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
    WHERE Personnel.Job LIKE \"%Repairs%\" 
    GROUP BY Personnel.FullName
    ");

    foreach($query as $data)
    {
      $specialistInREVIEW[] = $data['specialistInREVIEW'];
      $specialistInREVIEWCountLastWeek[] = $data['specialistInREVIEWCount'];
    }

      //Specialist RESOLVED Tickets in the Last Week
      $query = $con->query("
      SELECT Personnel.FullName, COUNT(Tickets.AssignedSpecialistID) as specialistInResolvedCount
      FROM Personnel 
      LEFT JOIN 
        ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" AND DATEDIFF(NOW(), CreatedTimestamp) < 7 ) 
      Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
      WHERE Personnel.Job LIKE \"%Repairs%\" 
      GROUP BY Personnel.FullName
      ");
  
      foreach($query as $data)
      {
       
        $specialistInResolvedCountLastWeek[] = $data['specialistInResolvedCount'];
      }


       //External Specialist Resolved Tickets Last Month
       $query = $con->query("
       SELECT Personnel.FullName as extSpecialists, COUNT(Tickets.AssignedSpecialistID) as extSpecialistResovledCount 
       FROM Personnel LEFT JOIN 
        ( SELECT * FROM Ticket WHERE TicketState = \"RESOLVED\" AND DATEDIFF(NOW(), CreatedTimestamp) < 7) 
       Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
       WHERE Personnel.Job LIKE \"%External%\" 
       GROUP BY Personnel.FullName
  
       ");
   
       foreach($query as $data)
       {
         
         $extSpecialistResovledCountLastWeek[] = $data['extSpecialistResovledCount'];
       }

       //External Specialist Not Resolved Tickets Last Week
       $query = $con->query("
       SELECT Personnel.FullName as extSpecialists, COUNT(Tickets.AssignedSpecialistID) as extSpecialistNotResovledCount 
       FROM Personnel LEFT JOIN 
        ( SELECT * FROM Ticket WHERE TicketState != \"RESOLVED\") 
       Tickets ON Personnel.ID = Tickets.AssignedSpecialistID 
       WHERE Personnel.Job LIKE \"%External%\" 
       GROUP BY Personnel.FullName
  
       ");
   
       foreach($query as $data)
       {
         
         $extSpecialistNotResovledCountLastWeek[] = $data['extSpecialistNotResovledCount'];
       }


?>
myChart2.data.datasets[0].data = <?php echo json_encode($problemCountLastWeek) ?>;
myChart4.data.datasets[0].data = <?php echo json_encode($specialistCountLastWeek) ?>;
myChart5.data.datasets[0].data = <?php echo json_encode($issueCountLastWeek) ?>;
myChart6.data.datasets[1].data = <?php echo json_encode($specialistInINPROGRESSCountLastWeek)?>;
myChart6.data.datasets[2].data = <?php echo json_encode($specialistInREVIEWCountLastWeek)?>;
myChart6.data.datasets[3].data = <?php echo json_encode($specialistInResolvedCountLastWeek)?>;
myChart7.data.datasets[0].data = <?php echo json_encode($numberOfHardwareIssuesLastWeek) ?>;
myChart9.data.datasets[0].data = <?php echo json_encode($issuesReporteddLastWeek) ?>;
myChart10.data.datasets[0].data = <?php echo json_encode($extSpecialistNotResovledCountLastWeek) ?>;
myChart10.data.datasets[1].data = <?php echo json_encode($extSpecialistResovledCountLastWeek) ?>;

myChart2.update();
myChart4.update();
myChart5.update();
myChart6.update();
myChart7.update();
myChart9.update();
myChart10.update();

document.getElementById('ButtoN').disabled = false;
document.getElementById('lastWeek').disabled = true;
document.getElementById('lastMonth').disabled = false;

SoftwareHeading.innerHTML = "<center>Ticket Tickets/Software (in the last Week)</center>"
TicketDistributionHeading.innerHTML = "<center>Ticket Distribution/Specialist (in the last Week)</center>"
AssignedTicketsHeading.innerHTML = "<center>Assigned Tickets/Specialists (in the last Week)</center>"
ProblemTypeHeading.innerHTML = "<center>Tickets/Problem Type (in the last Week)</center>"
HardwareHeading.innerHTML = "<center>Tickets/Hardware (in the last Week)</center>"
OperatorHeading.innerHTML = "<center>Calls/Operator (in the last Week)</center>"
ExternalHeading.innerHTML = "<center><center>Ticket Distribution/External Specialist (in the last week)</center>"
}
</script>

</body>
</html>