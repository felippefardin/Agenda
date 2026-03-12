<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agenda Inteligente</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <style>
        html, body { height: 100%; margin: 0; overflow: hidden; }
        .app-container { height: 100vh; display: flex; flex-direction: column; }
        .fc { height: 100% !important; font-size: 0.9rem; }
        .fc-daygrid-day { cursor: pointer; transition: 0.2s; }
        .fc-daygrid-day:hover { background-color: #f1f5f9 !important; }
        .fc-day-other { visibility: hidden !important; pointer-events: none !important; }
        .priority-urgent { border-left: 5px solid #ef4444 !important; background: #fee2e2 !important; color: #b91c1c !important; }
        .priority-important { border-left: 5px solid #f59e0b !important; background: #fef3c7 !important; color: #b45309 !important; }
        .priority-optional { border-left: 5px solid #10b981 !important; background: #d1fae5 !important; color: #047857 !important; }
        .fc-event { border: none !important; margin: 2px 4px !important; padding: 2px 5px !important; font-weight: bold; font-size: 0.85em; border-radius: 6px !important; }

        @media (max-width: 640px) {
            .fc-toolbar { flex-direction: column; gap: 8px; }
            .fc-toolbar-title { font-size: 1.1rem !important; }
            .app-header { padding: 0.75rem 1rem; }
        }

        #notification-container {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px; max-width: 320px;
        }
        .notif-item { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50">

    <div class="app-container">
        <header class="app-header p-4 md:p-6 bg-indigo-600 text-white flex justify-between items-center shadow-lg">
            <h1 class="text-xl md:text-2xl font-bold">
                <i class="fas fa-calendar-alt mr-2"></i> Minha Agenda
            </h1>
            
            <div class="flex items-center gap-4">
                <p id="clock" class="text-sm font-mono bg-indigo-500 px-3 py-1 rounded-lg hidden sm:block"></p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-xs bg-indigo-700 hover:bg-rose-600 px-4 py-2 rounded-xl transition-all duration-300 shadow-md font-bold uppercase tracking-wider">
                        <i class="fas fa-sign-out-alt"></i> 
                        <span class="hidden md:inline">Sair</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-grow bg-white overflow-hidden">
            <div id="calendar" class="h-full p-2 md:p-6"></div>
        </main>
    </div>

    <div id="notification-container"></div>

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
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome</label>
                    <input type="text" id="title" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descrição</label>
                    <textarea id="description" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Data</label>
                    <input type="date" id="taskDate" class="w-full border-2 p-3 rounded-xl outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Prioridade</label>
                        <select id="priority" class="w-full border-2 p-3 rounded-xl bg-white outline-none">
                            <option value="urgent">🔴 Urgente</option>
                            <option value="important">🟡 Importante</option>
                            <option value="optional">🟢 Opcional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                        <select id="status" class="w-full border-2 p-3 rounded-xl bg-white outline-none">
                            <option value="fechado">🔒 Fechado</option>
                            <option value="andamento">⏳ Em andamento</option>
                            <option value="finalizado">✅ Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="button" id="btnDelete" onclick="deleteTask()" class="hidden flex-1 bg-rose-50 text-rose-600 font-bold py-3 rounded-xl border border-rose-200">Remover</button>
                    <button type="submit" class="flex-[2] bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let calendar;
    let selectedDay;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(() => { 
            const clockEl = document.getElementById('clock');
            if(clockEl) clockEl.innerText = new Date().toLocaleTimeString('pt-BR'); 
        }, 1000);

        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: window.innerWidth < 768 ? 'dayGridDay' : 'dayGridMonth',
            locale: 'pt-br',
            height: '100%',
            showNonCurrentDates: false,
            fixedWeekCount: false,
            displayEventTime: false,
            headerToolbar: { 
                left: 'prev,next today', 
                center: 'title', 
                right: 'dayGridMonth,dayGridWeek,dayGridDay' 
            },
            buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', day: 'Dia' },
            dateClick: (info) => {
                selectedDay = info.dateStr;
                document.getElementById('actionDateLabel').innerText = new Date(selectedDay + "T00:00:00").toLocaleDateString('pt-BR', {day:'numeric', month:'long'});
                document.getElementById('actionModal').classList.remove('hidden');
            },
            events: "{{ url('/tasks') }}",
            eventDataTransform: function(task) {
                return {
                    id: task.id,
                    title: task.title,
                    start: task.start_time,
                    end: task.end_time,
                    extendedProps: { 
                        priority: task.priority, 
                        description: task.description, 
                        status: task.status // Sincronizado com o banco
                    }
                };
            },
            eventContent: function(arg) {
                const status = arg.event.extendedProps.status || 'fechado';
                const icon = status === 'finalizado' ? '✅' : (status === 'andamento' ? '⏳' : '🔒');
                let titleEl = document.createElement('div');
                titleEl.className = 'fc-event-title-container flex items-center gap-1 overflow-hidden text-xs';
                titleEl.innerHTML = `<span>${icon}</span> <span class="truncate">${arg.event.title}</span>`;
                return { domNodes: [titleEl] };
            },
            eventClassNames: (arg) => ['priority-' + arg.event.extendedProps.priority],
            windowResize: function() {
                calendar.changeView(window.innerWidth < 768 ? 'dayGridDay' : 'dayGridMonth');
            }
        });
        calendar.render();
        checkTodayNotifications();
    });

    function closeModals() {
        document.querySelectorAll('#actionModal, #listModal, #formModal').forEach(m => m.classList.add('hidden'));
    }

    async function showDayTasks() {
        const res = await fetch(`{{ url('/tasks') }}?date=${selectedDay}`, { headers: { 'Accept': 'application/json' } });
        const tasks = await res.json();
        const list = document.getElementById('dayTasksList');
        closeModals();
        document.getElementById('listModal').classList.remove('hidden');
        list.innerHTML = tasks.length ? '' : '<p class="text-center text-slate-400 py-10">Nenhum compromisso.</p>';
        tasks.forEach(task => {
            list.innerHTML += `<div onclick='editTask(${JSON.stringify(task)})' class="p-4 border-2 border-slate-50 rounded-xl hover:border-indigo-200 cursor-pointer bg-white shadow-sm">
                <h4 class="font-bold text-slate-800">${task.title}</h4>
                <p class="text-xs text-slate-500">${task.description || ''}</p>
            </div>`;
        });
    }

    function openFormModal() {
        document.getElementById('taskForm').reset();
        document.getElementById('taskId').value = "";
        document.getElementById('taskDate').value = selectedDay;
        document.getElementById('btnDelete').classList.add('hidden');
        closeModals();
        document.getElementById('formModal').classList.remove('hidden');
    }

    // Função de Edição corrigida
    function editTask(task) {
        closeModals();
        document.getElementById('formModal').classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Editar Compromisso";
        document.getElementById('taskId').value = task.id;
        document.getElementById('title').value = task.title;
        document.getElementById('description').value = task.description || '';
        document.getElementById('taskDate').value = task.start_time.split(' ')[0];
        document.getElementById('priority').value = task.priority;
        document.getElementById('status').value = task.status;
        document.getElementById('btnDelete').classList.remove('hidden');
    }

    document.getElementById('taskForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('taskId').value;
        
        const payload = {
            title: document.getElementById('title').value,
            description: document.getElementById('description').value,
            priority: document.getElementById('priority').value,
            status: document.getElementById('status').value, // Bate com o Controller e Model
            start_time: `${document.getElementById('taskDate').value} 00:00:00`,
            end_time: `${document.getElementById('taskDate').value} 23:59:59`
        };

        const response = await fetch(id ? `{{ url('/tasks') }}/${id}` : `{{ url('/tasks') }}`, {
            method: id ? 'PUT' : 'POST',
            headers: { 
                'X-CSRF-TOKEN': csrf, 
                'Content-Type': 'application/json', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            closeModals();
            calendar.refetchEvents();
        } else {
            const errorData = await response.json();
            alert('Erro ao salvar: ' + JSON.stringify(errorData.errors));
        }
    });

    async function deleteTask() {
        const id = document.getElementById('taskId').value;
        if(!confirm('Excluir este compromisso?')) return;
        
        await fetch(`{{ url('/tasks') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf }
        });
        closeModals();
        calendar.refetchEvents();
    }

    async function checkTodayNotifications() {
        const today = new Date().toISOString().split('T')[0];
        const res = await fetch(`{{ url('/tasks') }}?date=${today}`, { headers: { 'Accept': 'application/json' } });
        const tasks = await res.json();
        const container = document.getElementById('notification-container');
        tasks.forEach(task => {
            const div = document.createElement('div');
            div.className = "notif-item bg-slate-900 text-white p-4 rounded-xl shadow-2xl mb-2";
            div.innerHTML = `<strong>Hoje:</strong> ${task.title} <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>`;
            container.appendChild(div);
        });
    }
    </script>
</body>
</html>