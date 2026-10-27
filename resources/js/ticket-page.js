import { pageActions } from './page-actions';

/** Ações do atendimento de um chamado; cada uma chama a API e recarrega a página. */
export function ticketPage(ticketId) {
    const base = pageActions();
    const endpoint = (action) => `/api/tickets/${ticketId}/${action}`;

    return {
        ...base,

        replyBody: '',
        task: { column_id: '', category_id: '', priority_id: '' },

        reply() {
            return this.act('post', endpoint('messages'), { body: { body: this.replyBody } });
        },

        changeStatus(status) {
            return this.act('put', endpoint('status'), { body: { status } });
        },

        convert() {
            return this.act('post', endpoint('task'), { body: this.task });
        },
    };
}
