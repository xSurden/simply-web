<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $Env->get("APP_NAME") ?? "App Name" ?> | 404 Not Found</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Fira+Code:wght@400;500&display=swap');
            body { font-family: 'Inter', sans-serif; }
            .code-font { font-family: 'Fira Code', monospace; }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col items-center justify-center p-6">

        <div class="max-w-3xl w-full">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 mb-4 leading-tight">
                    <span class="text-red-600">404 Not Found</span>
                </h1>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-8">
                <div class="p-8">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-6">What happened?</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div>
                                <p class="font-bold text-slate-800">Requested Route: <?= $route ?? "null" ?></p>
                                <p class="text-sm text-slate-500">The page you are looking for does not exist.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div>
                                <p class="font-bold text-slate-800">For the app's developer(s)</p>
                                <p class="text-sm text-slate-500">Ensure that the route file exists in the folder <span class="text-red-600">/routes/your_route.php</span>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>

            <div class="flex justify-center space-x-6 text-sm font-semibold text-slate-400">
                <span class="cursor-default">Page loaded in <span class="text-blue-500"><?= $Env->getMicroTime() ?>ms</span></span>
            </div>
        </div>

    </body>
</html>