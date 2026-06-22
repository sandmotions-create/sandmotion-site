<?php
// Prevent unauthorized access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request.");
}

// ==========================================
// 1. CHANGE THIS TO YOUR EMAIL ADDRESS
// ==========================================
$to_email = "vaibhav.sandmotion@gmail.com"; 

// 2. Get the text data from the form
$name = htmlspecialchars($_POST['fullName'] ?? 'Unknown Client');
$whatsapp = htmlspecialchars($_POST['whatsappNumber'] ?? 'No Number');
$company = htmlspecialchars($_POST['companyName'] ?? 'No Company');

// 3. Handle the File Upload
if (isset($_FILES['floorPlanFile']) && $_FILES['floorPlanFile']['error'] === UPLOAD_ERR_OK) {
    
    $fileTmpPath = $_FILES['floorPlanFile']['tmp_name'];
    $originalFileName = $_FILES['floorPlanFile']['name'];
    
    // Security: Only allow specific architectural/image extensions
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'dwg', 'dxf'];
    $fileNameParts = explode('.', $originalFileName);
    $fileExtension = strtolower(end($fileNameParts));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        die("Error: File type not allowed. Please upload a PDF, JPG, or DWG.");
    }

    // Rename file to prevent overwriting and remove spaces (e.g., "167890123_my_house.pdf")
    $cleanFileName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $originalFileName);
    $newFileName = time() . '_' . $cleanFileName;
    
    // Set the destination path (the folder must exist on your server!)
    $uploadDirectory = 'uploads/';
    $destinationPath = $uploadDirectory . $newFileName;

    // Move the file from temporary storage to the uploads folder
    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
        
        // 4. Construct the URL so you can click it in your email
        // Make sure this matches your actual website domain
        $fileDownloadLink = "https://sandmotion.in/" . $destinationPath;

        // 5. Send the email alert to you
        $subject = "NEW DEMO REQUEST - Floor Plan from " . $name;
        
        $message = "You have received a new First Floor Free Demo request!\n\n";
        $message .= "CLIENT DETAILS:\n";
        $message .= "Name: " . $name . "\n";
        $message .= "WhatsApp: " . $whatsapp . "\n";
        $message .= "Company: " . $company . "\n\n";
        $message .= "FLOOR PLAN FILE:\n";
        $message .= "Click the link below to securely download the client's file:\n";
        $message .= $fileDownloadLink . "\n";

        // Basic email headers
        $headers = "From: noreply@sandmotion.in\r\n";
        $headers .= "Reply-To: noreply@sandmotion.in\r\n";

        if(mail($to_email, $subject, $message, $headers)) {
            echo "SUCCESS";
        } else {
            echo "Error: File saved, but email failed to send.";
        }

    } else {
        echo "Error: Could not save the file to the server. Check folder permissions.";
    }
} else {
    echo "Error: No file uploaded or file is too large for the server.";
}
?>