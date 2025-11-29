<?php
require 'auth.php';
$stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND status = 'deleted' ORDER BY created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Tasks</title>
    <link rel="icon" type="image/png" href="TDlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5', // Indigo 600
                        'primary-hover': '#4338ca', // Indigo 700
                        accent: '#0ea5e9', // Sky 500
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 50%, #e0e7ff 100%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col overflow-x-hidden">
    <div class="container mx-auto px-6 py-12 flex-1 max-w-5xl animate-[fadeInUp_0.8s_ease-out]">
        <div class="flex justify-between items-center mb-12 animate-[fadeInDown_0.8s_ease]">
            <a href="index.php" class="flex items-center gap-3 text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent hover:scale-105 hover:-rotate-2 transition-transform duration-300">
                <img src="TDlogo.png" alt="TD Logo" class="w-10 h-10 rounded-full shadow-md"> Task Dynamics
            </a>
            <a href="logout.php" class="px-4 py-2 border-2 border-slate-300 text-slate-500 rounded-xl hover:bg-white/50 hover:text-slate-800 hover:-translate-y-0.5 transition-all duration-300">Logout</a>
        </div>

        <div class="flex justify-center gap-12 mb-12 text-xl font-semibold">
            <a href="index.php" class="text-slate-500 pb-2 border-b-4 border-transparent hover:text-primary hover:-translate-y-0.5 transition-all duration-300">Tasks</a>
            <span class="text-slate-300">|</span>
            <a href="deleted_tasks.php" class="text-primary border-b-4 border-primary pb-2 opacity-100 transition-all duration-300">Deleted</a>
        </div>

        <div class="glass-card rounded-3xl shadow-xl p-12 mb-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/20">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-bold">Recycle Bin</h3>
                <?php if (count($tasks) > 0): ?>
                    <button onclick="clearAll()" class="px-4 py-2 bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 font-medium">Clear All</button>
                <?php endif; ?>
            </div>

            <?php if (count($tasks) > 0): ?>
                <ul class="space-y-4">
                    <?php foreach ($tasks as $task): ?>
                        <li class="flex items-center gap-6 p-5 rounded-2xl bg-white/40 border border-white/60 opacity-70 transition-all duration-300 hover:opacity-100 hover:bg-white/80 hover:shadow-md">
                            <span class="flex-1 text-lg font-medium line-through text-slate-500"><?php echo htmlspecialchars($task['title']); ?></span>
                            <div class="flex gap-2">
                                <a href="recover_task.php?id=<?php echo $task['id']; ?>" class="px-4 py-2 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 font-medium text-sm">Recover</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-slate-500 text-center py-8">No deleted tasks found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function clearAll() {
            if (confirm('Are you sure you want to permanently delete all tasks in the recycle bin?')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=clear_deleted'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
