<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agenda Moderna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-900">

    <div class="max-w-5xl mx-auto px-4 py-12">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-extrabold text-indigo-600 flex items-center gap-3">
                <i class="fas fa-calendar-alt"></i> Minha Agenda
            </h1>
            <div id="notification-status" class="text-sm font-medium text-slate-500 bg-white px-4 py-2 rounded-full shadow-sm">
                Notificações Ativas (1h antes)
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xl shadow-slate-200/50 rounded-2xl p-8 sticky top-8">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-500"></i> Novo Evento
                    </h2>

                    <form id="taskForm" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Título</label>
                            <input type="text" id="title" class="w-full border-slate-200 border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Ex: Reunião de Pauta" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">Prioridade</label>
                            <select id="priority" class="w-full border-slate-200 border-2 p-3 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="urgent">🔥 Urgente</option>
                                <option value="important">⭐ Importante</option>
                                <option value="optional">☕ Opcional</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-indigo-600">Início</label>
                            <input type="datetime-local" id="start_time" class="w-full border-slate-200 border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-rose-500">Fim</label>
                            <input type="datetime-local" id="end_time" class="w-full border-slate-200 border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer" required>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                            Salvar na Agenda
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white shadow-xl shadow-slate-200/50 rounded-2xl p-8">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <i class="fas fa-tasks text-indigo-500"></i> Seus Compromissos
                    </h2>
                    <div id="taskList" class="space-y-4">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Lógica de busca e exibição (Simplificada para o exemplo)
        async function loadTasks() {
            const response = await fetch('/tasks');
            const tasks = await response.json();
            const list = document.getElementById('taskList');
            list.innerHTML = '';

            tasks.forEach(task => {
                const date = new Date(task.start_time).toLocaleString('pt-BR');
                const priorityColors = {
                    urgent: 'bg-rose-100 text-rose-700 border-rose-200',
                    important: 'bg-amber-100 text-amber-700 border-amber-200',
                    optional: 'bg-emerald-100 text-emerald-700 border-emerald-200'
                };

                list.innerHTML += `
                    <div class="flex items-center justify-between p-5 border-2 border-slate-50 rounded-2xl hover:border-indigo-100 transition-colors">
                        <div class="flex gap-4 items-start">
                            <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">${task.title}</h3>
                                <p class="text-slate-500 text-sm italic">${date}</p>
                                <span class="inline-block mt-2 px-3 py-1 text-xs font-bold rounded-full border ${priorityColors[task.priority] || 'bg-slate-100'}">
                                    ${task.priority.toUpperCase()}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        window.onload = loadTasks;

        // Solicitar permissão de notificação
if (Notification.permission !== "granted") {
    Notification.requestPermission();
}

function checkNotifications() {
    const tasks = document.querySelectorAll('#taskList > div');
    tasks.forEach(async () => {        
    });
}
setInterval(checkNotifications, 60000);
    </script>
</body>
</html>