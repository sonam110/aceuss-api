
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Socket Test</h2>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        var url = "wss://api.aceuss.se:6001/app/b3c8fc875e4efec7dZ1e?protocol=7&client=js&version=4.3.1&flash=false";
        var ws = new WebSocket(url);
        ws.onopen = function () {
            // Websocket is connected
            console.log(url);
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