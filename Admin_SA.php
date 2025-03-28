<?php
include  "DB_Secerts.php";
?>

<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

// Check if the user is logged in (username is stored in the session)
if (!isset($_SESSION["Username"]) || !isset($_SESSION["EmpID"]) || $_SESSION["Position"] !== "Admin")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Administrator to have access. Redirecting to login page...</h1>";
        echo "<script>
                function redirection() {
                    window.location.href = 'login.php?t={$cacheBuster}';
                }
        
                // Call the redirect function
                setTimeout(redirection, 4000);
            </script>";
            exit();
}
$connection2 = $p;
$empId = $_SESSION['EmpID'];
$stmt = $connection2->prepare("SELECT Position From Employee WHERE EmpID = :empid");
if(!$stmt)
{    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Administrator to have access. Redirecting to login page...</h1>";
        echo "<script>
                function redirection() {
                    window.location.href = 'login.php?t={$cacheBuster}';
                }
        
                // Call the redirect function 
                setTimeout(redirection, 4000);
            </script>";
            exit();
}
$stmt->bindValue(':empid',$empId,PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$result || $result['Position'] !== "Admin")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
      echo "<h1>Must be a Administrator to have access. Redirecting to login page...</h1>";
        echo "<script>
                function redirection() {
                    window.location.href = 'login.php?t={$cacheBuster}';
                }
        
                // Call the redirect function
                setTimeout(redirection, 4000);
            </script>";
            exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_files/style.css">
    <title>Administrator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../css_files/modal.js"></script>

    <script>
        function Quote_AD() {
            window.location.href = "Admin_Quote.php";
        }

        function Logout()
                {
                    
                <?php
                if (isset($_GET['logout'])) {
                    // Start or resume the session
                    session_start();

                    // Destroy all session data
                    session_unset();
                    session_destroy();

                    // Redirect to the login page after logout
                    header("Location: Homepage.html");
                    exit();
                }
                ?>

                    window.location.href = '?logout=true';
                }
    </script>
</head>

