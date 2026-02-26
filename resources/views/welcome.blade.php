<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agenda Interativa - Bug Criativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <style>
        .fc-event { cursor: pointer; padding: 2px 5px; border-radius: 6px; }
        .priority-urgent { background-color: #ef4444 !important; border: none !important; }
        .priority-important { background-color: #f59e0b !important; border: none !important; }
        .priority-optional { background-color: #10b981 !important; border: none !important; }
        .fc-daygrid-day:hover { background-color: #f8fafc; cursor: cell; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-8">

    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-200">
        <header class="p-6 bg-indigo-600 text-white flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold"><i class="fas fa-calendar-alt mr-2"></i>Minha Agenda</h1>
                <p class="text-xs opacity-75">Clique no dia para anotar seu compromisso</p>
            </div>
            <div id="notif-indicator" class="text-xs bg-indigo-500 px-3 py-1 rounded-full">Notificações: Ativas</div>
        </header>
        
        <div id="calendar" class="p-6 min-h-[750px]"></div>
    </div>

    <div id="taskModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl scale-95 transition-transform">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-bold text-slate-800">Agendar</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
            </div>
            
            <form id="taskForm" class="p-6 space-y-4">
                <input type="hidden" id="taskId">
                <input type="hidden" id="start_date_hidden">
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Título do Compromisso</label>
                    <input type="text" id="title" class="w-full border-2 border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="O que você vai fazer?" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nível de Prioridade</label>
                    <select id="priority" class="w-full border-2 border-slate-200 p-3 rounded-xl bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="urgent">🔥 Urgente</option>
                        <option value="important">⭐ Importante</option>
                        <option value="optional">☕ Opcional</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" id="btnDelete" onclick="deleteTask()" class="hidden flex-1 bg-rose-50 text-rose-600 font-bold py-3 rounded-xl hover:bg-rose-100 transition-colors border border-rose-200">Excluir</button>
                    <button type="submit" id="btnSave" class="flex-[2] bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let calendar;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                dateClick: (info) => openModal(null, info.dateStr),
                eventClick: (info) => openModal(info.event),
                events: '/tasks', // Rota GET definida no seu web.php
                eventClassNames: (arg) => ['priority-' + arg.event.extendedProps.priority]
            });
            calendar.render();
            
            // Solicitar permissão de notificação
            if (Notification.permission !== "granted") Notification.requestPermission();
            setInterval(checkNotifications, 60000);
        });

        function openModal(event = null, date = null) {
            const modal = document.getElementById('taskModal');
            document.getElementById('taskForm').reset();
            modal.classList.remove('hidden');

            if (event) {
                document.getElementById('modalTitle').innerText = "Editar Compromisso";
                document.getElementById('taskId').value = event.id;
                document.getElementById('title').value = event.title;
                document.getElementById('priority').value = event.extendedProps.priority;
                document.getElementById('btnDelete').classList.remove('hidden');
            } else {
                document.getElementById('modalTitle').innerText = "Novo Compromisso";
                document.getElementById('taskId').value = "";
                document.getElementById('start_date_hidden').value = date;
                document.getElementById('btnDelete').classList.add('hidden');
            }
        }

        function closeModal() { document.getElementById('taskModal').classList.add('hidden'); }

        // LÓGICA DE SALVAMENTO CORRIGIDA PARA O SEU CONTROLLER
        document.getElementById('taskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSave');
            const id = document.getElementById('taskId').value;
            
            btn.disabled = true;
            btn.innerText = 'Processando...';

            const url = id ? `/tasks/${id}` : '/tasks';
            const method = id ? 'PUT' : 'POST';

            // Dados formatados para passar na validação do seu TaskController.php
            const payload = {
                title: document.getElementById('title').value,
                priority: document.getElementById('priority').value,
                description: "Criado via calendário",
                category: "Work", // Valor aceito no seu 'in:Work,...'
                // Formatação de data que o Laravel entende
                start_time: id ? undefined : document.getElementById('start_date_hidden').value + " 09:00:00",
                end_time: id ? undefined : document.getElementById('start_date_hidden').value + " 10:00:00"
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    calendar.refetchEvents(); // Atualiza o calendário visualmente
                    closeModal();
                } else {
                    const erro = await response.json();
                    alert('Erro: ' + (erro.message || 'Verifique as datas.'));
                }
            } catch (error) {
                alert('Erro de conexão.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Salvar';
            }
        });

        async function deleteTask() {
            const id = document.getElementById('taskId').value;
            if (confirm('Deseja excluir este compromisso?')) {
                const response = await fetch(`/tasks/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                if (response.ok) {
                    calendar.refetchEvents();
                    closeModal();
                }
            }
        }

        async function checkNotifications() {
            const response = await fetch('/tasks');
            const tasks = await response.json();
            const agora = new Date().getTime();

            tasks.forEach(task => {
                const diff = new Date(task.start_time).getTime() - agora;
                // Notifica se faltar entre 59 e 60 minutos
                if (diff > 3540000 && diff <= 3600000) {
                    if (Notification.permission === "granted") {
                        new Notification("Lembrete: " + task.title, { body: "Seu compromisso começa em 1 hora!" });
                    }
                }
            });
        }
    </script>
</body>
</html>