<?php include "DB_Secerts.php";?>

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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Get your plants thriving again with our expert plant repair service, providing specialized care and treatments to revive and rejuvenate your green companions.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_files/style.css">
    <title>www.PlantRepair.com</title>
    <head>
    <style>
       .result-box {
    color: white;
    border: 5px solid #0079BF;; /* Change border color */
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
    background-color: #00256fb2; /* Change background color */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
    transition: all 0.3s ease-in-out; /* Add smooth transition */
}

.result-box:hover{
    transform: scale(1.02); /* Apply slight scale on hover */
    box-color: 0 4px 6px rgba(0, 500, 0, 0.5); /* Enhance box shadow on hover */
    background-color:  #718EDE;
}


    </style>
            <script>
        document.addEventListener("DOMContentLoaded", function() {
            const resultBoxes = document.querySelectorAll(".result-box");
            
            resultBoxes.forEach(box => {
                box.addEventListener("mouseenter", function() {
                    if (!this.classList.contains("selected")) {
                        this.classList.add("highlight");
                    }
                });

                box.addEventListener("mouseleave", function() {
                    if (!this.classList.contains("selected")) {
                        this.classList.remove("highlight");
                    }
                });

                box.addEventListener("click", function() {
                    this.classList.toggle("selected");
                });

                box.addEventListener("keydown", function(event) {
                    if (event.key === "Enter") {
                        this.classList.toggle("selected");
                    }
                });
            });
        });
    </script>
    </head>
     <script>
			
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
<style>
    .result-box.highlight {
    background-color: black; 
}
   h2 {
    font-size: 90px;
    font-weight: 600;
    background-image: linear-gradient(to right, #00256fb2, hwb(0 0% 100%));
    color: transparent;
    background-clip: text;
    -webkit-background-clip: text;
  }  
   
  </style>
<body style="background-color: rgba(0, 136, 255, 0.3);">
<div class="text-center">
    
     <h2>Third Interface </h2>
     <style>
     h4 
     {
         color: white;
     }
     </style>
     <h4>Plant Repair Services</h4>

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
              <div class = "text-center">
              
    <style>
    h1 {
        color: white;
    }

    .center-text {
    text-align: center;
  }

    </style>

    <?php
    // Check if $_SESSION['EmpID'] is set and not false before using it
    if (isset($_SESSION['Username']) && $_SESSION['Username'] !== false) {
        echo "<h1><pre><b>Welcome, ". $_SESSION['Username'] . "!</b></pre></h1>";
    }
    ?>

        </div>
            </div>
        </nav> 

        <div class="center-text">
  <br></br>
  <h3><pre>Select 1 Line item to Process a Customer Quote </pre></h3>
</div>

   
<form action="Third_interface.php" method="post">
    <?php
    $connection = $pdo;
    $connection2 = $p;
    
    $stmt = $connection2->prepare("SELECT * from QuotePurchase where Qstatus = 'sanctioned' ");
    $stmt -> execute();
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
    
    if($result)
    {
        foreach($result as $results)
        {
            $qid = $results['QID'];
            $cid = $results['CID'];
            $empshow = $results['EmpID'];

            $stmt4 = $connection2->prepare("SELECT Name FROM Employee WHERE EmpID = :empid");
            $stmt4->bindValue(':empid', $empshow, PDO::PARAM_INT);
            $stmt4->execute();
            $resultempname = $stmt4->fetch(PDO::FETCH_ASSOC);
            $nameforemp = $resultempname['Name'];

            $stmt3 = $connection->prepare("SELECT name from customers where id = :id ");
            $stmt3->bindParam(':id', $cid, PDO::PARAM_INT);
            $stmt3->execute();
            $resultname = $stmt3->fetch(PDO::FETCH_ASSOC);
            $CUSTname = $resultname['name'];
            $EMAIL = $results['Email'];

            echo '<div class="result-box">';
            echo '<pre>';
            echo '<h6>';
            echo '<b>';
            echo "QuoteID: " .$results['QID']. "<br>";

            echo "LineItem: " . $results['LineItem'] . "<br>";

            echo "Secret Notes : " .$results['SNote']. "<br>";

            echo "Price(Individual): " .$results['Price']."<br>";

            echo "Customer: " . $CUSTname . "<br>";  

            echo "Email: " .$results['Email']."<br>";

            echo "Quote Created by: " .$nameforemp. "<br>"; 

            echo "Quote Status: " .$results['Qstatus']."<br>";

            echo "Date Quote was Entered: " .$results['DateEntered']."<br><br>";

            echo "Quote Total: " .$results['Total']. "<br><br>";  

            echo "<button type='button' onclick='window.location.href=\"editforThird.php?qid=$qid&cid=$cid\";'>Add a Discount for Quote Total</button>";
            echo '<button type="button" class="result-button" data-quote-id="' . $results['QID'] . '" data-customer-id="' . $results['CID'] . '" data-total="' . $results['Total'] . '" data-emp-show="' . $empshow . '" data-cust-name="' . $CUSTname . '" data-emp-name="' . $nameforemp . '" data-email="' . $EMAIL . '">Select</button>';
    
            echo '</div>';
            echo '</h6>';
            echo '</pre>';
        }
    }
    else 
    {
        echo "No Sanctioned Quotes are Available !";
    }
    ?>
    <pre><h4><button type="submit" name="order_button" onclick="return confirmSanction()">Process & Order Quote</button></pre><h4>
</form>

<script>
function confirmSanction() {
    return confirm("Are you sure you want to Process the selected line item(s) this Quote?");
}
</script>

<script>
    const buttons = document.querySelectorAll('.result-button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const allBoxes = document.querySelectorAll('.result-box');
            allBoxes.forEach(box => {
                box.classList.remove('highlight'); // Remove highlight from all boxes
            });
            
            const resultBox = this.closest('.result-box');
            resultBox.classList.add('highlight'); // Add highlight to the selected box
            
            const existingInput = document.querySelector('input[name="selected_quotes[]"]');
            if (existingInput) {
                existingInput.remove(); // Remove the old input field
            }
            
            if (resultBox.classList.contains('highlight')) {
                const quoteId = this.getAttribute('data-quote-id');
                const customerId = this.getAttribute('data-customer-id');
                const total = this.getAttribute('data-total');
                const empshow = this.getAttribute('data-emp-show');
                const custName = this.getAttribute('data-cust-name');
                const empName = this.getAttribute('data-emp-name'); // Get the empName attribute
                const email = this.getAttribute('data-email'); // Get the email attribute
                
                const selectedQuotesInput = document.createElement('input');
                selectedQuotesInput.type = 'hidden';
                selectedQuotesInput.name = 'selected_quotes[]';
                selectedQuotesInput.value = quoteId + '|' + customerId + '|' + total + '|' + empshow + '|' + custName + '|' + empName + '|' + email; 
                
                document.querySelector('form').appendChild(selectedQuotesInput);
            }
        });
    });
