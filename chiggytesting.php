<?php include 'frontEnd_testing/header.php'; ?>
    <link rel="stylesheet" href="chiggyback/newStyles.css">
    <?php include 'frontEnd_testing/navbar.php'; ?>
    <div class="guidePage">
        <title>Cannabot Testing Grounds</title>
            <div class="chatWindow">
            <img class="chatHeader" src="chiggyback/assets/chiggybanner3.png"/>    
            <div class="chatContent">
                <div id="chatArea" class="messages"></div>
                <div id="uploadArea" class="uploadArea">
                    <input type="file" id="imageInput" accept="image/*">
                    <div id="imagePreview" data-default-image="chiggyback/assets/newDefault.jpg"><img src="iggyback/assets/newDefault.jpg" alt="DefaultUploadImage"></div> <!-- Display image preview here -->
                </div>
            </div>
            <div class="uploadAndInput">
                <div class="inputBox">
                    <input type="text" id="userInput" name="userInput" placeholder="Ask me anything about growing cannabis">
                    <button id="sendButton">Send</button>
                </div>
                <div id="feedbackMsg"></div> <!-- Feedback and error messages -->
                </div>
                <div class="systemMsg">Our grow expert takes about 3 - 10 seconds to respond, please be patient! <br> Problems? Buggy output? <a href="mailto:sourcery@mnseeds.com">Let us know!</a> </div>
            </div>
            <script src="chiggyback/scripts/chiggyscript.js"></script>
        </div>
<?php include 'frontEnd_testing/footer.php'; ?>
