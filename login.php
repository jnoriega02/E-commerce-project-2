<?php
include "DB_Secerts.php"; 
?>
<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
 if (isset($_SESSION["message"])) {
    $message = $_SESSION["message"];
    
    // Clear the message from the session
    unset($_SESSION["message"]);
} else {
    $message = "";
}
 ?>

<!DOCTYPE hmtl> 

<html>
       <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css_files/style.css">
        <title>Login</title>
        <script>
    function Homepage()
                {
                    window.location.href = "Homepage.html";
                }
        </script>
    </head>
    <body style = "background-color: rgba(0, 136, 255, 0.3);">
<!--Gives background to navigation bar at the top of the page-->
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark py-3">
            <div class="container-fluid">
              <ul class="navbar-nav">
              <li class="nav-item">
                <div class = "right">
            <div class = "button"><button onclick = Homepage()>Homepage</button>
                </div>
            </div>
                </li>
              </ul>
            </div>
        </nav>           

    <div class="container align-items-center justify-content-center text-dark form-group">
            <h1 class="container align-items-center justify-content-center">Sign In</h1>

            <p>Please Login With Your Proper Credentials.</p>
    </div>
                <form action="login.php" method="post">
   
  <div class="container form-group">
    <label for="Username"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="Username"required>
  </div>
    
          <div class="container form-group">
    <label for="username"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="Password"required>
          </div>
   
    <div class="container form-group">
    <button type = "Submit">Login</button>
    </div>
                </form>
    <?php
    //use $_SERVER function to check if html form method is post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Username = $_POST["Username"];
    $Pass = $_POST["Password"];

    //set $conn variable as a connection to the database(prevents writing extra code)
    $conn = $p;
    
    //prepare() plus bindParam() function calls to prevent SQL injection attakcs
    $stmt = $conn->prepare("SELECT EmpID,Password, Position, Name FROM Employee WHERE Name = :Username AND Password = :Password");
    $stmt->bindParam(':Username', $Username);//bind user input pulled from Username label
    $stmt->bindParam(':Password', $Pass);//bind user input pulled from Password label
    $stmt->execute();


    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['Position'] === 'SalesAssoc') //if Position of user in database is SalesAssoc, send them to SalesAssoc page
    {
        $Position = $result['Position'];
        $_SESSION["Position"] = $Position;
        $_SESSION["Username"] = $Username;
        $_SESSION["EmpID"] = $result['EmpID'];
        header("Location: SalesAssociate.php");
        exit();
    } 
    
    elseif ($result && $result['Position'] === 'Admin') //if Position of user in database is Admin, send them to Admin page
    {
        $Position = $result['Position'];
        $_SESSION["Position"] = $Position;
        $_SESSION["Username"] = $Username;
        $_SESSION["EmpID"] = $result['EmpID'];

        header("Location: Admin_SA.php");
        exit();
    } 

    elseif ($result && $result['Position'] === 'HeadQ') //if Position of user in database is HeadQ, send them to Headquarter page
    {
        $Position = $result['Position'];
        $_SESSION["Position"] = $Position;
        $_SESSION["Username"] = $Username;
        $_SESSION["EmpID"] = $result['EmpID'];
        header("Location: Headquarter.php");
        exit();
    } 
    elseif ($result && $result['Position'] === 'ThirdFace') //if Position of user in database is ThirdFace, send them to Third Interface page
    {
        $Position = $result['Position'];
        $_SESSION["Position"] = $Position;
        $_SESSION["Username"] = $Username;
        $_SESSION["EmpID"] = $result['EmpID'];
        header("Location: Third_interface.php");
        exit();
    } 
    else 
    {   if($stmt->rowCount() === 0)
        {
        echo "<div class=\"container\">";
        echo "<font color=red><b>Invalid username or Password</b></font>";
        echo "</div>";
        session_unset();
        session_destroy();
        
        }
        else {
            // Invalid role, redirect to appropriate page
            $_SESSION["message"] = "You don't have the required position to access this page.";
            session_unset();
            session_destroy();

            header("Location: login.php");
            exit();
        }
    }
} 
                ?>
        </body>     
    </html>