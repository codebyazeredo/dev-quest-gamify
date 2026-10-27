import { modalForm } from './modal-form';
import { suggestBranchName } from './branch-name';

const EDITABLE_FIELDS = ['title', 'description', 'category_id', 'priority_id', 'due_at', 'branch_name', 'pull_request_url'];

function emptyTask(columnId) {
    return { column_id: columnId, title: '', description: '', category_id: '', priority_id: '', assigned_to: null, due_at: '', branch_name: '', pull_request_url: '' };
}

/**
 * Modal de criar/editar tarefa; `tasksById` traz os dados editáveis das tarefas já exibidas na página
 * e `categoryNames` (id => nome) alimenta a sugestão de nome da branch.
 */
export function taskModal(tasksById, categoryNames = {}) {
    return {
        ...modalForm(),

        isEdit: false,
        isLocked: false,
        taskId: null,
        isBranchAuto: true,

        openTask({ mode, columnId = null, taskId = null }) {
            this.isEdit = mode === 'edit';
            this.taskId = taskId;

            if (!this.isEdit) {
                this.isLocked = false;
                this.isBranchAuto = true;
                this.open({ url: '/api/tasks', method: 'post', form: emptyTask(columnId) });

                return;
            }

            const task = tasksById[taskId];
            this.isLocked = task.locked;
            // Uma branch já cadastrada nunca é sobrescrita pela sugestão.
            this.isBranchAuto = !task.branch_name;

            this.open({
                url: `/api/tasks/${taskId}`,
                method: 'put',
                form: Object.fromEntries(EDITABLE_FIELDS.map((field) => [field, task[field] ?? ''])),
            });
        },

        /** Reexecutada pelo x-effect quando título ou categoria mudam; só age enquanto a branch não foi editada à mão. */
        syncBranch() {
            const suggestion = suggestBranchName({
                title: this.form.title,
                category: categoryNames[this.form.category_id],
                taskId: this.taskId,
            });

            if (this.isBranchAuto) {
                this.form.branch_name = suggestion;
            }
        },

        regenerateBranch() {
            this.isBranchAuto = true;
            this.syncBranch();
        },

        isPullRequestLink() {
            return /^https?:\/\//i.test(this.form.pull_request_url ?? '');
        },
    };
}
