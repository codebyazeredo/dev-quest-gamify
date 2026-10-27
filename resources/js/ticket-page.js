import { pageActions } from './page-actions';

export function ticketPage(ticketId, boards, ticketTitle) {
    const endpoint = (action) => `/api/tickets/${ticketId}/${action}`;

    return {
        ...pageActions(),

        boards,
        replyBody: '',
        replyFiles: [],
        task: { board_id: '', column_id: '', title: ticketTitle, category_id: '', priority_id: '' },

        columns() {
            return boards.find((board) => String(board.id) === String(this.task.board_id))?.columns ?? [];
        },

        pickReplyFiles(event) {
            this.replyFiles = Array.from(event.target.files);
        },

        reply() {
            const body = new FormData();
            body.append('body', this.replyBody);
            this.replyFiles.forEach((file) => body.append('files[]', file));

            return this.act('post', endpoint('messages'), { body });
        },

        changeStatus(status) {
            return this.act('put', endpoint('status'), { body: { status } });
        },

        convert() {
            return this.act('post', endpoint('task'), { body: this.task });
        },
    };
}