</script>





<?php
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST['order_button'])) {
        if(isset($_POST['selected_quotes'])) {
            foreach($_POST['selected_quotes'] as $selectedQuote) {
                list($quoteId, $customerId, $total, $empshow, $CUSTname, $nameforemp,$EMAIL) = explode('|', $selectedQuote);
                
                $url = 'http://blitz.cs.niu.edu/PurchaseOrder/';

                // Prepare data for the RESTful webservice
                $data = array(
                    'order' => $quoteId,         // Use the quote ID as the order number
                    'associate' => $nameforemp,  // Usensales associate ID
                    'custid' => $customerId,    // Use the customer ID
                    'amount' => $total       // Use Total order amount
                );

                // Prepare options for the RESTful webservice request
                $options = array(
                    'http' => array(
                        'header' => array('Content-type: application/json', 'Accept: application/json'),
                        'method'  => 'POST',
                        'content' => json_encode($data)
                    )
                );

                // Create context and send request to the RESTful webservice
                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

                // Process the JSON response from the webservice
                $response = json_decode($result, true);

                // Handle response data and errors as needed
                if (isset($response['errors']) && is_array($response['errors'])) {
                    echo '<script>';
                    echo 'var errorString = "***Errors on RESTful Service:\\n";';
                    echo 'var errors = ' . json_encode($response['errors']) . ';';
                    
                    echo 'for (var i = 0; i < errors.length; i++) {';
                    echo '    errorString += "- " + errors[i] + "***\\n";';
                    echo '}';
                    echo 'alert(errorString);';
                    
                    echo 'function redirection() {';
                    echo '    window.location.href = "Third_interface.php";';
                    echo '}';
                    echo 'setTimeout(redirection, 100);';
                    
                    echo '</script>';
                } else {
                    $processingDate = $response['processDay']; // Date on which the order will be processed
                    $commissionPercentage = floatval($response['commission']); // Commission percentage for the salesperson
                    
                    // Calculate commission amount based on commission percentage and order amount
                    $orderAmount = $total; 
                    $CommissionAmount = ($commissionPercentage / 100); 
                    $empcomission = ($total * $CommissionAmount);
                    
                    $updateStmt2 = $connection2->prepare("SELECT Commissions FROM Employee WHERE EmpID = :empid");
                    $updateStmt2->bindParam(':empid', $empshow);
                    $updateStmt2->execute();
                    $grabbed = $updateStmt2->fetch(PDO::FETCH_ASSOC);
                    $comm = floatval($grabbed['Commissions']);

                    $newcommission = $empcomission + $comm;
                    
                    
                    // Example: Update the quote status to 'ordered'
                    $updateStmt = $connection2->prepare("UPDATE QuotePurchase SET Qstatus = 'ordered' WHERE CID = :cid");
                    $updateStmt->bindParam(':cid', $customerId);
                    $updateStmt->execute();
                    
                    
                    
                    // Store commission amount in the database
                    $insertCommissionStmt = $connection2->prepare("UPDATE Employee SET Commissions = :commission WHERE EmpID = :empID");
                    $insertCommissionStmt->bindParam(':commission', $newcommission, PDO::PARAM_STR);
                    $insertCommissionStmt->bindParam(':empID', $empshow, PDO::PARAM_INT); 
                    $insertCommissionStmt->execute();
                    
                    // Redirect back to the original page after processing
                    echo "<h2>Updated successfully! Redirecting back to Third Interface page...</h2>";
                    echo "<script>
                    alert('Email sent to $EMAIL');
                        function redirection() {
                            window.location.href = 'Third_interface.php';
                        }
                
                        // Call the redirect function
                        setTimeout(redirection, 100); 
                    </script>";
                    exit();
                }
            }
        } else {
            echo "<script>
            alert('No quotes selected. Returning to Third InterFace page');
            function redirection() {
                window.location.href = 'Third_interface.php';
            }

            // Call the redirect function 
            setTimeout(redirection, 100); 
        </script>";
        }
    }
}
?>
</body>
</html>
