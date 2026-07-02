<?php
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
    
    if (isset($_POST['add_profile'])) {
        $node_identity = gethostname() ?: 'lab-worker-node';
        
        $name       = !empty($_POST['form_name']) ? trim($_POST['form_name']) : 'N/A';
        $occupation = !empty($_POST['form_occupation']) ? trim($_POST['form_occupation']) : 'N/A';
        $title      = !empty($_POST['form_title']) ? trim($_POST['form_title']) : 'N/A';

        $stmt = $pdo->prepare("INSERT INTO lab_table (name, occupation, title, worker_node) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $occupation, $title, $node_identity]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $pdo->query("SELECT id, name, occupation, title, worker_node, created_at FROM lab_table ORDER BY id DESC LIMIT 5");
    $profiles = $stmt->fetchAll();

} catch (\PDOException $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Data Collector</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-900 text-zinc-100 min-h-screen p-8 flex flex-col items-center justify-between">

    <div class="w-full max-w-4xl bg-zinc-800 rounded-xl shadow-xl p-6 border border-zinc-700 mb-8">
        
        <div class="border-b border-zinc-700 pb-4 mb-6">
            <h1 class="text-3xl font-bold text-emerald-400 tracking-tight">fcvlab</h1>
            <p class="text-sm font-medium text-zinc-300 mt-0.5">FCVLab project application.</p>
            
            <p class="text-xs text-zinc-400 mt-3 bg-zinc-900/40 inline-block px-3 py-1.5 rounded-md border border-zinc-700/50">
                Database: <code class="text-amber-400 font-mono font-semibold"><?= htmlspecialchars($db) ?></code> | 
                Target Table: <code class="text-cyan-400 font-mono font-semibold">lab_table</code>
            </p>
        </div>

        <?php if (isset($error_msg)): ?>
            <div class="bg-red-950/40 border border-red-500/50 text-red-200 p-4 rounded mb-6 text-sm">
                <strong>Database Error:</strong> <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-zinc-900/50 p-4 rounded-lg border border-zinc-700/60 mb-8">
            <div>
                <label class="block text-xs font-semibold text-zinc-400 uppercase mb-1">Name</label>
                <input type="text" name="form_name" placeholder="John Doe" required
                       class="w-full bg-zinc-900 border border-zinc-600 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-400 uppercase mb-1">Occupation</label>
                <input type="text" name="form_occupation" placeholder="Engineering" required
                       class="w-full bg-zinc-900 border border-zinc-600 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-400 uppercase mb-1">Title</label>
                <input type="text" name="form_title" placeholder="DevOps Engineer" required
                       class="w-full bg-zinc-900 border border-zinc-600 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex items-end">
                <button type="submit" name="add_profile" class="w-full bg-emerald-500 hover:bg-emerald-600 text-zinc-900 font-bold py-2 rounded transition text-sm shadow-md h-[38px]">
                    Save Profile
                </button>
            </div>
        </form>

        <div class="space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Active Records</h3>
            <div class="overflow-x-auto rounded-lg border border-zinc-700 bg-zinc-900/40">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-zinc-700/40 text-zinc-300 font-medium text-xs uppercase tracking-wider">
                            <th class="p-3 border-b border-zinc-700">ID</th>
                            <th class="p-3 border-b border-zinc-700">Name</th>
                            <th class="p-3 border-b border-zinc-700">Occupation</th>
                            <th class="p-3 border-b border-zinc-700">Title</th>
                            <th class="p-3 border-b border-zinc-700">Pod Origin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700 text-zinc-300">
                        <?php if (!empty($profiles)): ?>
                            <?php foreach ($profiles as $row): ?>
                                <tr class="hover:bg-zinc-700/20 transition">
                                    <td class="p-3 font-mono text-emerald-400 font-semibold">#<?= $row['id'] ?></td>
                                    <td class="p-3 font-semibold text-zinc-100"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="p-3 text-zinc-300"><?= htmlspecialchars($row['occupation']) ?></td>
                                    <td class="p-3"><span class="bg-zinc-800 border border-zinc-700 px-2 py-0.5 rounded text-xs font-mono text-amber-400"><?= htmlspecialchars($row['title']) ?></span></td>
                                    <td class="p-3 font-mono text-xs text-zinc-500"><?= htmlspecialchars($row['worker_node']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-zinc-500 italic">
                                    No profile entries recorded yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="w-full text-center py-4 border-t border-zinc-800 mt-auto">
        <p class="text-xs text-zinc-500 font-mono tracking-wide">
            &copy; <?= date('Y') ?> <span class="text-zinc-400 font-semibold">fcvlab - fasil's lab</span>. All rights reserved.
        </p>
    </footer>

</body>
</html>
