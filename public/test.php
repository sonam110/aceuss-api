
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Testing RATCHET</h2>
    <div class="container">
        <div class="content">
            <div class="title">Laravel</div>
            <button onclick="send()">Submit</button>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        var ws = new WebSocket("wss://stage-api.bookmyinfluencers.com/chat-wss/");
        ws.onopen = function () {
            // Websocket is connected
            console.log("Websocket connected");
            ws.send("Hello World");
            console.log("Message sent");
        };
        ws.onmessage = function (event) {
            // Message received
            console.log("Message received = " + event.data);
        };
        ws.onclose = function () {
            // websocket is closed.
            console.log("Connection closed");
        };
    </script>
</body>
</html>