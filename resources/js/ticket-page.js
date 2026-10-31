import { selectedFiles } from './file-input';
import { pageActions } from './page-actions';

export function ticketPage(ticketId, boards, ticketTitle) {
    const endpoint = (action) => `/api/tickets/${ticketId}/${action}`;

    return {
        ...pageActions(),

        boards,
        replyBody: '',
        replyMode: 'public',
        task: { board_id: '', column_id: '', title: ticketTitle, category_id: '', priority_id: '' },

        columns() {
            return boards.find((board) => String(board.id) === String(this.task.board_id))?.columns ?? [];
        },

        reply() {
            const body = new FormData();
            body.append('body', this.replyBody);
            body.append('is_internal', this.replyMode === 'internal' ? '1' : '0');
            selectedFiles(this.$refs.replyFiles).forEach((file) => body.append('files[]', file));

            return this.act('post', endpoint('messages'), { body });
        },

        changeStatus(status) {
            return this.act('put', endpoint('status'), { body: { status } });
        },

        changePriority(priority) {
            return this.act('put', endpoint('priority'), { body: { priority } });
        },

        changeAssignee(userId) {
            return this.act('put', endpoint('assignee'), { body: { user_id: userId === '' ? null : Number(userId) } });
        },

        claim() {
            return this.act('post', endpoint('claim'));
        },

        convert() {
            return this.act('post', endpoint('task'), { body: this.task });
        },
    };
}