<body style="background-color: rgba(0, 136, 255, 0.3);">
    <div class="text-center">
        <header>
            <h1>Administration</h1>
        </header>
        <h2>Plant Repair Services</h2>
    </div>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark py-3">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <div class="right">
                    <div class="button"><button onclick="Logout()">Logout</button></div>
                </div>
            </li>

            <li class="nav-item">
                <div class="ml-auto">
                    <div class="button"><button onclick="Quote_AD()">Quote</button></div>
                </div>
            </li>
        </ul>
        <div class="text-center">
       
                <span class="navbar-text ">Welcome, <?php echo $_SESSION['Username']; ?></span>
         </div>



    </div>
            </nav>
    <div class="text-center">
        <header>
            <h2><u>Sales Associates</u></h2>
        </header>

        <?php
        include "DB_Secerts.php";

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        function deleteEmployee($connection, $empIdToDelete)
        {
            try {
                // Prepare the SQL query to delete the employee record
                $deleteQuery = "DELETE FROM Employee WHERE EmpId = :empId";

                $stmt = $connection->prepare($deleteQuery);

                $stmt->bindParam(':empId', $empIdToDelete);

                $stmt->execute();

                header("Location: Admin_SA.php");
                exit();
            } catch (PDOException $e) {
                echo "Error when deleting the employee: " . $e->getMessage();
                die();
            }
        }

        function addEmployee($connection, $name, $password, $address, $position)
        {
            try {
                // Prepare the SQL query to add the new employee
                $insertQuery = "INSERT INTO Employee (Name, Password, Address, Position) VALUES (:Name, :Password, :Address, :Position)";
                $stmt = $connection->prepare($insertQuery);
                $stmt->bindParam(':Name', $name);
                $stmt->bindParam(':Password', $password);
                $stmt->bindParam(':Address', $address);
                $stmt->bindParam(':Position', $position); 
                $stmt->execute();

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "Error when adding new Employee: " . $e->getMessage();
            }
        }

        // Saving $p variable from DB_Secrets.php into $connection variable for the prepared statement.
        $connection = $p;

        // Checking if the submit button for the new employee was pressed
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['position']) && isset($_POST['address'])) {
                $name = $_POST['name'];
                $password = $_POST['password'];
                $position = $_POST['position'];
                $address = $_POST['address']; 

                // Call the function to add the new employee
                addEmployee($connection, $name, $password, $address, $position);
            }
        }
        // Checking if delete button was pressed
        if (isset($_POST['delete']) && isset($_POST['employee_id'])) {
            $empIdToDelete = $_POST['employee_id'];
            // Call the function to handle the deletion
            deleteEmployee($connection, $empIdToDelete);
        }

        // Checking if the edit button was clicked
        if (isset($_POST['edit']) && isset($_POST['edit_id'])) {
            $editId = $_POST['edit_id'];

            // Fetch the existing employee information from the database based on the editId
            $stmt = $connection->prepare("SELECT EmpId, Name, Commissions, Address, Password FROM Employee WHERE EmpId = :editId");
            $stmt->bindParam(':editId', $editId);
            $stmt->execute();
            $employeeData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the employee was found
            if ($employeeData) {
                // Display the edit form with the existing employee information
                ?>
                <h2>Edit Employee Information</h2>
        <!-- Inside the edit form, add the Commissions field -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="hidden" name="update_id" value="<?php echo $employeeData['EmpId']; ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $employeeData['Name']; ?>" required><br>
         <label for="commissions">Commissions:</label>
        <input type="text" name="commissions" value="<?php echo $employeeData['Commissions']; ?>" required><br>
        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo $employeeData['Address']; ?>" required><br>
        <label for="password">Password:</label>
        <input type="text" name="password" value="<?php echo $employeeData['Password']; ?>" required><br>
        <input type="submit" name="update" value="Update">
</form>

            <?php
            } else {
                echo "Employee not found.";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Checking if the update button was pressed
            if (isset($_POST['update']) && isset($_POST['update_id'])) {
                $updateId = $_POST['update_id'];
                $name = $_POST['name'];
                $commissions = $_POST['commissions'];
                $address = $_POST['address'];
                $password = $_POST['password'];

                try {
                    // Prepare the SQL query to update the employee record
                    $updateQuery = "UPDATE Employee SET Name = :name, Commissions = :commissions, Address = :address, Password = :password WHERE EmpId = :updateId";
                    $stmt = $connection->prepare($updateQuery);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':commissions', $commissions);
                    $stmt->bindParam(':address', $address);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':updateId', $updateId);
                    $stmt->execute();

                   
                    header("Location: Admin_SA.php");
                    exit();
                } catch (PDOException $e) {
                    echo "An error occurred while updating the employee: " . $e->getMessage();
                }
            }
        }

        // Select EmpId, Name, and Commissions from the Employee database where the position is only SA
        $stmt = $connection->prepare("SELECT EmpId, Name, Commissions, Address, Password FROM Employee WHERE Position = 'SalesAssoc'");

        // Execute prepared statement.
        $stmt->execute();

        // Set the $results variable to the results of the execute statement.
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <!-- display the results as a table -->
        <table class="table table-bordered mx-auto">
            <tr>
                <th><u>EmpId</u></th>
                <th><u>Name</u></th>
                <th><u>Commission</u></th>
                <th><u>Options</u></th>
            </tr>

            <?php foreach ($results as $row): ?>
                <tr>
                    <!-- Display data for each row in the table -->
                    <td><?php echo $row['EmpId']; ?></td>
                    <td><?php echo $row['Name']; ?></td>
                    <td>$ <?php echo $row['Commissions']; ?></td>
                    <!-- Add the edit form with a hidden input field -->
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="edit_id" value="<?php echo $row['EmpId']; ?>">
                            <input type="submit" name="edit" value="Edit">
                        </form>
                        <form method="post" style="display: inline;" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="employee_id" value="<?php echo $row['EmpId']; ?>">
                            <input type="submit" name="delete" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div>
        <div class="left">
            <h1><u>Add Associate</u></h1>
            <!-- Update the form action to point to the same page without any redirection -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <!-- Display text boxes and make them into variables to add to the database -->
                <h3>Name: <input type="text" name="name" placeholder="First Last" required></h3>
                <h3>Password: <input type="text" name="password" placeholder="Password" required></h3>
                <h3>Address: <input type="text" name="address" placeholder="Address" required></h3>
                <h3>Position:
                    <select name="position">
                        <option value="SalesAssoc">Sales Associate</option>
                        <option value="ThirdFace">Third Interface Associate</option>
                        <option value="HeadQ">Headquarters Associate</option>
                    </select>
                </h3>
                <input type="submit" name="submit" value="Submit">
            </form>
        </div>
    </div>

</body>
</html>
