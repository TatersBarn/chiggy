<?php
// Define your OpenAI API key
$apiKey = "[YOUR_OPENAI_API_KEY]";

// Get the user input from the frontend
$userInput = json_decode(file_get_contents('php://input'), true);

// Define initial system prompt. This default prompt is 58 tokens and costs $0.0000087  (that is 87 ten thousandths of one US cent [87 parts of a decakilosected penny])
$systemPrompt = "You are a friendly and helpful chatbot, you are going to be chatting with a person who is a bit of a silly goose. Keep it PG and talk to the person, ask them about their day, and offer to help with tasks a language model could help with.";

// Construct the conversation array including system prompt and user input
// $conversation = [
//     [
//         "role" => "system",
//         "content" => $systemPrompt
//     ]
// ];

//Construct the conversation array including system prompt and user input
$conversation = [
    [
        "role" => "assistant",
        "content" => "Hi Taters, what are we working on today?"
    ]
];

//  foreach ($userInput['messages'] as $message) {
//     $conversation[] = [
//         "role" => $message['role'],
//         "content" => $message['content']
//     ];
// }

 foreach ($userInput['messages'] as $message) {
    $conversation[] = [
        "role" => $message['role'],
        "content" => $message['content']
    ];
}

// Initialize the model
// $model = "gpt-3.5-turbo-0125"; // Default model from version 1, no vision
// $model = "gpt-4-turbo"; // First Vision Model
// $model = "gpt-4o"; // Second Vision model, may switch back
$model = "gpt-4o-mini"; // Current default model, inexpensive, multimodal, may need new prompt

// //FOR OPENAI API
// // Construct the payload for the API request including conversation history
// $payload = [
//     "model" => $model,
//     "messages" => $conversation,
//     "max_tokens" => 1024
// ];

// // Set up cURL to make the API request
// $curl = curl_init();
// curl_setopt_array($curl, array(
//     CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'POST',
//     CURLOPT_POSTFIELDS => json_encode($payload),
//     CURLOPT_HTTPHEADER => array(
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $apiKey // Include Authorization header
//     ),
// ));


//FOR OOBABOOGA
$payload = [
     "mode" => "chat",
     "character"=> "Fleur",
     "messages" => $conversation
];


// Set up cURL to make the API request
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://finds-flows-yang-necessarily.trycloudflare.com/v1/chat/completions',
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
