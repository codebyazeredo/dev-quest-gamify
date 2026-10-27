import { modalForm } from './modal-form';

const EDITABLE_FIELDS = ['title', 'description', 'category_id', 'priority_id', 'due_at', 'branch_name', 'pull_request_url'];

function emptyTask(columnId) {
    return { column_id: columnId, title: '', description: '', category_id: '', priority_id: '', assigned_to: null, due_at: '', branch_name: '', pull_request_url: '' };
}

/** Modal de criar/editar tarefa; `tasksById` traz os dados editáveis das tarefas já exibidas na página. */
export function taskModal(tasksById) {
    return {
        ...modalForm(),

        isEdit: false,
        isLocked: false,

        openTask({ mode, columnId = null, taskId = null }) {
            this.isEdit = mode === 'edit';

            if (!this.isEdit) {
                this.isLocked = false;
                this.open({ url: '/api/tasks', method: 'post', form: emptyTask(columnId) });

                return;
            }

            const task = tasksById[taskId];
            this.isLocked = task.locked;

            this.open({
                url: `/api/tasks/${taskId}`,
                method: 'put',
                form: Object.fromEntries(EDITABLE_FIELDS.map((field) => [field, task[field] ?? ''])),
            });
        },
    };
}
