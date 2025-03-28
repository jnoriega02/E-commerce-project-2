<?php
include "DB_Secerts.php"; 
?>

<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
// Check if the user is logged in (username is stored in the session)
if (!isset($_SESSION["Username"]) || !isset($_SESSION["EmpID"]) || $_SESSION["Position"] !== "HeadQ")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a HeadQuarters member to Sanction Quotes. Redirecting to login page...</h1>";
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
        echo "<h1>Must be a HeadQuarters member to Sanction Quotes. Redirecting to login page...</h1>";
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

if(!$result || $result['Position'] !== "HeadQ")
{
    session_unset();    // Unset all session variables
    session_destroy();  // Destroy the session data
    $cacheBuster = time();
        // Set a message
        $_SESSION["message"] = "You don't have the required position to access this page.";
        echo "<h1>Must be a HeadQuarters member to Sanction Quotes. Redirecting to login page...</h1>";
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


<!DOCTYPE hmtl> 
<html lang= "en">
<head>

    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css_files/style.css">
        <title>Headquarters</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
</head>
    <body style = "background-color: rgba(0, 136, 255, 0.3);">
    <div class="text-center">
        <header>
            <h1>Headquarters</h1>
        <header>
            <h2>Plant Repair Services</h2>
    </div>
<!--Gives background to navigation bar at the top of the page-->
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark py-3">
        
            <div class="container-fluid">
              <ul class="navbar-nav">
              <li class="nav-item">
                <div class = "right">
            <div class = "button"><button onclick = Logout()>Logout</button></div>
                </div>
         </li>
              </ul>
              <div class="text-center ">
       
            <span class="navbar-text ">Welcome, <?php echo $_SESSION['Username']; ?></span>
            </div>

            </div>
        </nav>           

        <br></br><h3><u><b>Fields For Finalized Quotes</b></u></h3>
    
        <form method="post" action="Headquarter.php">
    <h3>    <?php
    
            $connection = $pdo;
            $connection2 = $p;
            $empId = $_SESSION["EmpID"];
    // Fetch the quotes from the QuotePurchase table
    $stmt = $connection2->prepare("SELECT QID, EmpID, CID, Price, LineItem, Total, Qstatus, DateEntered FROM QuotePurchase WHERE Qstatus = 'finalized' ORDER BY Total DESC ");
    $stmt->execute();
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($quotes))
    {
        echo "Wating for Finalized Quotes to be created...";
    }
    if (!empty($quotes) && isset($quotes[0]['EmpID'])) {
        $empIdFromQuery = $quotes[0]['EmpID'];
        echo '<input type="hidden" name="getempid" value="' . $empIdFromQuery . '">';

        echo"<label for=\"finalized_quotes\"><b>Finalized Customer Quotes:</b></label>";
        echo "<select name=\"finalized_quotes\"> id=finalized_quotes";
        $finalizedCustomerCIDs = array();
    
    foreach ($quotes as $quote) {
        if ($quote['Qstatus'] === 'finalized') {
            $cid = $quote['CID'];
            if (!in_array($cid, $finalizedCustomerCIDs)) {
                $finalizedCustomerCIDs[] = $cid;
                $customerName = getCustomerName($connection, $cid);
                echo "<option value='$cid'>$customerName</option>";
            }
        }
    }
    }
   

echo "</select>";


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

        <br>
        <?php
        $stmt = $connection2->prepare("SELECT Email FROM QuotePurchase WHERE Qstatus = 'finalized' ORDER BY Total DESC ");
        $stmt->execute();
        $mail = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($mail)
        {
            foreach($mail as $mails)
            {
            $email = $mails["Email"];
            //Add email as hidden input to display for customer
          echo "<input type='hidden' name='email' value=\"$email\">";
            }
        }

        else{
            echo "";
        }

        
        ?>

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
        
        <div class="discount-wrapper">
    				<br></br>
    				<label for="discount">Discount(optional):</label>
    				<input type="text" placeholder="10" name="discount" id="discount" pattern="^\d*(?:\.\d{0,2})?$" title="Only numerals allowed">

					<label for="percentageDiscount">Percentage</label>
    				<input type="radio" name="discountType" value="percentage" id="percentageDiscount" checked>
    
    				<label for="amountDiscount">Amount</label>
    				<input type="radio" name="discountType" value="amount" id="amountDiscount">
		</div>

        		<p id="totalAmount">Total Amount: $0.00</p>
				<input type="hidden" name="total_amount" id="totalAmountHidden" value="0.00">
					<button id="footerBtn" type="submit" name="createBtn">Add Entry Line Item Entry</button>
					<p> To Finalize Quote and submit to Headquaters, Click Here:<p>
					<button id="finalizefooterBtn" type="submit" name="finalizeBtn" onclick="return confirmSanction()"> Sanction Quote</button>
        <br></br><br></br>
