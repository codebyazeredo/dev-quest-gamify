import { pageActions } from './page-actions';

export function taskComments(taskId) {
    return {
        ...pageActions(),

        body: '',

        add() {
            if (this.body.trim() === '') {
                return null;
            }

            return this.act('post', `/api/tasks/${taskId}/comments`, { body: { body: this.body } });
        },

        remove(commentId) {
            return this.act('delete', `/api/comments/${commentId}`, { confirmation: 'Remover este comentário?' });
        },
    };
}
