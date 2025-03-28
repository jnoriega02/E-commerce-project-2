<?php
include "DB_Secerts.php";
?>


<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
// Check if the user is logged in (username is stored in the session)
if (!isset($_SESSION["Username"]) || !isset($_SESSION["EmpID"]) || $_SESSION["Position"] !== "ThirdFace")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Third Interface member to access this page. Redirecting to login page...</h1>";
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
        echo "<h1>Must be a Third Interface member to access this page. Redirecting to login page...</h1>";
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

if(!$result || $result['Position'] !== "ThirdFace")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a Third Interface member to access this page. Redirecting to login page...</h1>";
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
    function tipage()
                {
                    window.location.href = "Third_interface.php";
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
            <div class = "button"><button onclick = tipage()>Third Interface page</button>
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
<head>
<style>
.form-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Style for input fields */
.form-container input[type="email"],
.form-container input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Style for radio buttons */
.form-container input[type="radio"] {
    margin-right: 5px;
}

/* Style for the quote total */
#quoteTotal {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

/* Style for submit button */
.form-container input[type="submit"] {
    display: block;
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

/* Hover effect for submit button */
.form-container input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Additional styles for labels and paragraph */
.form-container label {
    display: inline-block;
    margin-right: 10px;
}

.form-container p {
    margin-top: 20px;
    font-weight: bold;
}
</style>
</head>
<?php

$connection2 = $p;
if (isset($_GET['qid'])) {
    $qid = $_GET['qid'];

    // Fetch the quote details from the QuotePurchase table based on the QID
    $stmt = $connection2->prepare("SELECT * FROM QuotePurchase WHERE QID = :qid AND Qstatus = 2 ");
    $stmt->bindValue(':qid', $qid, PDO::PARAM_INT);
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
        $Discount = $quote['Discount'];

        // Display the fetched data in editable input fields
        echo "<div class='form-container'>";
        echo "    <form method='post'>";
        echo "        <input type='hidden' name='qid' value='$qid'>";
        echo "        <input type='hidden' name='cid' value='$cid'>";
        echo "        <input type='hidden' name='total' value='$total' id='hiddenTotal'>";

        echo "        Email(Cannot edit): <input type='email' name='email' value='$email' maxlength='255' readonly><br>";
        echo "        Line Item(Cannot edit): <input type='text' name='lineItem' value='$lineItem' maxlength='255' readonly><br>";
        echo "        Secret Note(Cannot edit): <input type='text' name='sNote' value='$sNote' readonly><br>";
        echo "        Price(Cannot edit): <input type='text' name='price' value='$price' id='priceInput' oninput='updateTotal()' readonly><br>";
        echo "        Discount: <input type='text' name='discountInput' id='discountInput' oninput='updateTotal()'><br>";
        echo "        <label><input type='radio' name='discountType' value='percentage' checked> Percentage</label>";
        echo "        <label><input type='radio' name='discountType' value='amount'> Amount</label><br>";
        echo "        <span id='quoteTotal'>Quote Total: $$total</span>";

        echo "        <br><input type='submit' value='Update Quote'>";
        echo "    </form>";
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
      echo "<script>
        document.getElementById('discountInput').addEventListener('input', function () {
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

<script>
    function updateTotal() {
        var initialTotal = parseFloat(<?php echo $total; ?>);
        var newPrice = parseFloat(document.getElementById("priceInput").value);

        // Set newPrice to 0 if it's NaN (empty)
        if (isNaN(newPrice)) {
            newPrice = 0;
        }

        // Calculate the change in total based on new price and initial price
        var total = initialTotal - (<?php echo $price; ?> - newPrice);

        // Calculate discount based on selected radio button
        var discountInput = document.getElementById("discountInput").value;
        var discountType = document.querySelector('input[name="discountType"]:checked');

        // Ensure that discountInput is a valid number or an empty string
        if (discountInput === '' || !isNaN(parseFloat(discountInput))) {
            if (discountType) {
                discountType = discountType.value;

                if (discountType === 'percentage') {
                    // Calculate discount only if discountInput is a valid number
                    if (!isNaN(parseFloat(discountInput))) {
                        total *= (1 - (parseFloat(discountInput) / 100));
                    }
                } else if (discountType === 'amount') {
                    // Subtract discount only if discountInput is a valid number
                    if (!isNaN(parseFloat(discountInput))) {
                        total -= parseFloat(discountInput);
                    }
                }
            }
        }

        document.getElementById("quoteTotal").innerHTML = "Quote Total: $" + total.toFixed(2);
        document.getElementById("hiddenTotal").value = total.toFixed(2);
    }

    // Add event listeners to the radio buttons to trigger the updateTotal() function
    var discountRadioButtons = document.querySelectorAll('input[name="discountType"]');
    discountRadioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('change', updateTotal);
    });
</script>


<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $cid = $_POST['cid'];
    $qid = $_POST['qid'];
    $lineItem = isset($_POST['lineItem']) && $_POST["lineItem"] !== '' ? trim($_POST['lineItem']) : null;
    $sNote = isset($_POST['sNote']) && $_POST["sNote"] !== '' ? trim($_POST['sNote']) : null;
    $price = isset($_POST["price"]) && $_POST["price"] !== '' ? floatval($_POST["price"]) : null;
    $total = floatval($_POST['total']);
    $discount = isset($_POST["discountInput"]) && $_POST["discountInput"] !== '' ? floatval($_POST["discountInput"]) : null;

    // Update the QuotePurchase table
    $stmt = $connection2->prepare("UPDATE QuotePurchase SET LineItem = :lineItem, SNote = :sNote, Price = :price, Total = :total WHERE QID = :qid");
    $stmt->bindValue(':lineItem', $lineItem, PDO::PARAM_STR);
    $stmt->bindValue(':sNote', $sNote, PDO::PARAM_STR);
    $stmt->bindValue(':price', $price, PDO::PARAM_STR);
    $stmt->bindValue(':total', $total, PDO::PARAM_STR);
    $stmt->bindValue(':qid', $qid, PDO::PARAM_INT);

      // Update the QuotePurchase table
      $stmt2 = $connection2->prepare("UPDATE QuotePurchase SET Total = :total, Discount = :discount WHERE CID = :cid");
      $stmt2->bindValue(':total', $total, PDO::PARAM_STR);
      $stmt2->bindValue(':discount', $discount, PDO::PARAM_STR);
      $stmt2->bindValue(':cid', $cid, PDO::PARAM_INT);

    if ($stmt->execute() && $stmt2->execute()) {
        echo "<h2> Updated successfully! Redirecting back to Third Interface page...</h2>";
        echo "<script>
            function redirection() {
                window.location.href = 'Third_interface.php';
            }
    
            // Call the redirect function
            setTimeout(redirection, 400);
        </script>";
        exit();
    } else {
        
        echo "Error: " . $stmt->errorInfo()[2] . "<br>";
    }
}
?>

</body>
</html>
