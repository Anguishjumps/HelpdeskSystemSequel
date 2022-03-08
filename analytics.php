<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <title>Analytics Page</title>
  <script src=" https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js "></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src='https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js'></script>
  <script>

   //AJAX code to display output on the same page
  //   $(document).ready(function(){
  //     $("form").submit(function(event){
  //       //Preventing the default action of submitting the form
  //       event.preventDefault();
  //       var startDate = $("#startdate").val();
  //       var endDate = $("#enddate").val();
  //       // var sort = $("#sorting_criteria").val();
  //       $.post("analytics-filter.php", {startingDate:startDate, endingDate:endDate},function(responseData){
          
  //       });
  //     });
  // });

</script>





</head>

<center style="font-weight: bolder; font-size: xx-large;"> Analytics </center>
<body>

<?php if($_SESSION['deptName'] != 'Operator') header("Location: ./ticket-list.php"); ?>


<?php 
  // $message = '';
  // $message2 = '';
  $con = new mysqli("localhost","team008","dbnkKF2ykC","team008");

  //$sql = "SELECT * FROM Software";
  //$table = mysqli_query($con, $sql);


  // If request is made back to same page
//   if(isset($_POST['SubmitButton'])){
//     $message = $_POST['startdate'];
//     $message2 = $_POST['enddate'];
//       $query = $con->query("SELECT Personnel.FullName as SpecialistsUpdate, COUNT(Ticket.AssignedSpecialistID) as assignedTicketsUpdate 
//       FROM Personnel, Ticket 
//       WHERE Ticket.AssignedSpecialistID = Personnel.ID AND 
//       Ticket.AssignedSpecialistID IS NOT NULL AND 
//       DATE(Ticket.CreatedTimestamp) BETWEEN " . $_POST['startdate'] . " AND " . $_POST['enddate'] . " GROUP BY Personnel.FullName");

// foreach($query as $data)
// {
//   $SpecialistsUpdate[] = $data['SpecialistsUpdate'];
//   $assignedTicketsUpdate[] = $data['assignedTicketsUpdate'];
// }

//echo JSON_ENCODE($assignedTicketsUpdate);
// echo ($_POST['enddate']); 
// echo $_POST['startdate'];
// echo $message;

