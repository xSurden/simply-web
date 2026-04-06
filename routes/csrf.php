<?php
    // Testing CSRF and reCAPTCHA
    $Pkg = new \App\Modules\Security\CSRF();
    $GoogleRecaptcha = new \App\Modules\Security\ReCaptcha\Google();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // 1. Check CSRF
        if ($Pkg->validate(true)) {
            
            // 2. Check reCAPTCHA v2
            // Note: 'g-recaptcha-response' is the default name injected by the loadv2() JS
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            
            if ($GoogleRecaptcha->verify_v2($recaptchaResponse, 0.5)) {
                die("Success! Name: " . htmlspecialchars($_POST["name"]) . " | Age: " . (int)$_POST["age"]);
            } else {
                throw new \Exception("Unable to verify reCaptcha via Google API.");
            }
        }
    }   
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $Env->get("APP_NAME") ?? "App Name" ?> | CSRF & Recaptcha v2/3 Testing</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Fira+Code:wght@400;500&display=swap');
            body { font-family: 'Inter', sans-serif; }
            .code-font { font-family: 'Fira Code', monospace; }
        </style>
    </head>

    <body>
        <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
            <form action="" method="POST" class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
                <?= $Pkg->load() ?>
                <h2 class="text-2xl font-bold mb-6 text-gray-800">CSRF & Google Recaptcha v2/3 Test</h2>
                <p class="mb-6 text-gray-800">This is in its early phrase and may be vulnerable or is not working as intended.</p>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Enter your name
                    </label>
                    <input type="text" id="name" name="name" placeholder="Surden"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div class="mb-6">
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-1">
                        Enter your age
                    </label>
                    <input type="number" id="age" name="age" placeholder="20"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div class="mb-6">
                    <?= $GoogleRecaptcha->loadv2() ?>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200 shadow-sm">
                    Submit Data
                </button>
                
            </form>
        </div>
        
    </body>
</html>

