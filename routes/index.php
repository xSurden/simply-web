<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $Env->get("APP_NAME") ?? "App Name" ?> | Ready to Build</title>
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
                    Native PHP,
                    <span class="text-blue-600">Pure Performance</span>
                </h1>
                <p class="text-slate-500 font-medium max-w-2xl mx-auto">
                    Simply Web is designed to be a lightweight, educational PHP framework to run 
                    small to mid sized projects, rapid vanilla php prototyping, built-in security features
                    and for developers who loves pure PHP.
                </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-8">
                <div class="p-8">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-6">About Simply Web</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="text-blue-500 font-bold text-xl flex-shrink-0">→</div>
                            <div>
                                <p class="font-bold text-slate-800">For small-mid sized projects</p>
                                <p class="text-sm text-slate-500">Faster coding, templating and security out of the box while maintaining the vanilla PHP development experience.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-blue-500 font-bold text-xl flex-shrink-0">→</div>
                            <div>
                                <p class="font-bold text-slate-800">Universally Compatible</p>
                                <p class="text-sm text-slate-500">Requires only a webserver and PHP - nothing else. We have tested this with Laragon, XAMPP and HestiaCP. As long as <span class="text-blue-600">.htaccess</span> is compatible, or if you are using Nginx, you can use our template located in the root directory (<span class="text-blue-600">nginx.conf</span>) or within the README.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-blue-500 font-bold text-xl flex-shrink-0">→</div>
                            <div>
                                <p class="font-bold text-slate-800">Learning PHP</p>
                                <p class="text-sm text-slate-500">Simply Web bridges the gap between raw scripting and enterprise architecture. Master PHP fundamentals like Namespaces, Traits, and PSR standards without leaving the native PHP ecosystem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>

            <div class="flex justify-center space-x-6 text-sm font-semibold text-slate-400">
                <a href="https://github.com/xSurden/simply-web" class="hover:text-blue-600 transition flex items-center gap-2">
                    <i class="fab fa-github"></i> Github Repository
                </a>
                <span class="text-slate-200">|</span>
                <span class="cursor-default">Page loaded in <span class="text-blue-500"><?= $Env->getMicroTime() ?>ms</span></span>
            </div>
        </div>

    </body>
</html>