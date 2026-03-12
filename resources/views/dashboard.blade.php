<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="flex gap-4">
                        <input type="text" name="title" placeholder="Digite o título da tarefa..." required 
                            class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Salvar Tarefa
                        </button>
                    </div>

                    <input type="hidden" name="status" value="pendente"> 
                    <input type="hidden" name="priority" value="important">
                    <input type="hidden" name="description" value="Tarefa criada rapidamente pelo dashboard">
                    <input type="hidden" name="start_time" value="{{ date('Y-m-d H:i:s') }}">
                    <input type="hidden" name="end_time" value="{{ date('Y-m-d H:i:s', strtotime('+1 hour')) }}">

                    @if ($errors->any())
                        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                            <p class="font-bold">Ops! Verifique os campos abaixo:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>

                <hr class="my-8">

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Minhas Tarefas Recentes</h3>
                    @forelse($tasks as $task)
                        <div class="p-4 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-200">
                            <div>
                                <span class="font-bold text-indigo-600">[{{ $task->status }}]</span>
                                <span class="ml-2 text-gray-800">{{ $task->title }}</span>
                            </div>
                            <span class="text-xs text-gray-500">Início: {{ $task->start_time->format('d/m/H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Você ainda não tem tarefas cadastradas.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>