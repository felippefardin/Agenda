import './bootstrap'

const container = document.getElementById('tasks')
const form = document.getElementById('taskForm')

function getPriorityColor(priority) {
    if (priority === 'urgent') return 'bg-red-500'
    if (priority === 'important') return 'bg-yellow-500'
    return 'bg-green-500'
}

function loadTasks() {
    axios.get('/tasks')
        .then(response => {
            const tasks = response.data
            container.innerHTML = ''

            tasks.forEach(task => {
                const li = document.createElement('li')
                li.className = `p-4 rounded text-white flex justify-between items-center ${getPriorityColor(task.priority)}`
                li.innerHTML = `
                    <div>
                        <p class="font-bold">${task.title}</p>
                        <p class="text-sm">
                            ${new Date(task.start_time).toLocaleString()} 
                            até 
                            ${new Date(task.end_time).toLocaleString()}
                        </p>
                    </div>
                    <button onclick="deleteTask(${task.id})"
                        class="bg-black/30 px-3 py-1 rounded hover:bg-black/50">
                        ✕
                    </button>
                `
                container.appendChild(li)
            })
        })
        .catch(error => {
            console.error('Erro ao buscar tasks:', error)
        })
}

form.addEventListener('submit', function (e) {
    e.preventDefault()

    axios.post('/tasks', {
        title: document.getElementById('title').value,
        priority: document.getElementById('priority').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value
    })
        .then(() => {
            form.reset()
            loadTasks()
        })
        .catch(error => {
            console.error(error.response.data)
        })
})

window.deleteTask = function (id) {
    axios.delete(`/tasks/${id}`)
        .then(() => loadTasks())
}

loadTasks()