</form>

<script>
function confirmSanction() {
    return confirm("Are you sure you want to Sanction this line item and all other line items for this Quote?");
}
</script>

    <h2><i><u>Finalized Line Items and Prices for Customers below</u></i></h2>
    <h3>    <?php
    
            $connection = $pdo;
            $connection2 = $p;
            $empId = $_SESSION["EmpID"];
    // Fetch the quotes from the QuotePurchase table
    $stmt = $connection2->prepare("SELECT QID, EmpID, Email, CID, Price, LineItem, Total, DateEntered FROM QuotePurchase WHERE Qstatus = 'finalized' ORDER BY Total ASC ");
    $stmt->execute();
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($quotes))
    {
        echo "No Finalized Quotes Available";
    }

    // Display the quotes along with customer names
    foreach ($quotes as $quote) {
        
        $grablineitem = $quote["LineItem"];
        $grabemail = $quote['Email'];
        $grabempid = $quote["EmpID"];
        $grabprice = $quote["Price"];
        $total = $quote['Total'];
        $qid = $quote['QID'];
        $Date = $quote['DateEntered'];
        // Fetch the customer name using the CID for each quote
        $cid = $quote['CID'];
        $customerName = getCustomerName($connection, $cid);

        $stmt3 = $connection2->prepare("SELECT Name FROM Employee WHERE EmpID = :empid");
        $stmt3->bindValue(':empid', $grabempid, PDO::PARAM_INT);
        $stmt3->execute();
        $result = $stmt3->fetch(PDO::FETCH_ASSOC);
        $name = $result['Name'];
        
        echo "Created by: $name<br>";
        echo "Customer: $customerName<br>";
        echo "Line Item: $grablineitem<br>";
        echo "Price(individual): $". $grabprice . "<br>"; 
        echo "Quote Total: $". $total . "<br>";
        echo "Date Created: ". $Date . "<br>";
        echo "<button type='button' onclick='window.location.href=\"editforHeadQ_quote.php?qid=$qid&empid=$grabempid&cid=$cid\";'>Edit</button>";

        echo "<form method='post' style='display: inline-block;' onsubmit='return confirm(\"Are you sure you want to delete this quote?\");'>
                <input type='hidden' name='qid_to_delete' value='$qid'>
                <input type='hidden' name='emailsent' value='$grabemail'>
                <input type='hidden' name='cidtosanction' value='$cid'>
                <input type='hidden' name='totalbeforedelete' value='$total'>
                <input type='hidden' name='pricebeforedelete' value='$grabprice'>
                <button type='submit' name='delete_quote'>Delete</button>
              </form>";

              echo "<form method='post' style='display: inline-block;' onsubmit='return confirm(\"Are you sure you want to sanction this line item and all line items for this quote?\");'>
              <input type='hidden' name='qid_to_delete' value='$qid'>
              <input type='hidden' name='cidtosanction' value='$cid'>
              <input type='hidden' name='emailsent' value='$grabemail'>
              <button type='submit' name='FinalizeSanction_quote'>Sanction Quote</button>
            </form>";

        echo "<br></br>";
    }

?>
        </h3>


<script>
         // Function to calculate the discount amount
		 function calculateDiscount(totalAmount, discountType, discountValue) {
            if (discountType === "percentage") {
                // Convert the percentage to a decimal
                var percentageDiscount = discountValue / 100;
                return totalAmount * percentageDiscount;
            } else if (discountType === "amount") {
                return discountValue;
            }
            return 0;
        }
		
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

			 // Apply the discount (if any)
			 var discountInput = document.getElementById("discount");
			 var percentageDiscount = document.getElementById("percentageDiscount");
        	 var amountDiscount = document.getElementById("amountDiscount");

        	var discountType = percentageDiscount.checked ? "percentage" : "amount";
        	var discountValue = parseFloat(discountInput.value) || 0;

        	var discountAmount = calculateDiscount(totalAmount, discountType, discountValue);
			
			totalAmount -= discountAmount;


			if(totalAmount <= 0)
			{
				totalAmount = 0;
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

// Add event listeners to the discount input and radio buttons
var discountInput = document.getElementById("discount");
var percentageDiscount = document.getElementById("percentageDiscount");
var amountDiscount = document.getElementById("amountDiscount");

function applyDiscount() {
  var value = discountInput.value;
  if (!value.match(/^\d*(?:\.\d{0,2})?$/)) {
      // If the input does not match the pattern for a valid discount (optional decimal with up to two decimal places)
      // set it to an empty string to avoid invalid input
      discountInput.value = "";
  }
  updateTotalAmount();
}

discountInput.addEventListener("input", applyDiscount);
percentageDiscount.addEventListener("click", applyDiscount);
amountDiscount.addEventListener("click", applyDiscount);

</script>

<script>
    const deleteButtons = document.querySelectorAll(".deleteButton");
    
    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            if (confirm("Are you sure you want to delete this Line Item/Price?")) {
                const form = button.closest("form");
                if (form) {
                    form.submit();
                }
            }
        });
    });
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

