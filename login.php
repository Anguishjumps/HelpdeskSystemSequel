<html>
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Login</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="css/login.css">
      <link rel="stylesheet" href="css/styles.css">
      <link rel="icon" href="img/logoTools.png">
      <?php
        // Details for the database.
        const SERVERNAME = "localhost";
        const USERNAME = "team008";
        const PASSWORD = "dbnkKF2ykC";
        const DBNAME = "team008";
        // Start the user session.
        session_start();
        ?>
  </head>
  <body>
    <!--Container for the alert banner and login form.-->
    <div class="login">
    <!--Initially empty container for if/when banners are displayed
        Prevents the screen moving when a banner is created/deleted.
    -->
    <div class="alertContainer"></div>  
      <!--Container for the form.-->
      <form name="loginForm" class="form">
        <img id="logoImage" src="img/logoTools.png" alt="picture of make-it-all logo">
        <p id="loginLabel">Account Login</p>
        <input name="username" id="usernameInput" type="text" placeholder="Username" required/>
        <input name="password" id="passwordInput" type="password" placeholder="Password" required />
        <button type="button" onclick="checkLogin()">Login</button>
      </form>  
    </div>
  </body>
    <!--Calling all relevant JS scripts.-->
    <script src="js/script.js"></script>
    <script src="js/login.js"></script>
</html>