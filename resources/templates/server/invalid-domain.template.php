<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Domain</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --text-color: #f8fafc;
            --accent-color: #38bdf8;
        }

        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
        }

        .container {
            padding: 2rem;
            animation: fadeIn 0.5s ease-in-out;
        }

        .logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 2rem;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        p {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <h1>Invalid Domain</h1>
        <p>The domain you are visiting is not authorized to access this application.
        </p>

    </div>

</body>
</html>