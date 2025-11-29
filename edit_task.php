<?php
require 'auth.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $id, ':user_id' => $_SESSION['user_id']]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Task not found or access denied.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';

    if (empty($title)) {
        $error = 'Task title is required.';
    } else {
        $stmt = $db->prepare("UPDATE tasks SET title = :title WHERE id = :id AND user_id = :user_id");
        if ($stmt->execute([':title' => $title, ':id' => $id, ':user_id' => $_SESSION['user_id']])) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Failed to update task.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
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

        <div class="glass-card rounded-3xl shadow-xl p-12 mb-8 max-w-lg mx-auto transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/20">
            <h2 class="text-2xl font-bold mb-8">Edit Task</h2>
            <?php if ($error): ?>
                <div class="text-rose-500 mb-4 font-medium"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="flex items-center gap-4 mb-4">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required autofocus class="w-full p-4 border-2 border-transparent rounded-2xl bg-white/80 text-lg transition-all duration-300 shadow-inner focus:outline-none focus:border-primary focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.15)] focus:scale-[1.01]">
                </div>
                
                <div class="flex gap-4 mt-8">
                    <button type="submit" class="flex-1 py-4 bg-gradient-to-br from-primary to-accent text-white rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 font-semibold text-lg">Update</button>
                    <a href="index.php" class="flex-1 py-4 text-center border-2 border-slate-300 text-slate-500 rounded-2xl hover:bg-white/50 hover:text-slate-800 hover:-translate-y-1 transition-all duration-300 font-semibold text-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
