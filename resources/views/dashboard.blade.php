<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Minha Agenda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        <input type="text" name="title" placeholder="Título da Tarefa" required class="border-gray-300 rounded-md">
                        <textarea name="description" placeholder="Descrição" class="border-gray-300 rounded-md"></textarea>
                        
                        <select name="priority" required class="border-gray-300 rounded-md">
                            <option value="optional">Opcional</option>
                            <option value="important">Importante</option>
                            <option value="urgent">Urgente</option>
                        </select>

                        <input type="hidden" name="status" value="pendente">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm">Início</label>
                                <input type="datetime-local" name="start_time" required class="w-full border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm">Fim</label>
                                <input type="datetime-local" name="end_time" required class="w-full border-gray-300 rounded-md">
                            </div>
                        </div>

                        <x-primary-button class="justify-center">Salvar Tarefa</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-bold mb-4 border-b pb-2">Minhas Tarefas Salvas</h3>
                @if(isset($tasks) && $tasks->count() > 0)
                    @foreach($tasks as $task)
                        <div class="flex justify-between items-center py-3 border-b last:border-0">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $task->title }}</p>
                                <p class="text-xs text-gray-500">{{ $task->start_time->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 uppercase font-bold">
                                {{ $task->priority }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-sm">Nenhuma tarefa encontrada para este utilizador.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>