import { pageActions } from './page-actions';

/** Ações da página de uma tarefa; cada uma chama a API e recarrega a página. */
export function taskPage(taskId) {
    const base = pageActions();
    const endpoint = (action) => `/api/tasks/${taskId}/${action}`;

    return {
        ...base,

        showRejectForm: false,
        rejectionReason: '',
        assignToUserId: null,

        toggleRejectForm() {
            this.showRejectForm = !this.showRejectForm;
            this.rejectionReason = '';
            this.errors = {};
        },

        reject() {
            return this.act('post', endpoint('reject'), { body: { reason: this.rejectionReason } });
        },

        assign() {
            if (!this.assignToUserId) {
                return null;
            }

            return this.act('post', endpoint('assign'), { body: { user_id: this.assignToUserId } });
        },

        approve() {
            return this.act('post', endpoint('approve'), { confirmation: 'Aprovar esta tarefa?' });
        },

        archive() {
            return this.act('post', endpoint('archive'), {
                confirmation: 'Arquivar esta tarefa? Ela sairá do quadro, mas continuará acessível pelo arquivo.',
            });
        },

        perform(action) {
            return this.act('post', endpoint(action));
        },
    };
}
