<?php
// Set the upload directory
$uploadDir = '../botcache/';

// Check if base64 image data is sent
if (isset($_POST['imageType']) && $_POST['imageType'] == 'base64' && isset($_POST['imageData'])) {
    $base64Data = $_POST['imageData'];
    $imageData = base64_decode($base64Data);

    // Generate a random filename
    $imageName = uniqid('img_', true) . '.png';
    $targetFile = $uploadDir . $imageName;

    // Save the base64 decoded data as a PNG file
    if (file_put_contents($targetFile, $imageData) === false) {
        http_response_code(500);
        echo "Failed to save image.";
        exit();
    }

    // Resize the image if necessary
    list($width, $height) = getimagesize($targetFile);
    $maxSize = 1024; // Maximum dimension for resizing

    // Determine the new dimensions
    if ($width > $maxSize || $height > $maxSize) {
        if ($width > $height) {
            $newWidth = $maxSize;
            $newHeight = intval($height * ($maxSize / $width));
        } else {
            $newWidth = intval($width * ($maxSize / $height));
            $newHeight = $maxSize;
        }

        // Resize the image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $sourceImage = imagecreatefrompng($targetFile);

        // Resize the image
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save the resized image
        imagepng($resizedImage, $targetFile);

        // Free up memory
        imagedestroy($resizedImage);
        imagedestroy($sourceImage);
    }

     // Get the absolute URL of the uploaded and resized image
    $absoluteUrl = 'https://tktrng.com/guide/chiggyback/botcache/' . $imageName;

    // Return the absolute URL of the uploaded and resized image
    echo $absoluteUrl;
    exit();
}

// Handle file upload
if ($_FILES) {
    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    // Check if the uploaded file is an image
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        http_response_code(400);
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit();
    }

    if ($_FILES['image']['size'] > 2500000) { // Limit size to 2.5MB
        http_response_code(500);
        echo "File too large! Limit images to 2.5MB.";
        exit();
    }

    // Generate a random filename
    $imageName = uniqid('img_', true) . '.' . $imageFileType;
    $targetFile = $uploadDir . $imageName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        // Handle upload failure
        http_response_code(500);
        echo "Failed to upload image.";
        exit();
    }

    // Resize the image if necessary
    list($width, $height) = getimagesize($targetFile);
    $maxSize = 1024; // Maximum dimension for resizing

    // Determine the new dimensions
    if ($width > $maxSize || $height > $maxSize) {
        if ($width > $height) {
            $newWidth = $maxSize;
            $newHeight = intval($height * ($maxSize / $width));
        } else {
            $newWidth = intval($width * ($maxSize / $height));
            $newHeight = $maxSize;
        }

        // Resize the image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $sourceImage = null;

        switch ($imageFileType) {
            case 'jpg':
            case 'jpeg':
                $sourceImage = imagecreatefromjpeg($targetFile);
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($targetFile);
                break;
            case 'gif':
                $sourceImage = imagecreatefromgif($targetFile);
                break;
        }

        // Resize the image
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save the resized image
        switch ($imageFileType) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($resizedImage, $targetFile);
                break;
            case 'png':
                imagepng($resizedImage, $targetFile);
                break;
            case 'gif':
                imagegif($resizedImage, $targetFile);
                break;
        }

        // Free up memory
        imagedestroy($resizedImage);
        imagedestroy($sourceImage);
    }

    // Get the absolute URL of the uploaded and resized image
    $absoluteUrl = 'https://tktrng.com/guide/chiggyback/botcache/' . $imageName;

    // Return the absolute URL of the uploaded and resized image
    echo $absoluteUrl;
    exit();
}

http_response_code(400);
echo "Invalid request.";
?>
