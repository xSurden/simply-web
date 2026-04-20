<?php
// Initialize Classes
$DB = new \App\Modules\Database\ElegantHandle();
$Cache = new \App\Server\Utilities\Cache();

$cacheKey = 'user_data_primary.cache';
$readTime = "0.0000";
$displayData = "No data loaded.";
$statusMessage = "Ready for benchmark.";
$statusColor = "text-slate-400";
$rawPayload = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startTime = microtime(true);
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'database') {
            // Using upgraded select: Table, Where, Limit (default 1), Order
            $userData = $DB->select('users', ['id' => 1], 1, 'id DESC');

            if ($userData) {
                $Cache->saveCache($cacheKey, $userData);
                $rawPayload = $userData;
                $displayData = $userData['username'];
                $statusMessage = "Database Hit & Cache Updated";
                $statusColor = "text-blue-600";
            } else {
                $statusMessage = "DB Query Empty (User 1 not found)";
                $statusColor = "text-amber-500";
            }
        } 
        
        elseif ($action === 'cache') {
            $cached = $Cache->loadCache($cacheKey, 300);

            if ($cached) {
                $rawPayload = $cached;
                $displayData = $cached['username'];
                $statusMessage = "Lightning Fast Cache Hit!";
                $statusColor = "text-emerald-600";
            } else {
                $statusMessage = "Cache Miss / Expired";
                $statusColor = "text-red-500";
            }
        }

        elseif ($action === 'clear') {
            $Cache->clearCache($cacheKey);
            $statusMessage = "Cache storage cleared.";
            $statusColor = "text-slate-500";
        }
    } catch (\Exception $e) {
        $statusMessage = "Error: " . $e->getMessage();
        $statusColor = "text-red-600";
    }

    $readTime = number_format((microtime(true) - $startTime) * 1000, 4);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $Env->get("APP_NAME") ?? "App Name" ?> | Cache Test</title>
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
                Database <span class="text-blue-600">&</span> Cache
            </h1>
            <p class="text-slate-500 font-medium max-w-2xl mx-auto">
                Running <span class="text-slate-800 font-bold">ElegantHandle v2</span> with custom limits and ordering support.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="bg-slate-50 border-b border-slate-100 px-8 py-4 flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Real-time Performance</span>
                <span class="text-xs font-bold <?= $statusColor ?> px-3 py-1 bg-white border border-slate-200 rounded-full shadow-sm transition-all">
                    <?= $statusMessage ?>
                </span>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Execution Time</p>
                        <p class="text-3xl font-bold text-slate-800 code-font"><?= $readTime ?>ms</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Result Name</p>
                        <p class="text-xl font-bold text-slate-700 truncate"><?= $displayData ?></p>
                    </div>
                </div>

                <?php if ($rawPayload): ?>
                <div class="mb-8">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 block">Raw Data Inspector</label>
                    <div class="bg-slate-900 rounded-2xl p-6 shadow-inner border border-slate-800 overflow-hidden">
                        <pre class="text-emerald-400 code-font text-sm overflow-x-auto"><code><?= json_encode($rawPayload, JSON_PRETTY_PRINT) ?></code></pre>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-8 py-10 border-2 border-dashed border-slate-100 rounded-3xl flex flex-col items-center justify-center">
                    <p class="text-slate-400 text-sm font-medium">Cache is currently empty.</p>
                </div>
                <?php endif; ?>

                <form method="POST" class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="action" value="database" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white px-6 py-4 rounded-2xl text-sm font-bold transition flex items-center justify-center gap-2">
                        SQL Query
                    </button>
                    <button type="submit" name="action" value="cache" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-2xl text-sm font-bold transition shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                        Load Cache
                    </button>
                    <button type="submit" name="action" value="clear" class="px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                        Clear
                    </button>
                </form>
            </div>
        </div>

        <div class="flex justify-center space-x-6 text-sm font-semibold text-slate-400">
            <a href="/" class="hover:text-blue-600 transition flex items-center gap-2">
                ← Back
            </a>
            <span class="text-slate-200">|</span>
            <span class="cursor-default">Core Load: <span class="text-emerald-500 font-bold"><?= $Env->getMicroTime() ?>ms</span></span>
        </div>
    </div>

</body>
</html>