<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
</head>

<body>
    <h1>My QR Code</h1>
    <!-- Using the <img> tag -->
    <img src="{{ asset('qrcodes/' . $user->id . '_qrcode.svg') }}" alt="QR Code" width="200" height="200">

</body>

</html>