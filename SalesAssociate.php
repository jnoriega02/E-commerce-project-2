<?php
include "DB_Secerts.php";
?>

<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

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
        
                // Call the redirect function after 5 seconds
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
        
                // Call the redirect function after 5 seconds
                setTimeout(redirection, 4000);
            </script>";
            exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charser="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css_files/style.css">
        <title>Sales Associate</title>
        <script>
			
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
		</style>
</head>
<body style = "background-color: rgba(0, 136, 255, 0.3);">
<div class="text-center">
        <header>
            <h1>Sales Associates</h1>
        </header>
        <h2>Plant Repair Services</h2>
    </div>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark py-3">
            <div class="container-fluid">
              <ul class="navbar-nav">
              <li class="nav-item">
                <div class = "right">
            <div class = "button"><button onclick = Logout()>Logout</button>
                </div>
            </div>
                </li>
              </ul>

              <div class="text-center">
       
            <span class="navbar-text ">Welcome, <?php echo $_SESSION['Username']; ?></span>
            </div>
            </div>
        </nav> 


    <form action="SalesAssociate.php" method="post">
    
    <label for="customer"><b>Select Customer:</b></label>
				<!-- make dropdown box for customers to show from customer legacy database -->
				<select name="customer" id="customer">
				<?php
						// Saving $pdo variable from DB_Secrets.php into the connection variable for the prepared statement
						$connection = $pdo;
						// Grabbing primary key "id" and name column from the legacy database
						$stmt = $connection->prepare("SELECT id, name, city, street, contact FROM customers ORDER BY name ASC");
						$stmt->execute();
						// While loop with fetch() to grab rows and display the row data one by one. This displays in the dropdown box
						while ($row = $stmt->fetch()) {
							$customerId = $row["id"];
							$customerName = $row["name"];
							$city = $row["city"];
							$street = $row["street"];
							$contact = $row["contact"];
			
							// Check if the current option is the selected one based on the submitted customer value
							$selected = isset($_POST["customer"]) && $_POST["customer"] === $customerId ? "selected" : "";
			
							echo "<option value='$customerId' data-city='$city' data-street='$street' data-contact='$contact' $selected>$customerName</option>";
						}
					
				?>
			</select>
            <br>
        <label for="email">Email(exisitng quotes for customers will have email updated to this)(required):</label>
        <input type="email" name="email" placeholder="123@yahoo.com" maxlength="40"  required>

        <br>
        <div id="lineItemsContainer">
            <div class="line-item">
                <label for="lineItem">Line Item(required):</label>
                <input type="text" name="lineItem[]" placeholder="garage door" maxlength="255" required>

                <label for="price">$Price(required):</label>
                <input type="text"  id="priceInput" name="price[]" placeholder="200.50" title="Only numerals allowed" required>
                <button type="button" onclick="addLineItem()">Add Another Line Item</button>
            </div>
        </div>

        <div id="SecretNoteContainer">
            <div class="secret-note">
                <label for="secretnotes">Secret Notes(optional):</label>
               <input type="text" id="secretnotes" name="secretnote[]" maxlength="255" placeholder="Big garage damage door"><br>
               <button type="button" onclick="addSecretNote()">Add Secret Note</button>
            </div>
        </div>

        		<b><p id="totalAmount">Total Amount: $0.00</p></b>
				<input type="hidden" name="total_amount" id="totalAmountHidden" value="0.00">
					<button id="footerBtn" type="submit" name="createBtn">Create Quote</button>
					<p> To Finalize Quote and submit to Headquaters, Click Here:<p>
					<button id="finalizefooterBtn" type="submit" name="finalizeBtn" onclick="return confirmSanction()"> Finalize Quote</button>
    
    </form>
    
    <script>
function confirmSanction() {
    return confirm("Are you sure you want to Finalize this line item and all other line items for this Quote?");
}
</script>

