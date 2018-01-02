// SetFireBase Server Key & URL
var key = "key=AAAAhqtUv-M:APA91bGqbAHX-ErUoiDuhas-4vA__hqARzmYd4rlRoB6nAG--YjNftZS2sFJonfpLkROG7qHo2qFNhOdTNlRbDBV1LhrvLRTc0NHfszJz5Jgpg7l3k_ORznk3Y3W-u5Sc4m9uWBA60Lq";
var url = "https://fcm.googleapis.com/fcm/send";
var topics = 0;
// Set template for a send button
var sendButtonInner = '<td><input type="button" value="Send" onclick="makeNotification()"></td><td></td>';
var sendButton = document.createElement('tr');
sendButton.innerHTML = sendButtonInner;
// Set form as a variable
var notifForm = document.getElementById("notifier");

function setTopics(){
    // Get number of topic dropdowns to add
    var topicResponse = prompt("How many topics would you like to send this notification to?", "1");
    var topicNum = parseInt(topicResponse);
    // Check FCM coniditon limits
    if (topicNum > 3) alert("FireBase notifications will not accept more than 3 topics to send to.")
    else {
        // Remove previously added elements
        while (topics > 0) {
            notifForm.removeChild(notifForm.lastChild);
            // Lower the element index for each element removed i.e. topics
            topics--;
        };
        // For each topic dropdown needed, add a topic drop down then add a final send button
        for (var i = 0; i < topicNum; i++) {
            // Dynamically set template for a topic selector dropdown
            var topicDropdownInner = '<td><label for = "topic">Topic: </label></td><td><select name="topic'+i+'" id = "topic"><option value="all">All</option></select></td>';
            var topicDropdown = document.createElement('tr');
            topicDropdown.innerHTML = topicDropdownInner;
            notifForm.appendChild(topicDropdown);
            // Add one to element index for reference of elements appended to page
            topics++;
        };
        // Append a send button
        notifForm.appendChild(sendButton);
        // Add one to element index for reference of elements appended to page
        topics++;
    };
};

function makeNotification() {
    // Remove 'sendButton' element from topics index
    topics--;
    // Create notification request
    var notification = new XMLHttpRequest();
    notification.open("POST", url, true);
    // Set JSON header and authorization key
    notification.setRequestHeader("Content-type","application/json");
    notification.setRequestHeader("Authorization",key);
    // Set notification body & title
    var body = document.notifier.message.value;
    var notifTitle = document.notifier.title.value;
    // Create a string to append topics to
    var notifTopic = '';
    // Set variable for topic values
    var topicVal = document.getElementsByTagName("select");
    // For each topic, append to notifTopic string
    for (var i = 0; i < topics; i++) {
        // Set topic selected variable
        var topicSelected = topicVal[i].value;
        // If first topic, do not add '||'
        if (i == 0) {
            notifTopic += ("'" + topicSelected + "' in topics");
        }
        else {
            notifTopic += (" || '" + topicSelected + "' in topics");
        };
    };
    // Build notification as JSON string
    var data = JSON.stringify({ "condition" : notifTopic, "notification" : { "body" : body, "title" : notifTitle, "icon" : "ic_notification" }});
    // Log JSON string for debugging
    console.log(data);
    // Send notification
    notification.send(data);
    // Alert FireBase request response
    notification.onload = function () {
        alert(notification.response+', If this states a message_id, the message was successfully sent!');
    };
    // Add 'sendButton' element back to topic index for resetting topic dropdown elements
    topics++;
};
