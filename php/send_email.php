<?php
// Create an empty array to store the response data.
$response = array();

// Check if the request method is POST, indicating a form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data sent via POST request.
    $name = $_POST["full-name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Define the recipient email address, subject, and headers.
    $to = "info@webcodes.ee"; 
    $subject = "Kiri Webcodesi kontaktist $name";
    $headers = "From: $email";

   // Attempt to send the email.
    if (mail($to, $subject, $message, $headers)) {
         // If the email is sent successfully, set "success" to true in the response.
        $response["success"] = true;
    } else {
        // If there's an error sending the email, set "success" to false and provide an error message.
        $response["success"] = false;
        $response["message"] = "Message could not be sent.";
    }
}

// Set the response content type to JSON.
header("Content-type: application/json");

// Encode the response data as JSON and send it back to the client.
echo json_encode($response);
?>