<?php
$stmt = $connection2->prepare("SELECT Name , Commissions FROM Employee WHERE EmpID = :empid");
$stmt->bindValue(':empid', $empId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $result['Name'];
$commision = $result['Commissions'];
?>

<h2><i>Total Commission for: <?php echo $name . " " . "$" . $commision;?> </i></h2><br>
<h2><i>Quotes Created By: <?php echo $name;?></i> <br></br></h2> <h2><i><u>Line Items and Prices for Customers below</u></i></h2>
    <h3>    <?php
    
            $connection2 = $p;
            $empId = $_SESSION["EmpID"];
    // Fetch the quotes from the QuotePurchase table
    $stmt = $connection2->prepare("SELECT QID, EmpID, CID, Price, LineItem, Total, DateEntered FROM QuotePurchase WHERE EmpID = :empid AND Qstatus = 1 ORDER BY Total ASC ");
    $stmt->bindValue(':empid', $empId, PDO::PARAM_INT);
    $stmt->execute();
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($quotes))
    {
        echo "No quotes Created by $name yet";
    }
    // Display the quotes along with customer names
    foreach ($quotes as $quote) {
        $grablineitem = $quote["LineItem"];
        $grabprice = $quote["Price"];
        $total = $quote['Total'];
        $qid = $quote['QID'];
        $Date = $quote['DateEntered'];
        // Fetch the customer name using the CID for each quote
        $cid = $quote['CID'];
        $customerName = getCustomerName($connection, $cid);

        
        echo "Customer: $customerName<br>";
        echo "Line Item: $grablineitem<br>";
        echo "Price(individual): $". $grabprice . "<br>"; 
        echo "Quote Total: $". $total . "<br>";
        echo "Date Created: ". $Date . "<br>";
        echo "<button class='edit-button' onclick='redirectToEdit($qid)'>Edit</button>";

        echo "<form method='post' style='display: inline-block;' onsubmit='return confirm(\"Are you sure you want to Finalize this line item and all line items for this quote?\");'>
              <input type='hidden' name='cidtofinalize' value='$cid'>
              <button type='submit' name='Finalizethequote'>Finalize Quote</button>
            </form>";
        
        echo"<br></br>";
    }

    // Function to fetch the customer name based on the CID
    function getCustomerName($pdo, $cid) {
        $stmt = $pdo->prepare("SELECT id,name FROM customers WHERE id = :cid");
        $stmt->bindValue(':cid', $cid, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['name'])) {
            return $result['name'];
        } else {
            return 'Unknown Customer';
        }
    }
        ?>
    </h3>

<script>
    function redirectToEdit(qid) {
        window.location.href = 'edit_quote.php?qid=' + qid;
    }
</script>

        <script>
    // Function to calculate and update the total amount
    function updateTotalAmount() {
        var priceInputs = document.getElementsByName("price[]");
        var totalAmount = 0;

        // Calculate the total amount by summing all the prices
        for (var i = 0; i < priceInputs.length; i++) {
            var price = parseFloat(priceInputs[i].value);
            if (!isNaN(price) && price >= 0) {
                totalAmount += price;
            }
        }

        // Update the content of the total amount element
        var totalAmountElement = document.getElementById("totalAmount");
        totalAmountElement.textContent = "Total Amount: $" + totalAmount.toFixed(2);
    }

    // Function to add event listener to price inputs
    function addPriceInputListener() {
        var priceInputs = document.getElementsByName("price[]");
        for (var i = 0; i < priceInputs.length; i++) {
            priceInputs[i].addEventListener("input", updateTotalAmount);
        }
    }

    function addLineItem() {
        const container = document.getElementById('lineItemsContainer');
        const lineItemDiv = document.createElement('div');
        lineItemDiv.classList.add('line-item');

        lineItemDiv.innerHTML = `
            <label for="lineItem">Line Item(required):</label>
            <input type="text" name="lineItem[]" placeholder="garage door" maxlength="255" required>

            <label for="price">Price(required):</label>
            <input type="text"  id="priceInput" name="price[]" placeholder="200.50" title="Only numerals allowed" required>
            <button type="button" onclick="removeLineItem(this)">Delete</button>
        `;

        container.appendChild(lineItemDiv);
        // Calculate and update the total amount when a new line item is added
        updateTotalAmount();

        // Add event listener to the new price inputs
        addPriceInputListener();
    }

    function removeLineItem(button) {
        const lineItemDiv = button.parentElement;
        lineItemDiv.remove();
        updateTotalAmount();
    }

    // Add event listener to the initial price inputs
    addPriceInputListener();
