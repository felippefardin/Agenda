<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minha Agenda</title>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 min-h-screen p-10">

    <div class="max-w-4xl mx-auto">

        <h1 class="text-3xl font-bold mb-6 text-center">
            📅 Mini Agenda
        </h1>

        <!-- FORMULÁRIO -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Nova Tarefa</h2>

            <form id="taskForm" class="grid grid-cols-2 gap-4">

                <input type="text" id="title"
                    placeholder="Título"
                    class="border p-2 rounded col-span-2" required>

                <select id="priority" class="border p-2 rounded">
                    <option value="urgent">Urgente</option>
                    <option value="important">Importante</option>
                    <option value="optional">Opcional</option>
                </select>

                <input type="datetime-local" id="start_time"
                    class="border p-2 rounded" required>

                <input type="datetime-local" id="end_time"
                    class="border p-2 rounded" required>

                <button type="submit"
                    class="bg-blue-600 text-white p-2 rounded col-span-2 hover:bg-blue-700">
                    Criar Tarefa
                </button>
            </form>
        </div>

        <!-- LISTA -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h2 class="text-xl font-semibold mb-4">Tarefas</h2>
            <ul id="tasks" class="space-y-3"></ul>
        </div>

    </div>

</body>
</html>