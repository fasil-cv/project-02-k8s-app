<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kubernetes Application Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col items-center justify-center p-6">

    <div class="w-full max-w-3xl bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 p-8 space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-700 pb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-emerald-400">
                    K8s Unified App Node
                </h1>
                <p class="text-sm text-slate-400 mt-1">Single-Container Architecture (Nginx + PHP-FPM)</p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 animate-pulse">
                Cluster Active
            </span>
        </div>

        <?php
        $host = 'mysql-service'; 
        $user = 'app_user';
        $pass = 'app_password';
        $db   = 'app_database';

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            echo "
            <div class='bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl flex items-center space-x-3'>
                <span class='font-bold'>[!] Database Connection Failed:</span> <span>{$conn->connect_error}</span>
            </div>";
            exit();
        }

        // Auto-create table
        $tableQuery = "CREATE TABLE IF NOT EXISTS cluster_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pod_name VARCHAR(100) NOT NULL,
            generated_color VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($tableQuery);

        // Handle Writes
        $colors = ['Cyan', 'Emerald', 'Violet', 'Amber', 'Rose', 'Fuchsia'];
        $randomColor = $colors[array_rand($colors)];
        $podIdentifier = gethostname() ?: 'Unknown-K8s-Pod';

        if (isset($_GET['action']) && $_GET['action'] == 'insert') {
            $stmt = $conn->prepare("INSERT INTO cluster_logs (pod_name, generated_color) VALUES (?, ?)");
            $stmt->bind_param("ss", $podIdentifier, $randomColor);
            $stmt->execute();
            $stmt->close();
            
            header("Location: index.php");
            exit();
        }

        // Fetch Rows
        $result = $conn->query("SELECT id, pod_name, generated_color, created_at FROM cluster_logs ORDER BY id DESC LIMIT 5");
        ?>

        <div class="bg-slate-700/40 p-4 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-slate-300">
                <span class="font-semibold block text-slate-200">Serving Pod:</span>
                <code class="text-cyan-400 bg-slate-900 px-2 py-0.5 rounded text-xs"><?php echo $podIdentifier; ?></code>
            </div>
            <a href="?action=insert" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 font-medium text-white rounded-lg shadow-md transition-all duration-200 text-sm">
                + Generate & Insert Log Entry
            </a>
        </div>

        <div class="space-y-3">
            <h3 class="text-lg font-semibold text-slate-200">Recent Database Entries (Limit 5)</h3>
            <div class="overflow-hidden border border-slate-700 rounded-xl bg-slate-900/50">
                <table class="min-w-full divide-y divide-slate-700 text-left text-sm">
                    <thead class="bg-slate-800/70 text-slate-400 font-medium text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Origin Pod</th>
                            <th class="px-4 py-3">Color Metric</th>
                            <th class="px-4 py-3">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-slate-400">#<?php echo $row['id']; ?></td>
                                    <td class="px-4 py-3 font-mono text-xs"><?php echo htmlspecialchars($row['pod_name']); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border
                                            <?php
                                            switch($row['generated_color']) {
                                                case 'Cyan': echo 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20'; break;
                                                case 'Emerald': echo 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'; break;
                                                case 'Violet': echo 'bg-violet-500/10 text-violet-400 border-violet-500/20'; break;
                                                case 'Amber': echo 'bg-amber-500/10 text-amber-400 border-amber-500/20'; break;
                                                case 'Rose': echo 'bg-rose-500/10 text-rose-400 border-rose-500/20'; break;
                                                default: echo 'bg-fuchsia-500/10 text-fuchsia-400 border-fuchsia-500/20';
                                            }
                                            ?>">
                                            <?php echo $row['generated_color']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500 italic">No logs found. Click the button to add entries!</td>
                            </tr>
                        <?php endif; $conn->close(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
