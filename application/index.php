<?php
// Read configuration safely from Kubernetes OS Environment variables
$host = getenv('DATABASE_HOST') ?: 'mysql-service'; 
$db   = getenv('DATABASE_NAME') ?: 'lab-db';
$user = getenv('DATABASE_USER') ?: 'job';
$pass = getenv('DATABASE_PASSWORD') ?: 'lab_password_123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Fallback: Ensure table exists if not handled by init script
    $pdo->exec("CREATE TABLE IF NOT EXISTS lab_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        worker_node VARCHAR(100) NOT NULL,
        status_metric VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if (isset($_POST['trigger_job'])) {
        $node_identity = gethostname() ?: 'lab-worker-node';
        $metrics = ['Success', 'Running', 'Pending', 'Completed'];
        $random_metric = $metrics[array_rand($metrics)];

        $stmt = $pdo->prepare("INSERT INTO lab_table (worker_node, status_metric) VALUES (?, ?)");
        $stmt->execute([$node_identity, $random_metric]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Query rows to show on the page below
    $stmt = $pdo->query("SELECT id, worker_node, status_metric, created_at FROM lab_table ORDER BY id DESC LIMIT 5");
    $lab_rows = $stmt->fetchAll();

} catch (\PDOException $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Environment Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-900 text-zinc-100 min-h-screen p-8 flex flex-col items-center">

    <div class="w-full max-w-4xl bg-zinc-800 rounded-xl shadow-xl p-6 border border-zinc-700">
        
        <div class="flex justify-between items-center border-b border-zinc-700 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-emerald-400">Lab Database Console</h1>
                <p class="text-xs text-zinc-400 mt-1">
                    Database: <code class="bg-zinc-900 px-1.5 py-0.5 rounded text-amber-400 font-mono"><?= htmlspecialchars($db) ?></code> | 
                    User: <code class="bg-zinc-900 px-1.5 py-0.5 rounded text-cyan-400 font-mono"><?= htmlspecialchars($user) ?></code>
                </p>
            </div>
            
            <form method="POST">
                <button type="submit" name="trigger_job" class="bg-emerald-500 hover:bg-emerald-600 text-zinc-900 font-bold px-4 py-2 rounded transition text-sm shadow-md">
                    Run New Job Entry
                </button>
            </form>
        </div>

        <?php if (isset($error_msg)): ?>
            <div class="bg-red-950/40 border border-red-500/50 text-red-200 p-4 rounded mb-6 text-sm">
                <strong>Database Error:</strong> <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="space-y-3">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400">Target Table: <span class="text-zinc-200 font-mono">lab_table</span></h3>
            
            <div class="overflow-x-auto rounded-lg border border-zinc-700 bg-zinc-900/40">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-zinc-700/40 text-zinc-300 font-medium text-xs uppercase tracking-wider">
                            <th class="p-3 border-b border-zinc-700">ID</th>
                            <th class="p-3 border-b border-zinc-700">Worker Node</th>
                            <th class="p-3 border-b border-zinc-700">Job Metric</th>
                            <th class="p-3 border-b border-zinc-700">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700 text-zinc-300">
                        <?php if (!empty($lab_rows)): ?>
                            <?php foreach ($lab_rows as $row): ?>
                                <tr class="hover:bg-zinc-700/20 transition">
                                    <td class="p-3 font-mono text-emerald-400 font-semibold">#<?= $row['id'] ?></td>
                                    <td class="p-3 font-mono text-xs"><?= htmlspecialchars($row['worker_node']) ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-zinc-900 border border-zinc-700
                                            <?php
                                            switch($row['status_metric']) {
                                                case 'Success': echo 'text-emerald-400 border-emerald-500/20'; break;
                                                case 'Running': echo 'text-amber-400 border-amber-500/20'; break;
                                                case 'Pending': echo 'text-cyan-400 border-cyan-500/20'; break;
                                                default: echo 'text-zinc-400 border-zinc-600/20';
                                            }
                                            ?>">
                                            <?= htmlspecialchars($row['status_metric']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-zinc-500"><?= $row['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-zinc-500 italic">
                                    No lab metrics recorded yet. Click 'Run New Job Entry' above to save data.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
