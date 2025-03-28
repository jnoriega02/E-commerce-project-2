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
<?php

// Initialize filter variables
$selectedStatus = "";
$selectedDateStart = "";
$selectedDateEnd = "";
$selectedEmpID = "";
$selectedCID = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the selected filter values from the form submission
    $selectedStatus = $_POST["status"];
    $selectedDateStart = $_POST["date_start"];
    $selectedDateEnd = $_POST["date_end"];
    $selectedEmpID = $_POST["empid"];
    $selectedCID = $_POST["cid"];
}

$connection = $p;

$legacyPdo= $pdo;

// Fetch all distinct CID values from the QuotePurchase table
$sql_cid = "SELECT DISTINCT CID FROM QuotePurchase";
$stmt_cid = $connection->prepare($sql_cid);
$stmt_cid->execute();
$CIDValues = $stmt_cid->fetchAll(PDO::FETCH_COLUMN);

// Fetch customer details from the legacy database for the fetched CIDs
$customerDetails = [];

// Fetch customer details from the legacy database for the fetched CIDs
foreach ($CIDValues as $cid) {
    $sql_get_details = "SELECT name, contact, city, street FROM customers WHERE id = :cid";
    $stmt_get_details = $legacyPdo->prepare($sql_get_details);
    $stmt_get_details->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt_get_details->execute();
    $result = $stmt_get_details->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $customerDetails[$cid] = $result;
    }
}
$sql_empid = "SELECT DISTINCT EmpID FROM QuotePurchase";
$stmt_empid = $connection->prepare($sql_empid);
$stmt_empid->execute();
$empIDValues = $stmt_empid->fetchAll(PDO::FETCH_COLUMN);

// Fetch employee names from the Employee table for the fetched EmpIDs
$employeeNames = [];

foreach ($empIDValues as $empid) {
    $sql_name = "SELECT Name FROM Employee WHERE EmpID = :empid";
    $stmt_name = $connection->prepare($sql_name);
    $stmt_name->bindParam(':empid', $empid, PDO::PARAM_INT);
    $stmt_name->execute();
    $result = $stmt_name->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $employeeNames[$empid] = $result['Name'];
    }
}
// Prepare the SQL query based on the selected filters
$sql = "SELECT QID, DateEntered, EmpID, CID, Price, Qstatus, Email, LineItem, Total, SNote FROM QuotePurchase WHERE 1";


if (!empty($selectedStatus)) {
    $sql .= " AND Qstatus = :status";
}

// Add the range-based filter for Date
if (!empty($selectedDateStart) && !empty($selectedDateEnd)) {
    $sql .= " AND DateEntered BETWEEN :date_start AND :date_end";
}

if (!empty($selectedEmpID)) {
    $selectedEmpName = $employeeNames[$selectedEmpID];
    $sql .= " AND EmpID = :empid";
}

if (!empty($selectedCID)) {
    $sql .= " AND CID = :cid";
}

$stmt = $connection->prepare($sql);

// Bind the parameters for the filters if they are not empty
if (!empty($selectedStatus)) {
    $stmt->bindParam(":status", $selectedStatus);
}

// Bind the parameters for the range-based Date filter
if (!empty($selectedDateStart) && !empty($selectedDateEnd)) {
    $stmt->bindParam(":date_start", $selectedDateStart);
    $stmt->bindParam(":date_end", $selectedDateEnd);
}

if (!empty($selectedCID)) {
    $sql_get_name = "SELECT name FROM customers WHERE id = :cid";
    $stmt_get_name = $legacyPdo->prepare($sql_get_name);
    $stmt_get_name->bindParam(':cid', $selectedCID, PDO::PARAM_INT);
    $stmt_get_name->execute();
    $result = $stmt_get_name->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $customerName = $result['name'];
        $sql .= " AND id = :cid";
        $stmt->bindParam(":cid", $selectedCID);
    }
}

if (!empty($selectedEmpID)) {
    $stmt->bindParam(":empid", $selectedEmpID); // Bind the EmpID parameter
}
// Execute the prepared statement
$stmt->execute();