//}









  //Tickets groupped by state
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
  
  //Problem type and count
  $query = $con->query("
  SELECT ProblemType.Problem as problemTypes, Count(Ticket.ID) as numberOfProblems
  FROM Ticket INNER JOIN ProblemType ON ProblemType.ID=Ticket.TypeID 
  GROUP BY ProblemType.Problem
  ");

  foreach($query as $data)
  {
    $problems[] = $data['problemTypes'];
    $problemCount[] = $data['numberOfProblems'];
  }



  $query = $con->query("
  select ReporterID as reporter, COUNT(ReporterID) as numberOfReporters 
  FROM Ticket 
  group by ReporterID
  ");

  foreach($query as $data)
  {
    $reporter[] = $data['reporter'];
    $reporterCount[] = $data['numberOfReporters'];
  }

  //Assigned specialists and the total assigned tickets
  $query = $con->query("
  SELECT Personnel.FullName as specialist, COUNT(Ticket.AssignedSpecialistID) as numberOfSpecialists 
  FROM Personnel, Ticket 
  WHERE Ticket.AssignedSpecialistID = Personnel.ID AND Ticket.AssignedSpecialistID IS NOT NULL 
  GROUP BY Personnel.FullName
  ");

  foreach($query as $data)
  {
    $specialist[] = $data['specialist'];
    $specialistCount[] = $data['numberOfSpecialists'];
  }

  //Software and number of tickets
  $query = $con->query("
  SELECT Software.SoftwareName as Software,COUNT(Ticket.SoftwareID) as numberOfIssues 
  FROM Software,Ticket 
  WHERE Software.ID=Ticket.SoftwareID AND Ticket.SoftwareID IS NOT NULL 
  GROUP BY Software.SoftwareName
  ");

  foreach($query as $data)
  {
    if (strpos($data['Software'], ' ') !== false) {
      $data['Software'] = explode(" ",$data['Software']);
  }
    $Software[] = $data['Software'];
    $issueCount[] = $data['numberOfIssues'];
  }


  //Specialist and the TODO tickets
  $query = $con->query("
  Select AssignedSpecialistID as specialistInTODO, COUNT(AssignedSpecialistID) as specialistInTODOCount 
  FROM Ticket 
  WHERE AssignedSpecialistID IS NOT NULL AND TicketState=\"TODO\"
  GROUP BY AssignedSpecialistID
  ");

  foreach($query as $data)
  {
    $specialistInTODO[] = $data['specialistInTODO'];
    $specialistInTODOCount[] = $data['specialistInTODOCount'];
  }

  
  //Specialist and the RESOLVED tickets
  $query = $con->query("
  SELECT Specialist.PersonID AS \"specialistInResolved\", COUNT(Ticket.AssignedSpecialistID) AS \"specialistInResolvedCount\" 
  FROM Specialist LEFT JOIN Ticket ON Specialist.PersonID = Ticket.AssignedSpecialistID 
  WHERE Ticket.TicketState=\"RESOLVED\" 
  GROUP BY Specialist.PersonID
  ");

  foreach($query as $data)
  {
    $specialistInResolved[] = $data['specialistInResolved'];
    $specialistInResolvedCount[] = $data['specialistInResolvedCount'];
  }

  //Specialist and the INPROGRESS tickets
  $query = $con->query("
  SELECT Specialist.PersonID AS \"specialistInINPROGRESS\", COUNT(Ticket.AssignedSpecialistID) AS \"specialistInINPROGRESSCount\" 
  FROM Specialist LEFT JOIN Ticket ON Specialist.PersonID = Ticket.AssignedSpecialistID 
  WHERE Ticket.TicketState=\"INPROGRESS\" 
  GROUP BY Specialist.PersonID
  ");

  foreach($query as $data)
  {
    $specialistInINPROGRESS[] = $data['specialistInINPROGRESS'];
    $specialistInINPROGRESSCount[] = $data['specialistInINPROGRESSCount'];
  }

  //Specialist and the INREVIEW tickets
  $query = $con->query("
  SELECT Specialist.PersonID AS \"specialistInREVIEW\", COUNT(Ticket.AssignedSpecialistID) AS \"specialistInREVIEWCount\" 
  FROM Specialist LEFT JOIN Ticket ON Specialist.PersonID = Ticket.AssignedSpecialistID 
  WHERE Ticket.TicketState=\"INREVIEW\" 
  GROUP BY Specialist.PersonID
  ");

  foreach($query as $data)
  {
    $specialistInREVIEW[] = $data['specialistInREVIEW'];
    $specialistInREVIEWCount[] = $data['specialistInREVIEWCount'];
  }

  //Average time taken for a resolved ticket
  $query = $con->query("
  SELECT AVG(DATEDIFF(ResolvedTimestamp,CreatedTimestamp)) as avgResolutionTime
  FROM Ticket 
  WHERE ResolvedTimeStamp IS NOT NULL
  ");

  foreach($query as $data)
  {
    $avgResolutionTime = $data['avgResolutionTime'];
  }

  //Hardware and the number of tickets
  $query = $con->query("
  SELECT Hardware.Device as Hardware,COUNT(Ticket.HardwareID) as numberOfHardwareIssues FROM Hardware,Ticket 
  WHERE Hardware.ID=Ticket.HardwareID AND Ticket.HardwareID IS NOT NULL 
  GROUP BY Hardware.Device
  ");

  foreach($query as $data)
  {
    $Hardware[] = $data['Hardware'];
    $numberOfHardwareIssues[] = $data['numberOfHardwareIssues'];
  }

  //Day of month and number of tickets
  $query = $con->query("
  SELECT DAY(CreatedTimestamp) AS Day, COUNT(TypeID) AS Tickets from Ticket group by Day
  ");

  foreach($query as $data)
  {
    $Day[] = $data['Day'];
    $Tickets[] = $data['Tickets'];
  }

//Query for table with software, hardware, make and Issue
$query = "SELECT Hardware.Device, Hardware.Make, Software.SoftwareName, Ticket.TicketDescription, Ticket.TicketState
FROM Hardware, Software, Ticket 
WHERE Ticket.HardwareID=Hardware.ID 
AND Ticket.SoftwareID=Software.ID";

//Table to be changed
$tableQuery1 = "  SELECT ProblemType.Problem as problemTypes, Count(Ticket.ID) as numberOfProblems
FROM Ticket INNER JOIN ProblemType ON ProblemType.ID=Ticket.TypeID 
GROUP BY ProblemType.Problem";

$conn = mysqli_connect("localhost","team008","dbnkKF2ykC","team008");
$table = mysqli_query($conn, $query);
$sideTable1 = mysqli_query($conn, $tableQuery1);

//Longest unresolved ticket
$query = $con->query("
SELECT ID as ticketID, MAX(DATEDIFF(CURRENT_TIMESTAMP,CreatedTimestamp)) as unresolvedDays FROM Ticket WHERE ResolvedTimeStamp IS NULL
");

foreach($query as $data)
{
  $ticketID = $data['ticketID'];
  $unresolvedDays = $data['unresolvedDays'];
}

//Average time a ticket has remained open
$query = $con->query("
SELECT AVG(DATEDIFF(CURRENT_TIMESTAMP,CreatedTimestamp)) as avgOpenTime FROM Ticket WHERE ResolvedTimeStamp IS NULL
");

foreach($query as $data)
{
  $avgOpenTime = $data['avgOpenTime'];
}

//Specialist with the most open tickets
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

?>

<div>
  <div style="width: 450px; float: left; padding-left:60px; padding-top:30px">
    <div style="border-style:solid; height: 620px; border-radius:5px; background-color:#ffe6d4">
      <canvas id="myChart"></canvas>
      <center style="padding-top: 10px;"><b>Ticket States</b></center><br>
      <ul>
        <li><?php echo "  Average resolved ticket time:<b> " . round((str_replace("\"","",json_encode($avgResolutionTime))),2) . "</b> days" ?></li>
        <li><?php echo "  Average unresolved ticket time:<b> " . round((str_replace("\"","",json_encode($avgOpenTime))),2) . "</b> days" ?></li>
        <li><?php echo "  Oldest unresolved ticket:<b> " . round((str_replace("\"","",json_encode($unresolvedDays))),2) . "</b> days" ?></li>
        <li><?php echo "<b>" . str_replace("\"","",json_encode($mostUnresolved)) . "</b> has the most unresolved tickets:<b> " . round((str_replace("\"","",json_encode($numberOfUnresolved))),0) . "</b>" ?></li>
      </ul>
    </div>
  </div>
  <div style="float: right; padding-right: 60px; padding-top: 30px; width: 70%">
    <div style="border-style:solid; padding:20px; height: 620px; border-radius:5px; background-color:#ffd5b5">
      <!-- <form method="POST">   -->
      <center>
      <label for="start">Start Date:</label>
      <input type="date" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" id="startdate" name="startdate">
      <!-- onblur="(this.type='text')" -->
      <label for="end">End Date:</label>
      <input type="date" placeholder="dd/mm/yyyy" onfocus="(this.type='date')"  id="enddate" name="enddate">
      <!-- <input type="date" id="end" name="end" placeholder="End Date" onfocus="(this.type='date')"> -->
      <input type="submit" onclick="retrieve()" name="SubmitButton"><button id="ButtoN" type="button" style="margin-left:3px" onclick="revert()" disabled> Revert </button>
      </center>
      <!-- </form> -->
      <br>  
      <div>
        <div style="float:right;">
          <div style="width: 475px; float:top; border-style:inset; border-radius:5px; background-color:white">
            <u><center>Specialists and Assigned Tickets Distribution</center></u>
            <canvas id="myChart6"></canvas>
          </div>
          <div style="width: 475px; float:bottom; border-style:inset; border-radius:5px; background-color:white">
            <u><center>Reporters and the Number of Reported Tickets</center></u>
            <canvas id="myChart3"></canvas>
          </div>
        </div>
        <div style="float: left;">
          <div style="width: 475px; float:top; border-style:inset; border-radius:5px; background-color:white">
            <u><center>Specialists and Assigned Tickets</center></u>
            <canvas id="myChart4"></canvas>
          </div>
          <div style="width: 475px; float:bottom; border-style:inset; border-radius:5px; background-color:white">
            <u><center>Problem Types</center></u>
            <canvas id="myChart2"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="serverResponses">
    <div style="float: right; padding-right: 60px; padding-top: 25px; width: 70%; padding-bottom:10px">
      <div style="border-style:solid; padding:20px; height: 620px; border-radius:5px; background-color:#ffd5b5">
        <div float="top">
          <div style="width: 475px; border-style:inset; border-radius:5px; float:left; background-color: white;">
            <u><center>Software and Their Related Tickets</center></u>
                <canvas id="myChart5"></canvas>
            </div>
            <div  style="width: 475px; border-style:inset; border-radius:5px; float:right; background-color:white">
              <u><center>Hardware and Their Related Tickets</center></u>
                <canvas id="myChart7"></canvas>
            </div>
          </div>
          <div style="float:bottom;">         
            <div style="overflow:auto; height:320px; width:100%;">
              <table class="table caption-top table-hover table-bordered table-sm table-striped" style="border: 3px; background-color:white">
                <thead class="table-dark" style="position: sticky; top: 0;">
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
      <div style="width: 450px; float: left; padding-left:60px; padding-top:26px">
        <div style="border-style:solid; height: 620px; border-radius:5px; background-color:#ffe6d4">
          <div style=" width:95%; border-style:inset; border-radius:5px; background-color: white;  margin-top: 22px; margin-left:10px; ">
            <u><center>Days of the Month and Number of Tickets Logged</center></u>
              <div style="height:215px; margin-top:15px">
                <canvas id="myChart8" style="background-color:white;"></canvas>
              </div>
          </div>
          <div id="bottomInside" style="float:bottom; margin-left:10px; margin-right:10px">
            <!-- This has to be changed -->
            <table class="table caption-top table-hover table-bordered table-sm table-striped" style="border: 3px; background-color:white">
              <thead class="table-dark" style="position: sticky; top: 0;">
                <tr>
                  <th scope="col">Problem Type</th>
                  <th scope="col">Count</th>
                </tr>
              </thead>
                <tbody id="myTable">
                  <?php if (mysqli_num_rows($sideTable1) > 0) {
                    //output data of each row
                    while ($row = mysqli_fetch_array($sideTable1)) {
                      echo "<tr>
                      <td>$row[0]</td>
                      <td>$row[1]</td> 
                      </tr>";
                      }
                    }
                  ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div id="serverResponse">

    </div>
</div>

<script>

  // === include 'setup' then 'config' above ===
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

  const labels2 = <?php echo json_encode($problems) ?>;
  const data2 = {
    labels: labels2,
    datasets: [{
      label: 'Problem Types',
      data: <?php echo json_encode($problemCount) ?>,
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

  const labels3 = <?php echo json_encode($reporter) ?>;
  const data3 = {
    labels: labels3,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($reporterCount) ?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
      ],
      borderColor: [
        'rgb(255, 99, 132)',
      ],
      borderWidth: 1
    }]
  };

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

  const labels6 = <?php echo json_encode($specialist) ?>;
  const data6 = {
    labels: labels6,
    datasets: [{
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
      label:"Resolved",
      data: <?php echo json_encode($specialistInResolvedCount) ?>,
      backgroundColor:"rgba(54, 162, 235, 0.2)",
      borderColor: "rgba(54, 162, 235)",
      borderWidth: 1
    },
    {
      label:"To-Do",
      data: <?php echo json_encode($specialistInTODOCount) ?>,
      backgroundColor:"rgba(255,99, 132,0.2)",
      borderColor: "rgba(255, 99, 132)",
      borderWidth: 1
    },
    {
      label:"In-Review",
      data: <?php echo json_encode($specialistInREVIEWCount) ?>,
      backgroundColor:"rgba(153, 102, 255, 0.2)",
      borderColor:"rgba(153, 102, 255)",
      borderWidth: 1
    }]
  }
  
  const labels7 = <?php echo json_encode($Hardware) ?>;
  const data7 = {
    labels: labels7,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($numberOfHardwareIssues) ?>,
      backgroundColor: [

        'rgba(75, 135, 174, 0.2)',
      ],
      borderColor: [
        'rgb(75, 135, 174)',
      ],
      borderWidth: 1
    }]
  };
  
  const labels8 = <?php echo json_encode($Day) ?>;
  const data8 = {
    labels: labels8,
    datasets: [{
      label: 'Number of Tickets',
      data: <?php echo json_encode($Tickets) ?>,
      fill: true,
      backgroundColor: [

        'rgba(75, 135, 174, 0.2)',
      ],
      borderColor: [
        'rgb(75, 135, 174)',
      ],
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
    type: 'bar',
    data: data2,
    options: {
onClick(e){
  console.log("x");
}
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
    type: 'bar',
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




//Function on hitting submit
  function retrieve() {
    var startDate = $("#startdate").val();     
    var endDate = $("#enddate").val();
    //serverResponse.innerHTML = startDate





    <?php
      
      //Couple queries just to update the chart values. The inputted start and end date is supposed to go in these queries so that the values get updated.
      //Only updating the values on 3 graphs here cause I was testing
      $query = $con->query("
      SELECT Personnel.FullName as Specialists, COUNT(Ticket.AssignedSpecialistID) as assignedTickets 
      FROM Personnel, Ticket 
      WHERE Ticket.AssignedSpecialistID = Personnel.ID AND 
      Ticket.AssignedSpecialistID IS NOT NULL AND 
      DATE(Ticket.CreatedTimestamp) BETWEEN '2021-10-10' AND '2021-10-30' 
      GROUP BY Personnel.FullName
      ");
  
      foreach($query as $data)
      {
        $Specialists[] = $data['Specialists'];
        $assignedTicket[] = $data['assignedTickets'];
      }

      $query = $con->query("
      SELECT Software.SoftwareName as Softwares,COUNT(Ticket.SoftwareID) as numberOfIssuesUpdated 
      FROM Software,Ticket 
      WHERE Software.ID=Ticket.SoftwareID AND Ticket.SoftwareID IS NOT NULL AND DATE(Ticket.CreatedTimestamp) 
      BETWEEN '2021-10-10' AND '2021-10-30' GROUP BY Software.SoftwareName
      ");
  
      foreach($query as $data)
      {
        $Softwares[] = $data['Softwares'];
        $numberOfIssuesUpdated[] = $data['numberOfIssuesUpdated'];
      }

      $query = $con->query("
      SELECT TicketState as state, Count(ID) as numberOfTicketStatesUpdated FROM Ticket 
      WHERE CreatedTimestamp 
      BETWEEN '2021-10-10' AND '2021-10-30' 
      GROUP BY TicketState
      ");
  
      foreach($query as $data)
      {
        $state[] = $data['state'];
        $numberOfTicketStatesUpdated[] = $data['numberOfTicketStatesUpdated'];
      }
      ?>
     myChart4.data.datasets[0].data = <?php echo json_encode($assignedTicket) ?>;
     myChart5.data.datasets[0].data = <?php echo json_encode($numberOfIssuesUpdated) ?>;
     myChart2.data.datasets[0].data = [13,1];
     myChart4.update();
     myChart5.update();
     myChart2.update();
     document.getElementById("ButtoN").disabled = false;
     console.log("Logged");
}

function revert(){
  myChart4.data.datasets[0].data = <?php echo json_encode($specialistCount) ?>;
  myChart5.data.datasets[0].data = <?php echo json_encode($issueCount) ?>;
  myChart2.data.datasets[0].data = <?php echo json_encode($problemCount) ?>;
  myChart4.update();
  myChart5.update();
  myChart2.update();
  document.getElementById("ButtoN").disabled = true;

}

//Everything below this isn't in use
function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      xhttp.responseText // return from your php;
    }
  };
  xhttp.open("GET", "analytics.php?startD="+startDate, true);
  xhttp.send();
}


   
// Function to create the cookie
function createCookie(name, value, days) {
    var expires;
      
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
      
    document.cookie = escape(name) + "=" + 
        escape(value) + expires + "; path=/";
}

function ajaxuse(){
  var startDate = $("#startdate").val();

  //ajax
  $.ajax({
    type:'post',
    data: {ajax:1, date: startDate},
    success: function(serverResponse){
      $().text('date :' + serverResponse);
    }
  })
}


</script>

</body>
</html>