<?php
include "DB_Secerts.php";
?>

<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

$connection2 = $p;
$empId = $_SESSION['EmpID'];
$stmt = $connection2->prepare("SELECT Position From Employee WHERE EmpID = :empid");
if(!$stmt)
{    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Sales Associate to enter Quotes. Redirecting to login page...</h1>";
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

if(!$result || $result['Position'] !== "SalesAssoc")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Sales Associate to enter Quotes. Redirecting to login page...</h1>";
        echo "<script>
                function redirection() {
                    window.location.href = 'login.php?t={$cacheBuster}';
                }
        
                // Call the redirect function
                setTimeout(redirection, 4000);
            </script>";
            exit();
}

// Check if the user is logged in (username is stored in the session)
if (!isset($_SESSION["Username"]) || !isset($_SESSION["EmpID"]) || $_SESSION["Position"] !== "SalesAssoc")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Sales Associate to enter Quotes. Redirecting to login page...</h1>";
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
<html>
<head>
	<meta charser="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css_files/style.css">
        <title>Quote Edit</title>
        <script>
    function sapage()
                {
                    window.location.href = "SalesAssociate.php";
                }
        </script>
		</style>
</head>
<body style = "background-color: rgba(0, 136, 255, 0.3);">
<nav class="navbar navbar-expand-sm bg-dark navbar-dark py-3">
            <div class="container-fluid">
              <ul class="navbar-nav">
              <li class="nav-item">
                <div class = "right">
            <div class = "button"><button onclick = sapage()>Sales Associate page</button>
                </div>
            </div>
                </li>
              </ul>
            </div>
        </nav> 
        
<?php
    // Check if $_SESSION['EmpID'] is set and not false before using it
    if (isset($_SESSION['Username']) && $_SESSION['Username'] !== false) {
        echo "<h1>Welcome, ". $_SESSION['Username'] . "!</h1>";
    }
?>

<?php

$connection2 = $p;
if (isset($_GET['qid'])) {
    $qid = $_GET['qid'];
    $empId = $_SESSION["EmpID"];

    // Fetch the quote details from the QuotePurchase table based on the QID
    $stmt = $connection2->prepare("SELECT * FROM QuotePurchase WHERE QID = :qid ANd EmpID = :empid ");
    $stmt->bindValue(':qid', $qid, PDO::PARAM_INT);
    $stmt->bindValue(':empid', $empId, PDO::PARAM_INT);
    $stmt->execute();
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($quote) {
        // Extract the quote details from the fetched data
        $lineItem = $quote['LineItem'];
        $sNote = $quote['SNote'];
        $price = $quote['Price'];
        $cid = $quote['CID'];
        $email = $quote['Email'];
        $total = $quote['Total'];

        // Display the fetched data in editable input fields
        echo "<form action='edit_quote.php' method='post'>";
        echo "<input type='hidden' name='qid' value='$qid'>"; // Include the QID as a hidden field to identify the quote being updated
        echo "<input type='hidden' name='cid' value='$cid'>";
        echo "<input type='hidden' name='total' value='$total'>"; 

        echo "Email(Changes for every line item pertaining to this customer): <input type='email' name='email' value='$email' maxlength='255' required><br></br>";
        echo "Line Item: <input type='text' name='lineItem' value='$lineItem' maxlength='255' required><br></br>";
        echo "Secret Note: <input type='text' name='sNote' value='$sNote'><br></br>";
        echo "Price: <input type='text' name='price' value='$price' id='priceInput' required><br></br>";
        echo "Quote Total(Changes for every line item pertaining to this customer): $". $total;
        // Display other fields as needed (CID, email, discount, etc.)

        echo "<br></br><input type='submit' value='Update Quote'>";
        echo "</form>";
        echo "<p><b>Refresh Page to clear mistaken entry</p></b>";
        echo "<script>
        document.getElementById('priceInput').addEventListener('input', function () {
          var inputVal = this.value;
          if (inputVal.trim() !== '' && (!/^\d+(\.\d{1,2})?$/.test(inputVal) || inputVal.length > 8)) 
          {
            this.setCustomValidity('Please enter a number with up to two decimal places, non-negative, and no more than 7 digits.');
          } else {
            this.setCustomValidity('');
          }
        });
      </script>";
    
    } else {
        echo "Quote not found.";
    }
} else {
    echo ".";
}
?>

<?php
$connection2 = $p;
// Assuming you have the necessary database credentials and connection established as shown in your existing code.

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['qid'])) {
    $empId = $_SESSION["EmpID"];
    $qid = $_POST['qid'];
    $lineItem = trim($_POST['lineItem']);
    $sNote =  isset($_POST["sNote"]) && $_POST["sNote"] !== '' ? trim($_POST["sNote"]) : null;
    $price = floatval($_POST['price']);
    $email = trim($_POST['email']);
    $cid = $_POST['cid'];

    // Fetch the existing price and total amount from the QuotePurchase table
    $fetchDataQuery = "SELECT Price, Total FROM QuotePurchase WHERE QID = :qid AND EmpID = :empid";
    $stmtFetchData = $connection2->prepare($fetchDataQuery);
    $stmtFetchData->bindParam(':qid', $qid, PDO::PARAM_INT);
    $stmtFetchData->bindParam(':empid', $empId, PDO::PARAM_INT);
    $stmtFetchData->execute();
    $quoteData = $stmtFetchData->fetch(PDO::FETCH_ASSOC);
    
    if ($quoteData){
        
        $existingPrice = floatval($quoteData['Price']);
        $existingTotalAmount = floatval($_POST['total']);
        $newPrice = floatval($price);
         // Update the TotalAmount column based on whether the new price is higher or lower
        // If the new price is equal to the existing price, the TotalAmount remains unchanged.
        $totalAmount = ($newPrice > $existingPrice) ? $existingTotalAmount + ($newPrice - $existingPrice) : $existingTotalAmount - ($existingPrice - $newPrice);

    // Prepare and execute the SQL query to update the quote details in the QuotePurchase table
    $sql = "UPDATE QuotePurchase SET LineItem = :lineItem, SNote = :sNote, Price = :price WHERE QID = :qid";
    $stmt = $connection2->prepare($sql);
    $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
    $stmt->bindParam(':sNote', $sNote, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':qid', $qid, PDO::PARAM_INT);

    $sql2 = "UPDATE QuotePurchase SET Email = :email, Total = :total WHERE CID = :cid";
    $stmt2 = $connection2->prepare($sql2); 
    $stmt2->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt2->bindParam(':total', $totalAmount, PDO::PARAM_STR);  
    $stmt2->bindParam(':cid', $cid, PDO::PARAM_INT); 
    if ($stmt->execute() && $stmt2->execute()) { 
        echo "<h2> Updated successfully! Redirecting back to Sales Associate page...</h2>";
        echo "<script>
            function redirection() {
                window.location.href = 'SalesAssociate.php';
            }
    
            // Call the redirect function
            setTimeout(redirection, 3000);
        </script>";
        exit();
    } else {
        echo "Error updating quote: " . $stmt->errorInfo()[2];
    }

}

else{
    echo "Error fetching existing data.Redirecting back to Sales Associate page...";
    echo "<script>
            function redirection() {
                window.location.href = 'SalesAssociate.php';
            }
    
            // Call the redirect function
            setTimeout(redirection, 3000);
        </script>";
        exit();
}
    
}
?>


</body>
</html>