// Set the $results variable to the results of the execute statement.
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_files/style.css">
    <title>Administrator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

    <script>
    function Admin_SA()
                {
                    window.location.href = "Admin_SA.php";
                }

    function Logout()
                {
                    <!-- Add the logout logic within the same file -->
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
                    <div class="button"><button onclick="Admin_SA()">Employees</button></div>
                </div>
            </li>
        </ul>
        <div class="text-center">
       
                <span class="navbar-text ">Welcome, <?php echo $_SESSION['Username']; ?></span>
         </div>



    </div>
</nav>
<div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">

<div class= 'text-center'>

                    <h1>Quotes<h1>
        </div>

<form method="post">
    <label for="status">Status:</label>
    <select name="status" id="status">
        <option value="">-- All Status --</option>
        <option value="unresolved">--Unresolved--</option>
        <option value="sanctioned">--Sanctioned--</option> 
        <option value="finalized">--Finalized--</option> 
        <option value="ordered">--Ordered--</option>
        <?php foreach ($statusValues as $status): ?>
            <option value="<?php echo $status; ?>" <?php if ($selectedStatus === $status) echo "selected"; ?>><?php echo $status; ?></option>
        <?php endforeach; ?>
    </select>

   
        <!--Add the range-based filter for Date -->
    <label for="date_start">Start Date:</label>
    <input type="date" name="date_start" id="date_start" value="<?php echo $selectedDateStart; ?>">

    <label for="date_end">End Date:</label>
    <input type="date" name="date_end" id="date_end" value="<?php echo $selectedDateEnd; ?>">

<label for="empid">Employee:</label>
<select name="empid" id="empid">
    <option value="">-- All Employees --</option>
    <?php foreach ($employeeNames as $empid => $name): ?>
        <option value="<?php echo $empid; ?>" <?php if ($selectedEmpID == $empid) echo "selected"; ?>>
            <?php echo $name; ?>
        </option>
    <?php endforeach; ?>
</select>

    <label for="cid">Customer Name:</label>
<select name="cid" id="cid">
    <option value="">-- All Customers --</option>
    <?php foreach ($customerDetails as $cid => $details): ?>
        <option value="<?php echo $cid; ?>" <?php if ($selectedCID == $cid) echo "selected"; ?>><?php echo $details['name']; ?></option>
    <?php endforeach; ?>
</select>


    <input type="submit" value="Filter">
</form>
                </div>
            </div>
        </div>
    </div>


<!-- display the results as table -->
<table class="table table-bordered mx-auto">
    <tr>
        <th><u>Quote ID</u></th>
        <th><u>Date</u></th>
        <th><u>Employee ID</u></th>
        <th><u>Customer Name</u></th>
        <th><u>Total</u></th>
        <th><u>Status</u></th>
        <th><u>Option</u></th>
    </tr>
   
    <?php foreach ($results as $row): ?>
        <tr>
            <!-- Display data for each row in the table -->
            <td><?php echo $row['QID']; ?></td>
            <td><?php echo $row['DateEntered']; ?></td>
            <td><?php echo $employeeNames[$row['EmpID']]; ?></td>
            <td><?php echo $customerDetails[$row['CID']]['name']; ?></td>
            <td>$ <?php echo $row['Total']; ?></td>
            <td><?php echo $row['Qstatus']; ?></td>
            <td>
            <button onclick="toggleDetails('<?php echo $row['QID']; ?>')">View</button>
            <
            </td>
        </tr>
        <tr class="details-row" id="details_<?php echo $row['QID']; ?>" style="display: none;">
            <td colspan="15">

                
                <p><strong>Customer ID:</strong> <?php echo $row['CID']; ?></p>
                <p><strong>Email:</strong> <?php echo $row['Email']; ?></p>
                <?php echo $row['Commission']; ?>
                <p><strong>Secondary Contact:</strong> <?php echo $customerDetails[$row['CID']]['contact']; ?></p>
                <p><strong>City:</strong> <?php echo $customerDetails[$row['CID']]['city']; ?></p>
                <p><strong>Street:</strong> <?php echo $customerDetails[$row['CID']]['street']; ?></p>
                <p><strong>Price:</strong> $<?php echo $row['Price']; ?></p>
                <p><strong>Line Items:</strong> <?php echo $row['LineItem']; ?></p>
                <p><strong>Secret Notes:</strong> <?php echo $row['SNote']; ?></p>
                <p><strong>Total:</strong> <?php echo $row['Total']; ?></p>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<script>
    function toggleDetails(qid) {
        const detailsRow = document.getElementById('details_' + qid);
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
        } else {
            detailsRow.style.display = 'none';
        }
    }
</script>
</body>
</html>
