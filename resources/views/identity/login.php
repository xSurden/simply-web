<?php
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simply-Web Framework | Security Gateway Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#0f111a] text-white flex items-center justify-center min-h-screen p-5">

    <div class="bg-[#1a1c29] border border-[#30363d] rounded-lg w-full max-w-[400px] p-8 sm:p-10 shadow-2xl">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold tracking-tight mb-2">simply-web</h1>
            <p class="text-[#8b949e] text-sm">Sign in to your account infrastructure</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-500/10 border border-red-500/40 text-[#ff7b72] text-sm p-3 rounded-md mb-5 text-center">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            
            <div>
                <label class="block text-xs font-medium mb-2 text-white uppercase tracking-wider" for="usernameOrEmail">
                    Username or Email
                </label>
                <input 
                    type="text" 
                    id="usernameOrEmail" 
                    name="usernameOrEmail" 
                    class="w-full bg-[#0d1117] border border-[#30363d] rounded-md text-white px-4 py-3 text-base placeholder:text-[#484f58] transition-colors focus:outline-hidden focus:border-[#7c4dff] focus:ring-3 focus:ring-[#7c4dff]/15" 
                    placeholder="Enter your username or email" 
                    required 
                    autofocus
                >
            </div>

            <div>
                <label class="block text-xs font-medium mb-2 text-white uppercase tracking-wider" for="password">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full bg-[#0d1117] border border-[#30363d] rounded-md text-white px-4 py-3 text-base placeholder:text-[#484f58] transition-colors focus:outline-hidden focus:border-[#7c4dff] focus:ring-3 focus:ring-[#7c4dff]/15" 
                    placeholder="••••••••" 
                    required
                >
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full bg-[#7c4dff] hover:bg-[#651fff] text-white font-semibold py-3 px-4 rounded-md text-base cursor-pointer transition-colors"
                >
                    Sign In
                </button>
            </div>
            
        </form>

        <div class="mt-6 text-center text-xs text-[#8b949e]">
            Don't have an account? <a href="/register" class="text-[#7c4dff] font-medium no-underline hover:underline">Register here</a>
        </div>
    </div>

</body>
</html>