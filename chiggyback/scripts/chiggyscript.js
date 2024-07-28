$(document).ready(function() {
    var conversationHistory = []; // Array to store the history of conversation
    var imageUrl;

    function addMessageToHistory(role, content, type = "text") {
        conversationHistory.push({ role: role, content: content, type: type });
    }

    // Event listener for clicking the send button
    $("#sendButton").click(function() {
        var userInput = $("#userInput").val();

        // Add user message to conversation history
        addMessageToHistory("user", userInput, "text");

        var messagesDiv = document.getElementById("chatArea");
        var messageDiv = document.createElement("div");
        messageDiv.className = "message user";
        messageDiv.textContent = "User: " + userInput;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        resetImagePreview();
        
        // Clear image upload field
        $("#imageInput").val("");

        // Clear user input field after sending message
        $("#userInput").val("");

                // Check if an image is uploaded and add it to the history, as well as the chatarea
        if (imageUrl) {
            addMessageToHistory("user", imageUrl, "image_url");

            var imageInConversation = document.createElement("div");
            imageInConversation.className = "message user";
            var img = document.createElement("img"); // Create image element
            img.src = imageUrl;
            img.setAttribute("height", "200");
            img.setAttribute("width", "auto");
            img.setAttribute("alt", "User Image");
            imageInConversation.appendChild(img);
            messagesDiv.appendChild(imageInConversation);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Construct the payload with both text and image messages
        var payload = {
            "messages": constructMessages()
        };

        // Send payload to backend
        sendPayloadToBackend(payload);

        function constructMessages() {
            var messages = [];
            var currentMessage = null;

            for (var i = 0; i < conversationHistory.length; i++) {
                var message = conversationHistory[i];

                if ((message.role === "user"|| message.role === "assistant") && message.type === "text") {
                    if (currentMessage) {
                        messages.push(currentMessage);
                    }
                    currentMessage = {
                        "role": message.role,
                        "content": message.content
                    };
                } else if (message.role === "user" && message.type === "image_url" && currentMessage) {
                    currentMessage.content.push({
                        "type": "image_url",
                        "image_url": {
                            "url": message.content,
                            "detail": "low"
                        }
                    });
                } else {
                    if (currentMessage) {
                        messages.push(currentMessage);
                        currentMessage = null;
                    }
                    messages.push({
                        "role": message.role,
                        "content": [
                            {
                                "type": message.type,
                                "text": message.content
                            }
                        ]
                    });
                }
            }

            if (currentMessage) {
                messages.push(currentMessage);
            }

            return messages;
        }

        var ellipsisDiv = document.createElement("div");
        ellipsisDiv.className = "message ellipsis"; // Add 'ellipsis' class to the ellipsis element
        messagesDiv.appendChild(ellipsisDiv); // Add the animated ellipsis
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });

    function sendPayloadToBackend(payload) {
        $.ajax({
            url: 'chiggyback/scripts/chiggyback.php', // Send the request to piccyback.php
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function(response) {
                imageUrl = null;
                $(".ellipsis").remove(); // Remove the animated ellipsis
                displayResponse(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                displayErrorMessage("An error occurred while sending the message. Please try again.");
            }
        });
    }

    function uploadImage() {
        //create formData variable and pull image input
        var formData = new FormData();
        formData.append('image', document.getElementById('imageInput').files[0]);

        //create a div to contain our ellipsis animation then add the class that starts the animation
        var ellipsisDiv = document.createElement("div");
        ellipsisDiv.className = "message ellipsis"; // Add 'ellipsis' class to the ellipsis element

        //capture feedbackMsg area for uploadMessage and add preview to preview element
        var uploadMessageDiv = document.getElementById("feedbackMsg");
        $(uploadMessageDiv).empty(); // Clear the feedback message area
        uploadMessageDiv.textContent = "Uploading image ";
        uploadMessageDiv.appendChild(ellipsisDiv);
        
        //send the actual request
        $.ajax({
            url: 'chiggyback/scripts/imgUpload.php', // Change to the correct URL of your image upload endpoint
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                imageUrl = extractImageUrl(response); // Assign the absolute URL returned by upload_image.php
                displayImagePreview(imageUrl); // Display image preview after successful upload
                $(uploadMessageDiv).empty(); // Clear the feedback message area
                uploadMessageDiv.textContent = "Image uploaded Successfully";
                setTimeout(function() {
                    $(uploadMessageDiv).empty(); // Clear success message after 3 seconds
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error('Image upload failed:', error);
            }
        });
    }

    // // Event listener for clicking the upload image button
    // $("#uploadImageButton").click(function() {
    //     imageUrl = uploadImage();
    // });

    // Event listener for file input change
    $("#imageInput").change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            // reader.onload = function(e) {
            //     $("#imageContainer").css("background-image", "url(" + e.target.result + ")");
            // }
            reader.readAsDataURL(file);
            uploadImage(file); // Upload the image
        }
    });

    // Event listener for paste event
    $("#userInput").on('paste', function(event) {
        var file;
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf("image") === 0) {
                file = items[i].getAsFile();
            }
        }
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var base64Image = e.target.result.split(',')[1]; // Extract base64 part
                // $("#imageContainer").css("background-image", "url(" + e.target.result + ")");
                uploadBase64Image(base64Image); // Upload the base64 image
            }
            reader.readAsDataURL(file);
        }
    });
    
    function uploadBase64Image(base64Image) {
        //create a div to contain our ellipsis animation then add the class that starts the animation
        var ellipsisDiv = document.createElement("div");
        ellipsisDiv.className = "message ellipsis"; // Add 'ellipsis' class to the ellipsis element

        //capture feedbackMsg area for uploadMessage and add preview to preview element
        var uploadMessageDiv = document.getElementById("feedbackMsg");
        $(uploadMessageDiv).empty(); // Clear the feedback message area
        uploadMessageDiv.textContent = "Uploading image ";
        uploadMessageDiv.appendChild(ellipsisDiv);
        $.ajax({
            url: 'chiggyback/scripts/imgUpload.php',
            type: 'POST',
            data: {
                imageData: base64Image,
                imageType: 'base64'
            },
            success: function(response) {
                imageUrl = extractImageUrl(response); // Assign the absolute URL returned by upload_image.php
                displayImagePreview(imageUrl); // Display image preview after successful upload
                $(uploadMessageDiv).empty(); // Clear the feedback message area
                uploadMessageDiv.textContent = "Image uploaded Successfully";
                setTimeout(function() {
                    $(uploadMessageDiv).empty(); // Clear success message after 3 seconds
                }, 3000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Image upload failed: " + textStatus, errorThrown);
            }
        });
    }

    function displayImagePreview(imageUrl) {
    // var imagePreview = $("#imagePreview");
    // imagePreview.empty(); // Clear previous image previews
    // var img = $("<img>").attr("src", imageUrl).css("maxWidth", "200px"); // Create image element
    // imagePreview.append(img); // Append image preview
    $('#imagePreview').empty();
    $('#imagePreview').html('<img src="' + imageUrl + '" alt="user image" />');
    }
    
    function extractImageUrl(blobUrl) {
    // Extract the actual URL without the "blob:" prefix
    var url = blobUrl.replace("blob:", "");
    return url;
    }

    function displayResponse(response) {
        
        var jsonResponse = JSON.parse(response);
        var messages = jsonResponse.choices[0].message.content;
        messages = messages.replace(/\n/g, "<br>");
    
        addMessageToHistory("assistant", messages);
    
        var messagesDiv = document.getElementById("chatArea");
        var messageDiv = document.createElement("div");
        messageDiv.className = "message bot";
        messageDiv.innerHTML = "CG-Guide: ";
        messagesDiv.appendChild(messageDiv);
        streamText(messages, messagesDiv, messageDiv, function() {
            // This callback will be executed after streaming is completed
            // Re-enable input field and send button after streaming is completed
            $("#userInput").prop("disabled", false);
            $("#sendButton").prop("disabled", false);
            isSendingMessage = false; // Reset flag
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });
    }

    function streamText(letters, divbox, minidiv, callback) {
        let i = 0;
        const updateText = () => {
            // If we're at the end of the text, clear the interval and call the callback if it exists
            if (i >= letters.length) {
                if (callback) {
                    callback(); // Call the callback function after streaming is completed
                }
                return;
            }
    
            // Get the next chunk of letters to append
            let chunk = '';
            while (i < letters.length && chunk.length < 4) { // Adjust the chunk size as needed
                chunk += letters[i++];
            }
    
            if (chunk.includes("<") || chunk.includes("<b") || chunk.includes("<br") || chunk.includes("br>") || chunk.includes("r>") || chunk.includes(">")) { 
                // Add more chars to chunk in case there's a markup tag
                while (i < letters.length && !chunk.endsWith('>')) {
                    chunk += letters[i++];
                }
            }
    
            // Append the chunk to the minidiv
            minidiv.innerHTML += chunk;
            divbox.appendChild(minidiv);
            divbox.scrollTop = divbox.scrollHeight;
    
            // Schedule the next update
            requestAnimationFrame(updateText);
        };
    
        // Start the streaming
        requestAnimationFrame(updateText);
    }


    function displayErrorMessage(message) {
        // Display error message
        var errorDiv = document.getElementById("feedbackMsg");
        errordiv.innerHTML = "";
        errorDiv.textContent = message;

        // Clear error message after 3 seconds
        setTimeout(function() {
            errorDiv.textContent = "";
        }, 3000);
    }


    // // Click event to trigger file input click
    // $("#uploadArea").click(function() {
    //     $("#imageInput").click();
    // });
    
    $("#imagePreview").click(function() {
        $("#imageInput").click();
    });
    
    function resetImagePreview() {
    var defaultImage = $('#imagePreview').data('default-image');
    $('#imagePreview').html('<img src="' + defaultImage + '" alt="Default Image" />');
}

//     // Set default background image for uploadArea
//     $("#imagePreview").css("background-image", "url('chiggyback/assets/defaultup.jpg')");
});