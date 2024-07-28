<?php

// Get the user input from the frontend
$userInput = json_decode(file_get_contents('php://input'), true);

// // Get the character string
// $character = $userInput[character];

//Construct the conversation array including system prompt and user input
$conversation = [
    [
        "role" => "assistant",
        "content" => "Hi Taters, what are we working on today?"
    ]
];

 foreach ($userInput['messages'] as $message) {
    $conversation[] = [
        "role" => $message['role'],
        "content" => $message['content']
    ];
}

//FOR OOBABOOGA
$payload = [
     "mode" => "chat",
     "character"=> "Fleur",
     "messages" => $conversation
];


// Set up cURL to make the API request
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => '[URLFOROPENAIAPICOMPATIBLEENDPOINTOOBABOOGA]',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
));

// Execute the API request
$response = curl_exec($curl);
curl_close($curl);

// Return the response to the frontend
echo $response;
?>
