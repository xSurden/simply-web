<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Simply-Web | Ready to Build</title>
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
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 mb-2">Simply-Web is now installed</h1>
                <p class="text-slate-500 font-medium">Great - you have installed it. Now create!</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-8">
                <div class="p-8">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">Next Steps</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="text-slate-300 font-bold text-xl flex-shrink-0">01</div>
                            <div>
                                <p class="font-semibold text-slate-800">Edit this page</p>
                                <p class="text-sm text-slate-500">Find this view at <code class="bg-slate-100 px-1.5 py-0.5 rounded text-blue-600 code-font">/routes/index.view.php</code></p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-slate-300 font-bold text-xl flex-shrink-0">02</div>
                            <div>
                                <p class="font-semibold text-slate-800">Define a Route</p>
                                <p class="text-sm text-slate-500">Create a new .view.php file at <code class="bg-slate-100 px-1.5 py-0.5 rounded text-blue-600 code-font">/resources/routes/</code> to add a new page/Api.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-slate-300 font-bold text-xl flex-shrink-0">03</div>
                            <div>
                                <p class="font-semibold text-slate-800">Code</p>
                                <p class="text-sm text-slate-500">$Template->Render(); // Use any HTML templates or make your own.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-slate-50 border-t border-slate-100 px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">URL: <?= \SW\Source\Server\Utilities\Text::DisplayText($_SERVER["HTTP_HOST"]) ?></span>
                    </div>
                    <span class="text-xs text-slate-400 code-font">PHP v<?= phpversion(); ?></span>
                </div>
            </div>

            <div class="flex justify-center space-x-6 text-sm font-semibold text-slate-400">
                <a href="https://github.com/xSurden/simply-web" class="hover:text-blue-600 transition">GitHub Repo</a>
            </div>
        </div>

    </body>
</html>