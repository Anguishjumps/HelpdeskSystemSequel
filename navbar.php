<!--
  Linking the CSS file.
-->
<link rel="stylesheet" href="css/navbar.css">
<script src="./js/script.js"></script>

<div class= "sidebar">
  <!--Snippet of the company logo.-->
  <img id="logo" class="center" src="img/logoTools.png" alt="tools" />
  <!--
    Assigns the 'active' class to the page if it is the current page.
    This is done because the code is not manually implemented on each individual page so it it not automatically known which page is 'active'.
  -->
  <?php if ($_SESSION['deptName'] == "Operator") { ?>
    <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/analytics-page.php' ? 'active' : '') ?>" href="analytics-page.php">
    <!--
      SVG Image without use of external libraries.
      Width uses 'view width' which means the size of the icon is based on the screen size making it responsive.
    -->
    <svg class="sidebar-icon"
      xmlns="http://www.w3.org/2000/svg"
      width="2vw"
      height="100%"
      viewBox="0 0 32 32">
      <path d="M4 2H2v26a2 2 0 0 0 2 2h26v-2H4z" fill="currentColor"/>
      <path d="M30 9h-7v2h3.59L19 18.59l-4.29-4.3a1 1 0 0 0-1.42 0L6 21.59L7.41 23L14 16.41l4.29 4.3a1 1 0 0 0 1.42 0l8.29-8.3V16h2z" fill="currentColor"/>
    </svg>
      <h3 class="sidebar-label">Analytics</h3>
    </a>
  <?php } ?>
  <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/equipment-page.php' ? 'active' : '') ?>" href="equipment-page.php">
  <svg class="sidebar-icon"
   xmlns="http://www.w3.org/2000/svg" 
   width="2vw" 
   height="100%" 
   viewBox="0 0 512 512">
   <rect x="80" y="80" width="352" height="352" rx="48" ry="48" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
   <rect x="144" y="144" width="224" height="224" rx="16" ry="16" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 80V48"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M336 80V48"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 80V48"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 464v-32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M336 464v-32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 464v-32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M432 256h32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M432 336h32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M432 176h32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M48 256h32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M48 336h32"/>
   <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M48 176h32"/>
  </svg>
    <h3 class="sidebar-label">Equipment</h3>
  </a>
  <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/software-page.php' ? 'active' : '') ?>" href="software-page.php">
  <svg class="sidebar-icon"
    xmlns="http://www.w3.org/2000/svg" 
    width="2vw" 
    height="100%" 
    viewBox="0 0 32 32">
    <path d="M21.49 13.115l-9-5a1 1 0 0 0-1 0l-9 5A1.008 1.008 0 0 0 2 14v9.995a1 1 0 0 0 .52.87l9 5A1.004 1.004 0 0 0 12 30a1.056 1.056 0 0 0 .49-.135l9-5A.992.992 0 0 0 22 24V14a1.008 1.008 0 0 0-.51-.885zM11 27.295l-7-3.89v-7.72l7 3.89zm1-9.45L5.06 14L12 10.135l6.94 3.86zm8 5.56l-7 3.89v-7.72l7-3.89z" fill="currentColor"/>
    <path d="M30 6h-4V2h-2v4h-4v2h4v4h2V8h4V6z" 
    fill="currentColor"/>
  </svg>
    <h3 class="sidebar-label">Software</h3>
  </a>
  <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/ticket-list.php' ? 'active' : '') ?>" href="ticket-list.php">
  <svg class="sidebar-icon"
    xmlns="http://www.w3.org/2000/svg"
    width="2vw"
    height="100%"
    viewBox="0 0 16 16">
    <g fill="currentColor">
      <path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM5 7a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H5z"/><path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5a1.5 1.5 0 0 0 0 3a.5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5a1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6V4.5zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5h-13z"/>
    </g>
  </svg>
    <h3 class="sidebar-label">Tickets</h3>
  </a>
  <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/archive-page.php' ? 'active' : '') ?>" href="archive-page.php">
  <svg class="sidebar-icon"
    xmlns="http://www.w3.org/2000/svg"
    width="2vw"
    height="100%"
    viewBox="0 0 16 16">
    <g fill="currentColor">
      <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1V2zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5H2zm13-3H1v2h14V2zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
    </g>
  </svg>
    <h3 class="sidebar-label">Archive</h3>
  </a>
  <!--Call-page will only show for the user if they are an 'Operator'-->
  <?php if ($_SESSION['deptName'] == "Operator") { ?>
      <a class="<?php echo ($_SERVER['PHP_SELF'] == '/21cob290-part-2-team-08/call-page.php' ? 'active' : '') ?>" href="call-page.php">
      <svg class="sidebar-icon"
        style="margin-right: 5%;"
        xmlns="http://www.w3.org/2000/svg" 
        width="2vw"
        height="100%"
        viewBox="0 0 16 16">
        <g fill="currentColor">
          <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608a17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42a18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
        </g>
      </svg>  
        <h3 class="sidebar-label">Calls</h3>
      </a>
  <?php } ?>

  <!--Link for the user doesn't have a link but is done for aesthetic consistency.-->
  <a href="#">
    <svg class="sidebar-icon"
     xmlns="http://www.w3.org/2000/svg" 
     width="2vw" 
     height="100%" 
     viewBox="0 0 24 24">
     <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33A7.95 7.95 0 0 1 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" 
     fill="currentColor"/>
    </svg>
    <h4 class="sidebar-label" id="username" data-userid="<?= $_SESSION['userid'] ?>"><?= $_SESSION['username'] ?></h4>
  </a>
  <!--Allows the user to logout and return to the login page.-->
  <a href="#" id="log-out-button" onclick="logOut()">
    <svg class="sidebar-icon"
      xmlns="http://www.w3.org/2000/svg" 
      width="2vw" 
      height="100%" 
      viewBox="0 0 24 24">
      <g transform="translate(24 0) scale(-1 1)">
        <g stroke-width="1.5" fill="none">
          <path d="M12 12h7m0 0l-3 3m3-3l-3-3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M19 6V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
      </g>
    </svg>
    <h3 class="sidebar-label">Log Out</h3>
  </a>
</div>