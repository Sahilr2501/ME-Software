<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ME Software</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f6f9;
        }

        .container {
            width: 90%;
            max-width: 600px;
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        .logo {
            width: 180px;
            max-width: 100%;
            margin-bottom: 30px;
        }

        h1 {
            margin-bottom: 30px;
            color: #222;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            display: block;
            padding: 15px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn.inventory {
            background: #28a745;
        }

        .btn.inventory:hover {
            background: #1e7e34;
        }
    </style>
</head>

<body>

<div class="container">

    <img src="ME-Logo.jpeg" alt="ME Logo" class="logo">

    <h1>ME Software</h1>

    <div class="buttons">
        <a href="http://localhost/ME%20Software/can-lid-production/" class="btn">
            Can & Lid Production
        </a>

        <a href="http://localhost/ME%20Software/milk-can-inventory/" class="btn inventory">
            Milk Can Inventory
        </a>
    </div>

</div>

</body>
</html>