</script>


<script>
  document.getElementById('priceInput').addEventListener('input', function () {
    var inputVal = this.value;
    if (inputVal.trim() !== '' && (!/^\d+(\.\d{1,2})?$/.test(inputVal) || inputVal.length > 8)) 
    {
      this.setCustomValidity('Please enter a number with up to two decimal places, non-negative, and no more than 7 digits.');
    } else {
      this.setCustomValidity('');
    }
  });
</script>


<script>
        function addSecretNote() {
            const container = document.getElementById('SecretNoteContainer');
            const secretnoteDiv = document.createElement('div');
            secretnoteDiv.classList.add('secret-note');

            secretnoteDiv.innerHTML = `
            <label for="secretnotes">Secret Notes(optional):</label>
            <input type="text" id="secretnotes" name="secretnote[]" placeholder="Big garage damage door" title="Press Delete for extra box if no input is entered" 
            maxlength="255" 
            required
            oninvalid="this.setCustomValidity('Please enter a secret note or delete this field.');"
            oninput="this.setCustomValidity('');">
            <button type="button" onclick="removeLineItem(this)">Delete</button>
            `;

            container.appendChild(secretnoteDiv);
        }

        function removeLineItem(button) {
            const secretnoteDiv = button.parentElement;
            secretnoteDiv.remove();
        }
</script>



<script>
  

        // Function to update the hidden total amount input field
        function updateHiddenTotalAmount(totalAmount) {
            var hiddenTotalAmountInput = document.getElementById("totalAmountHidden");
            hiddenTotalAmountInput.value = totalAmount.toFixed(2);
        }


        // Add event listener to the "Create Quote" button
        var createQuoteBtn = document.querySelector("button[name='createBtn']");
        createQuoteBtn.addEventListener("click", function() {
            // Get the total amount from the displayed element
            var totalAmountElement = document.getElementById("totalAmount");
            var totalAmount = parseFloat(totalAmountElement.textContent.replace("Total Amount: $", ""));

            // Update the hidden input field with the total amount
            updateHiddenTotalAmount(totalAmount);
        });

        // Add event listener to the "Finalize Quote" button
        var finalizeBtn = document.querySelector("button[name='finalizeBtn']");
        finalizeBtn.addEventListener("click", function() {
            // Get the total amount from the displayed element
            var totalAmountElement = document.getElementById("totalAmount");
            var totalAmount = parseFloat(totalAmountElement.textContent.replace("Total Amount: $", ""));

            // Update the hidden input field with the total amount
            updateHiddenTotalAmount(totalAmount);
        });
</script>

</body>
</html>


<?php
$connection2 = $p;