<?php
$connection2 = $p;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(isset($_POST["createBtn"]))
    {
        //code to make sure you cannot add send input unless a customer is selected
        if(!isset($_POST["finalized_quotes"]) || !isset($_POST["email"]) || !isset($_POST["getempid"]))
        {
            echo "<script>
            alert('***Cant Add For Quote that does not exist.*** Returning to Headquarter page..');
            function redirection() {
                window.location.href = document.referrer;
            }

            setTimeout(redirection, 100);
            </script>";
        }

        else{

    $lineItems = $_POST["lineItem"];
    $prices = $_POST["price"];
    $email = $_POST["email"];
    $customerId = $_POST["finalized_quotes"];
    $secretNotes = $_POST["secretnote"];
    $totalAmount = $_POST["total_amount"];
    $empId = $_SESSION["EmpID"];
    $grabbedempid = $_POST["getempid"];
    $qStatus = "finalized";
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
        
        $updateSql = "UPDATE QuotePurchase SET Total = :newTotal  WHERE CID = :cid";
        $updateStmt = $connection2->prepare($updateSql);
        $updateStmt->bindParam(':newTotal', $newTotal, PDO::PARAM_STR);
        $updateStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        for ($i = 0; $i < count($lineItems); $i++) {
            $lineItem = trim($lineItems[$i]);
            $price = floatval($prices[$i]); 
    
            $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;
    
            $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Email, Total, CID, Discount, Qstatus, EmpID) 
                    VALUES (:lineItem, :snote, :price, :email, :total , :cid , :discount, :qstatus , :empid)";
             $stmt = $connection2->prepare($sql);
             $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
             $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
             $stmt->bindParam(':price', $price, PDO::PARAM_STR);
             $stmt->bindParam(':email', $email, PDO::PARAM_STR);
             $stmt->bindParam(':total', $newTotal, PDO::PARAM_STR);
             $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
             $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
             $stmt->bindParam(':qstatus', $qStatus, PDO::PARAM_STR);
             $stmt->bindParam(':empid', $grabbedempid, PDO::PARAM_INT);
     
             if ($stmt->execute() && $updateStmt->execute()) 
             {
                echo "Quote updated successfully. New total: $newTotal <br>";
                 echo "Line Item(s) inserted successfully: $lineItem, $price <br>";
                 echo "<script>
                function redirection() {
                    window.location.href = document.referrer;
                }
        
                // Call the redirect function
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

  
    }

    elseif (isset($_POST["finalizeBtn"])) {
        if(!isset($_POST["finalized_quotes"]) || !isset($_POST["email"]) || !isset($_POST["getempid"]))
        {
            //code to make sure you cannot add send input unless a customer is selected
            echo "<script>
            alert('***Cant Sanction For Quote that does not exist.*** Returning to Headquarter page..');
            function redirection() {
                window.location.href = document.referrer;
            }

            setTimeout(redirection, 100);
            </script>";
           
        }

        else{
        $lineItems = $_POST["lineItem"];
        $prices = $_POST["price"];
        $email = $_POST["email"];
        $customerId = $_POST["finalized_quotes"];
        $secretNotes = $_POST["secretnote"];
        $totalAmount = $_POST["total_amount"];
        $empId = $_SESSION["EmpID"];
        $grabbedempid = $_POST["getempid"];
        $qStatusF = "finalized";
        $qStatusS = "sanctioned";
        $discount = isset($_POST["discount"]) && $_POST["discount"] !== '' ? floatval($_POST["discount"]) : null;
        
         // Check if a quote already exists for the customer
         $quoteExists = false;
         $existingTotal = 0;
         $existingQuoteId = null;
          // Query to check if a quote exists
        $quoteCheckSql = "SELECT QID, Total FROM QuotePurchase WHERE CID = :cid AND Qstatus = :qstatus";
        $quoteCheckStmt = $connection2->prepare($quoteCheckSql);
        $quoteCheckStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        $quoteCheckStmt->bindParam(':qstatus', $qStatusF, PDO::PARAM_STR);
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
            
            $updateSql = "UPDATE QuotePurchase SET Total = :newTotal, Qstatus = :qstatus  WHERE CID = :cid";
            $updateStmt = $connection2->prepare($updateSql);
            $updateStmt->bindParam(':newTotal', $newTotal, PDO::PARAM_STR);
            $updateStmt->bindParam(':qstatus', $qStatusS, PDO::PARAM_STR);
            $updateStmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
            for ($i = 0; $i < count($lineItems); $i++) {
                $lineItem = trim($lineItems[$i]);
                $price = floatval($prices[$i]); 
        
                $secretNote = isset($secretNotes[$i]) && $secretNotes[$i] !== '' ? trim($secretNotes[$i]) : null;
        
                $sql = "INSERT INTO QuotePurchase (LineItem, SNote, Price, Email, Total, CID, Discount, Qstatus, EmpID) 
                        VALUES (:lineItem, :snote, :price, :email, :total , :cid , :discount, :qstatus , :empid)";
                 $stmt = $connection2->prepare($sql);
                 $stmt->bindParam(':lineItem', $lineItem, PDO::PARAM_STR);
                 $stmt->bindParam(':snote', $secretNote, PDO::PARAM_STR);
                 $stmt->bindParam(':price', $price, PDO::PARAM_STR);
                 $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                 $stmt->bindParam(':total', $newTotal, PDO::PARAM_STR);
                 $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
                 $stmt->bindParam(':discount', $discount, PDO::PARAM_STR);
                 $stmt->bindParam(':qstatus', $qStatusS, PDO::PARAM_STR);
                 $stmt->bindParam(':empid', $grabbedempid, PDO::PARAM_INT);
         
                 if ($stmt->execute() && $updateStmt->execute()) 
                 {
                    echo "Quote updated successfully. New total: $newTotal <br>";
                     echo "Quote inserted successfully: $lineItem, $price <br>";
                     echo "<script>
                     alert('Email sent to $email');
                    function redirection() {
                        window.location.href = document.referrer;
                    }
            
                    // Call the redirect function
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

                        }



elseif (isset($_POST['delete_quote'])) 
                        {
                           
$pricebeforedelete = isset($_POST["pricebeforedelete"]) && $_POST["pricebeforedelete"] !== '' ? floatval($_POST["pricebeforedelete"]) : null;
$qidToDelete = $_POST['qid_to_delete'];
$totalbeforeupdate = floatval($_POST['totalbeforedelete']);
$cidtosanction = $_POST['cidtosanction'];


$stmttrack = $connection2->prepare("SELECT Total FROM QuotePurchase WHERE QID = :qid");
$stmttrack->bindParam(':qid', $qidToDelete, PDO::PARAM_INT);
$stmttrack->execute();
$grabbeddata = $stmttrack->fetch(PDO::FETCH_ASSOC);

if (!empty($grabbeddata)) {
    $newTotal = $totalbeforeupdate - $pricebeforedelete;
    $stmtafter = $connection2->prepare("UPDATE QuotePurchase SET Total = :total WHERE CID = :cid");
    $stmtafter->bindParam(':total', $newTotal, PDO::PARAM_STR);
    $stmtafter->bindParam(':cid', $cidtosanction, PDO::PARAM_INT);
    $stmtafter->execute();

    $stmtDelete = $connection2->prepare("DELETE FROM QuotePurchase WHERE QID = :qid");
    $stmtDelete->bindParam(':qid', $qidToDelete, PDO::PARAM_INT);
    $stmtDelete->execute();

echo "<script>
function redirection() {
    window.location.href = document.referrer;
}

// Call the redirect function
setTimeout(redirection, 100);
</script>";
}
else{
    echo "Error: " . $stmtDelete->errorInfo()[2] . "<br>";
    echo "Error: " . $stmtafter->errorInfo()[2] . "<br>";
}
                        }


elseif (isset($_POST['FinalizeSanction_quote'])) 
{
    $email = $_POST['emailsent'];
$cidtosanction = $_POST['cidtosanction'];
$stmtsanct = $connection2->prepare("UPDATE QuotePurchase SET Qstatus = 2 WHERE CID = :cid AND Qstatus = 3");
$stmtsanct->bindParam(':cid', $cidtosanction, PDO::PARAM_INT);
$stmtsanct->execute();

echo "<script>
alert('Email sent to $email');
function redirection() {
    window.location.href = document.referrer;
}

// Call the redirect function
setTimeout(redirection, 100);
</script>";
}
                    
    
}
?>


</body>     
    </html>





    