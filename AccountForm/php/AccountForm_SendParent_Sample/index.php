<?php
/*----------------------------------------------
Author: SDK Support Group
Company: Paya
Contact: sdksupport@paya.com
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!!! Samples intended for educational use only!!!
!!!        Not intended for production       !!!
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-----------------------------------------------*/       
    
    
    require('shared.php');
    
    // set variables
    $locationID = $location['ID'];
    $contactID = $location['ContactID'];
    $host = $developer['Host'];
    $developerID = $developer['ID'];
    
    // set variables for generating the required hash
    $user_id = $user['ID'];
    $user_hash_key = $user['HashKEY'];
    $timestamp = time();
    
    // Generate the secure hash, making sure the variables
    // are in the proper sequence.
    $data = $user_id . $timestamp;
    $hash_key = hash_hmac('sha256', $data, $user_hash_key);

    //set a unique account_vault_api_id (This can be generated by any method of your choosing)
    $acctVaultAPIID = "SDK" . $timestamp;
    
    // Create Request
    $req = [
       "accountvault" => [
          "payment_method" => "cc", //required field can be cc or ach
          "location_id" => $locationID, //required field
          "account_vault_api_id" => $acctVaultAPIID, //required field
          "ach_sec_code" => "WEB",//required for ach
          "account_holder_name" => "Rick Sanchez",//required for ach
          //the follwing fields are optional
          "contact_id" => $contactID,
          "title" => "Stored payment 1",
          "billing_address" => "123 Main St",
          "billing_city" => "Anytown",
          "billing_state" => "GA",
          //the following fields are used to control iFrame behaviour
          "show_account_holder_name" => "Rick Sanchez",
          "show_title" => "1",
          "show_account_holder_name" => "1",
          "show_street" => "1",
          "show_zip" => "1",
          "stylesheet_url" => "https://api.payaconnect.com/css/accountform.css",
          //the following fields are used to control iFame after completion
          "display_close_button" => "1",
          "parent_close" => "1",
          "parent_close_delay" => "3",
          "parent_origin" => null,
          "redirect_url_on_approval" => "successful.php",
          "redirect_url_delay" => "1"
        ]
    ];
    
    // Hex encode the request data
    $hexReq = bin2hex(json_encode($req));
    
    // Build URL (URL + Developer ID + Hash Key + User ID + Timestamp + Hex-encoded Request Data)
    $url = $host . "/v2/accountform?developer-id=" . $developerID . "&hash-key=" . $hash_key . "&user-id=" . $user_id . "&timestamp=" . $timestamp . "&data=" . $hexReq;


    // create and set cookie to send the account_vault_api_id
    // to the approved page to GET account vault details for display
    setcookie("acctVaultAPIID", $acctVaultAPIID);
    
?>
<html>
    <head>    
        <style>
            a {
                display:inline-block;
                background-color:#428bca;
                border-color:#357ebd;
                border-radius:5px;
                border-width:0;
                border-style:inset;
                color:#ffffff;
                font-size:12px;
                font-family:cursive;
                height:30px;
                width:100px;
                margin:0px;
                padding:7px;
                text-decoration:none;
                text-align:center;
            }
        </style>
        
        <!-- Add this script tag prior to embedding the iFrame -->
        <script>
            window.addEventListener("message", receiveMessage, false);
            
            function receiveMessage(event) {
              // Make sure the value for allowed matches the domain of the iFrame you are embedding.
              var allowed = "https://api.sandbox.payaconnect.com";
              // Verify sender's identity
              if (event.origin !== allowed) return;
            
              // Add logic here for doing something in response to the message
              console.log(event); // for example only, log event object
              console.log(JSON.parse(event.data)); // for example only, log JSON data
              
              // Write Response from accountForm to Parent parent_orgine
			        document.getElementById("form_response").innerHTML = JSON.stringify(event.data);

              // Write Response from Accountform to Parent Page
			        var response = document.getElementById("form_response");
              var obj = JSON.parse(event.data);
              response.innerHTML = JSON.stringify(obj, undefined, 2);
			      }
        </script>
        
    </head>
    <body>
    
        <div>
            <h1>Paya Connect Account Storage Form</h1>
            <br />
        </div>
        
        
        
        <!-- include the iframe after the script tag for the event listener -->
        <iframe src="<?= $url ?>" width="400px" height="500px"></iframe>
        
        <div>
          <h1>Parent Page Response</h1>
          <pre id="form_response"></pre>
        </div>


    </body>
</html>



