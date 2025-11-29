<?php
require 'auth.php';
$stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND status = 'active' ORDER BY position ASC, created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$tasks) {
    $tasks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <link rel="icon" type="image/png" href="TDlogo.png">
</head>
<body class="text-slate-800 min-h-screen flex flex-col overflow-x-hidden">
    <div class="container mx-auto px-6 py-12 flex-1 max-w-5xl animate-[fadeInUp_0.8s_ease-out]">
        <?php if (count($tasks) === 0): ?>
            <!-- Empty State: Only Big Button -->
            <div class="text-center pt-[30vh] animate-[scaleIn_0.8s_ease]">
                <a href="create_task.php" class="inline-block text-3xl font-bold text-white py-8 px-16 rounded-full bg-gradient-to-br from-primary to-accent shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 uppercase tracking-wider animate-pulse">
                    CREATE TASK
                </a>
            </div>
        <?php else: ?>
            <!-- Normal State: Headers and List -->
            <div class="flex justify-between items-center mb-12 animate-[fadeInDown_0.8s_ease]">
                <a href="index.php" class="flex items-center gap-3 text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent hover:scale-105 hover:-rotate-2 transition-transform duration-300">
                    <img src="TDlogo.png" alt="TD Logo" class="w-10 h-10 rounded-full shadow-md"> Task Dynamics
                </a>
                <a href="logout.php" class="px-4 py-2 border-2 border-slate-300 text-slate-500 rounded-xl hover:bg-white/50 hover:text-slate-800 hover:-translate-y-0.5 transition-all duration-300">Logout</a>
            </div>

            <div class="flex justify-center gap-12 mb-12 text-xl font-semibold">
                <a href="index.php" class="text-primary border-b-4 border-primary pb-2 opacity-100 transition-all duration-300">Tasks</a>
                <span class="text-slate-300">|</span>
                <a href="deleted_tasks.php" class="text-slate-500 pb-2 border-b-4 border-transparent hover:text-primary hover:-translate-y-0.5 transition-all duration-300">Deleted</a>
            </div>

            <div class="glass-card rounded-3xl shadow-xl p-12 mb-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/20">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-bold">My Tasks</h3>
                    <div class="flex gap-2">
                        <button id="toggleEdit" class="px-4 py-2 border-2 border-slate-300 text-slate-500 rounded-xl hover:bg-white/50 hover:text-slate-800 hover:-translate-y-0.5 transition-all duration-300">Edit</button>
                        <a href="create_task.php" class="px-6 py-2 bg-gradient-to-br from-primary to-accent text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 font-semibold">+ Add</a>
                    </div>
                </div>

                <ul class="space-y-4" id="taskList">
                    <?php foreach ($tasks as $task): ?>
                        <li class="task-item group relative flex items-center gap-6 p-5 rounded-2xl bg-white/60 border border-white/80 transition-all duration-300 hover:bg-white/90 hover:scale-[1.01] hover:translate-x-1 hover:shadow-md hover:z-10 cursor-grab active:cursor-grabbing active:scale-95 <?php echo $task['is_completed'] ? 'opacity-60 bg-slate-100/40' : ''; ?>" data-id="<?php echo $task['id']; ?>">
                            <div class="drag-handle text-slate-400 opacity-30 group-hover:opacity-100 transition-opacity duration-200">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                            </div>
                            
                            <div class="w-6 h-6 rounded-lg border-2 border-slate-300 cursor-pointer flex items-center justify-center transition-all duration-300 bg-white hover:border-primary hover:scale-110 <?php echo $task['is_completed'] ? '!bg-emerald-500 !border-emerald-500 text-white !scale-110' : ''; ?>" onclick="toggleTask(<?php echo $task['id']; ?>, this)">
                                <?php if ($task['is_completed']): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                <?php endif; ?>
                            </div>

                            <span class="flex-1 text-lg font-medium <?php echo $task['is_completed'] ? 'line-through text-slate-400' : ''; ?>"><?php echo htmlspecialchars($task['title']); ?></span>

                            <div class="task-actions hidden gap-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="p-2 border-2 border-slate-300 text-slate-500 rounded-lg hover:bg-white/50 hover:text-slate-800 hover:-translate-y-0.5 transition-all duration-300">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="p-2 bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300" onclick="return confirm('Delete this task?')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </div>
                            
                            <!-- Trash icon for checked tasks -->
                            <div class="trash-icon <?php echo $task['is_completed'] ? 'block' : 'hidden'; ?>">
                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-rose-500 hover:text-rose-600 transition-colors duration-200">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Drag and Drop
        const taskList = document.getElementById('taskList');
        if (taskList) {
            new Sortable(taskList, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const order = Array.from(taskList.children).map(item => item.getAttribute('data-id'));
                    
                    fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=update_order&order[]=' + order.join('&order[]=')
                    });
                }
            });
        }

        // Toggle Task Completion
        function toggleTask(id, el) {
            const item = el.closest('.task-item');
            const isCompleted = item.classList.contains('opacity-60'); // Check opacity class as proxy
            const newState = isCompleted ? 0 : 1;

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=toggle_status&id=${id}&completed=${newState}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Toggle classes manually to match PHP logic
                    if (newState) {
                        item.classList.add('opacity-60', 'bg-slate-100/40');
                        el.classList.add('!bg-emerald-500', '!border-emerald-500', 'text-white', '!scale-110');
                        el.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                        item.querySelector('.trash-icon').classList.remove('hidden');
                        item.querySelector('.trash-icon').classList.add('block');
                        item.querySelector('span').classList.add('line-through', 'text-slate-400');
                    } else {
                        item.classList.remove('opacity-60', 'bg-slate-100/40');
                        el.classList.remove('!bg-emerald-500', '!border-emerald-500', 'text-white', '!scale-110');
                        el.innerHTML = '';
                        item.querySelector('.trash-icon').classList.add('hidden');
                        item.querySelector('.trash-icon').classList.remove('block');
                        item.querySelector('span').classList.remove('line-through', 'text-slate-400');
                    }
                }
            });
        }

        // Toggle Edit Mode
        const toggleEditBtn = document.getElementById('toggleEdit');
        if (toggleEditBtn) {
            toggleEditBtn.addEventListener('click', () => {
                const actions = document.querySelectorAll('.task-actions');
                const trashIcons = document.querySelectorAll('.trash-icon');
                
                actions.forEach(action => {
                    action.classList.toggle('hidden');
                    action.classList.toggle('flex');
                });
                
                trashIcons.forEach(icon => {
                    // If actions are visible (flex), hide trash icons
                    if (actions[0].classList.contains('flex')) {
                         icon.style.display = 'none';
                    } else {
                        // Restore if completed
                        const item = icon.closest('.task-item');
                        if (item.classList.contains('opacity-60')) {
                            icon.style.display = 'block';
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>
