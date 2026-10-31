import { pageActions } from './page-actions';

export function taskChecklist(taskId) {
    return {
        ...pageActions(),

        title: '',

        add() {
            if (this.title.trim() === '') {
                return null;
            }

            return this.act('post', `/api/tasks/${taskId}/checklist`, { body: { title: this.title } });
        },

        toggle(itemId) {
            return this.act('post', `/api/checklist/${itemId}/toggle`);
        },

        remove(itemId) {
            return this.act('delete', `/api/checklist/${itemId}`);
        },
    };
}