// Get the data from the HTML form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(isset($_POST["createBtn"]))
    {
    $lineItems = $_POST["lineItem"];
    $prices = $_POST["price"];
    $customerId = $_POST["customer"];
    $email = $_POST["email"]; 
    $secretNotes = $_POST["secretnote"];
    $totalAmount = $_POST["total_amount"];
    $empId = $_SESSION["EmpID"];
    $qStatus = "unresolved";
    $discount = isset($_POST["discount"]) && $_POST["discount"] !== '' ? floatval($_POST["discount"]) : null;
    
     // Check if a quote already exists for the customer
     $quoteExists = false;
     $existingTotal = 0;
     $existingQuoteId = null;
      // Query to check if a quote exists
    $quoteCheckSql = "SELECT QID, Total FROM QuotePurchase WHERE CID = :cid AND Qstatus = :qstatus";
    $quoteCheckStmt = $connection2->prepare($quoteCheckSql);
    $quoteCheckStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
    $quoteCheckStmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
    $quoteCheckStmt->execute();
    if ($quoteCheckStmt->rowCount() > 0) {
        $quoteExists = true;
        $existingQuoteData = $quoteCheckStmt->fetch(PDO::FETCH_ASSOC);
        $existingTotal = $existingQuoteData["Total"];
        $existingQuoteId = $existingQuoteData["QID"];
    }
    if ($quoteExists) 
    {
        // Update the existing quote's Total amount by adding the new Total Amount
        $newTotal = $existingTotal + $totalAmount;
        
        $updateSql = "UPDATE QuotePurchase SET Total = :newTotal, Email = :email WHERE CID = :cid";
        $updateStmt = $connection2->prepare($updateSql);
        $updateStmt->bindParam(':newTotal', $newTotal, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $updateStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        for ($i = 0; $i < count($lineItems); $i++) {
            $lineItem = isset($lineItems[$i]) && $lineItems[$i] !== '' ? trim($lineItems[$i]) : null;
            $price = isset($prices[$i]) && $prices[$i] !== '' ? floatval($prices[$i]) : null; 
    
            $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;
    
            $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Total, CID, Email, Discount, Qstatus, EmpID) 
                    VALUES (:lineItem, :snote, :price, :total , :cid , :email, :discount, :qstatus , :empid)";
             $stmt = $connection2->prepare($sql);
             $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
             $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
             $stmt->bindParam(':price', $price, PDO::PARAM_STR);
             $stmt->bindParam(':total', $newTotal, PDO::PARAM_STR);
             $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
             $stmt->bindParam(':email', $email, PDO::PARAM_STR);
             $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
             $stmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
             $stmt->bindParam(':empid', $empId, PDO::PARAM_INT);
     
             if ($stmt->execute() && $updateStmt->execute()) 
             {
                echo "Quote updated successfully. New total: $newTotal <br>";
                 echo "Quote inserted successfully: $lineItem, $price <br>";
                 echo "<script>
                function redirection() {
                    window.location.href = document.referrer;
                }
        
                // Call the redirect function after 5 seconds
                setTimeout(redirection, 100);
            </script>";
             } 
             else 
             {
                 echo "Error: " . $stmt->errorInfo()[2] . "<br>";
             }
        }
        
    
    }

    else {
    // Prepare and execute the SQL query for each line item and price
    for ($i = 0; $i < count($lineItems); $i++) 
    {
        $lineItem = isset($lineItems[$i]) && $lineItems[$i] !== '' ? trim($lineItems[$i]) : null;
        $price = isset($prices[$i]) && $prices[$i] !== '' ? floatval($prices[$i]) : null; 

        $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;

        $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Total, CID, Email, Discount, Qstatus, EmpID) 
                VALUES (:lineItem, :snote, :price, :total , :cid , :email, :discount, :qstatus , :empid)";
         $stmt = $connection2->prepare($sql);
         $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
         $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
         $stmt->bindParam(':price', $price, PDO::PARAM_STR);
         $stmt->bindParam(':total', $totalAmount, PDO::PARAM_STR);
         $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
         $stmt->bindParam(':email', $email, PDO::PARAM_STR);
         $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
         $stmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
         $stmt->bindParam(':empid', $empId, PDO::PARAM_INT);
 
         if ($stmt->execute()) 
                {
             echo "Quote inserted successfully: $lineItem, $price <br>";
             echo "<script>
            function redirection() {
                window.location.href = document.referrer;
            }
    
            // Call the redirect function after 5 seconds
            setTimeout(redirection, 100);
        </script>";
                } 
         else 
         {
             echo "Error: " . $stmt->errorInfo()[2] . "<br>";
         }
    }
        }
    }

    elseif (isset($_POST["finalizeBtn"])) {
    $lineItems = $_POST["lineItem"];
    $prices = $_POST["price"];
    $customerId = $_POST["customer"];
    $email = $_POST["email"]; 
    $secretNotes = $_POST["secretnote"];
    $totalAmount = $_POST["total_amount"];
    $empId = $_SESSION["EmpID"];
    $qStatus = "finalized";
    $discount = isset($_POST["discount"]) && $_POST["discount"] !== '' ? floatval($_POST["discount"]) : null;
    
    // Check if a quote already exists for the customer
    $quoteExists = false;
    $existingTotal = 0;
    $existingQuoteId = null;
     // Query to check if a quote exists
   $quoteCheckSql = "SELECT QID, Total FROM QuotePurchase WHERE CID = :cid ";
   $quoteCheckStmt = $connection2->prepare($quoteCheckSql);
   $quoteCheckStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
   $quoteCheckStmt->execute();
   if ($quoteCheckStmt->rowCount() > 0) 
        {
       $quoteExists = true;
       $existingQuoteData = $quoteCheckStmt->fetch(PDO::FETCH_ASSOC);
       $existingTotal = $existingQuoteData["Total"];
       $existingQuoteId = $existingQuoteData["QID"];
        }
   if ($quoteExists) 
   {
       // Update the existing quote's Total amount by adding the new Total Amount
       $newTotal = $existingTotal + $totalAmount;
       
       $updateSql = "UPDATE QuotePurchase SET Total = :newTotal , Qstatus = :qstatus WHERE CID = :cid";
       $updateStmt = $connection2->prepare($updateSql);
       $updateStmt->bindParam(':newTotal', $newTotal, PDO::PARAM_STR);
       $updateStmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
       $updateStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
       for ($i = 0; $i < count($lineItems); $i++) 
       {
        $lineItem = isset($lineItems[$i]) && $lineItems[$i] !== '' ? trim($lineItems[$i]) : null;
        $price = isset($prices[$i]) && $prices[$i] !== '' ? floatval($prices[$i]) : null;
   
           $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;
   
           $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Total, CID, Email, Discount, Qstatus, EmpID) 
                   VALUES (:lineItem, :snote, :price, :total , :cid , :email, :discount, :qstatus , :empid)";
            $stmt = $connection2->prepare($sql);
            $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
            $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':total', $newTotal, PDO::PARAM_STR);
            $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
            $stmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
            $stmt->bindParam(':empid', $empId, PDO::PARAM_INT);
    
            if ($stmt->execute() && $updateStmt->execute()) 
                    {
               echo "Quote updated successfully. New total: $newTotal <br>";
                echo "Quote inserted successfully: $lineItem, $price <br>";
                echo "<script>
               function redirection() {
                   window.location.href = document.referrer;
               }
       
               // Call the redirect function after 5 seconds
               setTimeout(redirection, 100);
           </script>";
                    } 
            else 
            {
                echo "Error: " . $stmt->errorInfo()[2] . "<br>";
            }
       }
       
   
   }

   else {
   // Prepare and execute the SQL query for each line item and price
   for ($i = 0; $i < count($lineItems); $i++) 
   {
    $lineItem = isset($lineItems[$i]) && $lineItems[$i] !== '' ? trim($lineItems[$i]) : null;
    $price = isset($prices[$i]) && $prices[$i] !== '' ? floatval($prices[$i]) : null; 

       $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;

       $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Total, CID, Email, Discount, Qstatus, EmpID) 
               VALUES (:lineItem, :snote, :price, :total , :cid , :email, :discount, :qstatus , :empid)";
        $stmt = $connection2->prepare($sql);
        $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
        $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':total', $totalAmount, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
        $stmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
        $stmt->bindParam(':empid', $empId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Quote inserted successfully: $lineItem, $price <br>";
            echo "<script>
           function redirection() {
               window.location.href = document.referrer;
           }
   
           // Call the redirect function after 5 seconds
           setTimeout(redirection, 100);
       </script>";
                            } 
        else 
        {
            echo "Error: " . $stmt->errorInfo()[2] . "<br>";
        }
   }
       }
                        }

elseif (isset($_POST['Finalizethequote'])) 
    {
                        $cidtofinalize = $_POST['cidtofinalize']; 
                        $stmtsanct = $connection2->prepare("UPDATE QuotePurchase SET Qstatus = 3 WHERE CID = :cid AND Qstatus = 1");
                        $stmtsanct->bindParam(':cid', $cidtofinalize, PDO::PARAM_INT);
                        $stmtsanct->execute();
                        echo "<script>
                        function redirection() {
                            window.location.href = document.referrer;
                        }
                        
                        // Call the redirect function after 5 seconds
                        setTimeout(redirection, 100);
                        </script>";
    }
                              
    
}
?>
