<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bug Criativo - Agenda Inteligente</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <style>
        .fc-daygrid-day { cursor: pointer; transition: 0.2s; }
        .fc-daygrid-day:hover { background-color: #f1f5f9 !important; }
        .priority-urgent { border-left: 5px solid #ef4444 !important; background: #fee2e2 !important; color: #b91c1c !important; }
        .priority-important { border-left: 5px solid #f59e0b !important; background: #fef3c7 !important; color: #b45309 !important; }
        .priority-optional { border-left: 5px solid #10b981 !important; background: #d1fae5 !important; color: #047857 !important; }
        .fc-event { border: none !important; margin: 2px 4px !important; padding: 2px 5px !important; font-weight: bold; font-size: 0.85em; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-4">

    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-200">
        <header class="p-6 bg-indigo-600 text-white flex justify-between items-center">
            <h1 class="text-2xl font-bold"><i class="fas fa-calendar-alt mr-2"></i> Minha Agenda</h1>
            <p id="clock" class="text-sm font-mono bg-indigo-500 px-3 py-1 rounded-lg"></p>
        </header>
        <div id="calendar" class="p-6 min-h-[750px]"></div>
    </div>

    <div id="actionModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-40 p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl p-6 text-center">
            <h2 id="actionDateLabel" class="text-xl font-bold mb-6 text-slate-800"></h2>
            <div class="grid grid-cols-1 gap-4">
                <button onclick="showDayTasks()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-4 rounded-xl flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i> Ver Compromissos
                </button>
                <button onclick="openFormModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Agendar Novo
                </button>
                <button onclick="closeModals()" class="text-slate-400 text-sm mt-2">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="listModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl flex flex-col max-h-[80vh]">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">Compromissos do Dia</h2>
                <button onclick="closeModals()" class="text-slate-400 text-2xl">&times;</button>
            </div>
            <div id="dayTasksList" class="p-6 space-y-4 overflow-y-auto"></div>
        </div>
    </div>

    <div id="formModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 id="formTitle" class="text-lg font-bold">Novo Compromisso</h2>
                <button onclick="closeModals()" class="text-slate-400 text-2xl">&times;</button>
            </div>
            <form id="taskForm" class="p-6 space-y-4">
                <input type="hidden" id="taskId">
                <input type="hidden" id="originalDate">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome</label>
                    <input type="text" id="title" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descrição</label>
                    <textarea id="description" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" rows="3"></textarea>
                </div>
                
                <div id="dateEditGroup">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Data do Compromisso</label>
                    <input type="date" id="taskDate" class="w-full border-2 p-3 rounded-xl outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora</label>
                        <input type="time" id="taskTime" class="w-full border-2 p-3 rounded-xl outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Prioridade</label>
                        <select id="priority" class="w-full border-2 p-3 rounded-xl bg-white outline-none">
                            <option value="urgent">🔥 Urgente</option>
                            <option value="important">⭐ Importante</option>
                            <option value="optional">☕ Opcional</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="button" id="btnDelete" onclick="deleteTask()" class="hidden flex-1 bg-rose-50 text-rose-600 font-bold py-3 rounded-xl border border-rose-200">Remover</button>
                    <button type="submit" class="flex-[2] bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div id="notifToast" class="hidden fixed bottom-6 right-6 bg-slate-900 text-white p-5 rounded-2xl shadow-2xl border-l-4 border-indigo-500 z-[100] max-w-xs">
        <div class="flex items-start gap-4">
            <div class="bg-indigo-500/20 p-2 rounded-lg text-indigo-400"><i class="fas fa-bell"></i></div>
            <div>
                <h4 class="font-bold text-sm">Lembrete Próximo</h4>
                <p id="notifText" class="text-xs text-slate-300 mt-1"></p>
                <button onclick="this.parentElement.parentElement.parentElement.classList.add('hidden')" class="mt-3 text-[10px] font-bold uppercase tracking-wider text-indigo-400">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        let calendar;
        let selectedDay;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', function() {
            setInterval(() => { document.getElementById('clock').innerText = new Date().toLocaleTimeString('pt-BR'); }, 1000);

            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
                dateClick: (info) => {
                    selectedDay = info.dateStr;
                    document.getElementById('actionDateLabel').innerText = new Date(selectedDay).toLocaleDateString('pt-BR', {day:'numeric', month:'long'});
                    document.getElementById('actionModal').classList.remove('hidden');
                },
                events: '/tasks',
                eventClassNames: (arg) => ['priority-' + arg.event.extendedProps.priority]
            });
            calendar.render();
            setInterval(checkNotifications, 30000);
        });

        async function showDayTasks() {
            const res = await fetch(`/tasks?date=${selectedDay}`);
            const tasks = await res.json();
            const list = document.getElementById('dayTasksList');
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('listModal').classList.remove('hidden');

            list.innerHTML = tasks.length ? '' : '<p class="text-center text-slate-400 py-4">Nenhum compromisso para hoje.</p>';
            tasks.forEach(task => {
                const time = new Date(task.start_time).toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
                list.innerHTML += `
                    <div onclick="editTask(${JSON.stringify(task).replace(/"/g, '&quot;')})" class="p-4 border-2 border-slate-50 rounded-xl hover:border-indigo-200 cursor-pointer bg-white">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-mono font-bold text-indigo-600">${time}h</span>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100">${task.priority}</span>
                        </div>
                        <h4 class="font-bold text-slate-800">${task.title}</h4>
                    </div>`;
            });
        }

        function openFormModal() {
            document.getElementById('taskForm').reset();
            document.getElementById('taskId').value = "";
            document.getElementById('taskDate').value = selectedDay;
            document.getElementById('btnDelete').classList.add('hidden');
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('formModal').classList.remove('hidden');
            document.getElementById('formTitle').innerText = "Novo Compromisso";
        }

        function editTask(task) {
            const dateOnly = task.start_time.split('T')[0] || task.start_time.split(' ')[0];
            const timeOnly = new Date(task.start_time).toTimeString().substring(0,5);

            document.getElementById('taskId').value = task.id;
            document.getElementById('title').value = task.title;
            document.getElementById('description').value = task.description;
            document.getElementById('taskDate').value = dateOnly;
            document.getElementById('taskTime').value = timeOnly;
            document.getElementById('priority').value = task.priority;
            
            document.getElementById('btnDelete').classList.remove('hidden');
            document.getElementById('listModal').classList.add('hidden');
            document.getElementById('formModal').classList.remove('hidden');
            document.getElementById('formTitle').innerText = "Editar Compromisso";
        }

        document.getElementById('taskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('taskId').value;
            const newDate = document.getElementById('taskDate').value;
            const time = document.getElementById('taskTime').value;

            const payload = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                priority: document.getElementById('priority').value,
                start_time: `${newDate} ${time}:00`,
                end_time: `${newDate} 23:59:59`,
                category: 'Work'
            };

            const response = await fetch(id ? `/tasks/${id}` : '/tasks', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload)
            });

            if(response.ok) {
                closeModals();
                calendar.refetchEvents();
                // Redireciona visualmente o calendário para a nova data
                calendar.gotoDate(newDate);
            }
        });

        async function deleteTask() {
            const id = document.getElementById('taskId').value;
            if (confirm('Deseja excluir este compromisso?')) {
                await fetch(`/tasks/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } });
                closeModals();
                calendar.refetchEvents();
            }
        }

        async function checkNotifications() {
            const res = await fetch('/tasks');
            const tasks = await res.json();
            const agora = new Date().getTime();

            tasks.forEach(task => {
                const diff = new Date(task.start_time).getTime() - agora;
                if (diff > 0 && diff <= 1800000 && !task.is_notified) {
                    document.getElementById('notifText').innerText = `O compromisso "${task.title}" começa em 30 min!`;
                    document.getElementById('notifToast').classList.remove('hidden');
                }
            });
        }

        function closeModals() {
            document.querySelectorAll('#actionModal, #listModal, #formModal').forEach(m => m.classList.add('hidden'));
        }
    </script>
</body>
</html>