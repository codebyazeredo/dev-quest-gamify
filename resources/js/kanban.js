import { api, ApiError } from './api';
import { pageActions, stashToasts } from './page-actions';

const EDGE_SIZE = 72;
const EDGE_SPEED = 18;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('kanbanBoard', () => ({
        ...pageActions(),

        newColumnName: '',
        draggingTaskId: null,
        overColumnId: null,
        dropIndex: 0,
        dragHeight: 0,
        placeholder: null,
        search: '',
        category: '',
        priority: '',
        assignee: '',
        mine: false,
        late: false,

        get hasFilters() {
            return Boolean(this.search || this.category || this.priority || this.assignee || this.mine || this.late);
        },

        clearFilters() {
            this.search = '';
            this.category = '';
            this.priority = '';
            this.assignee = '';
            this.mine = false;
            this.late = false;
        },

        matches(card) {
            const data = card.dataset;

            return (!this.search || data.search.includes(this.search.toLowerCase()))
                && (!this.category || data.category === this.category)
                && (!this.priority || data.priority === this.priority)
                && (!this.assignee || data.assignee === this.assignee)
                && (!this.mine || data.mine === '1')
                && (!this.late || data.late === '1');
        },

        countVisible(section) {
            void [this.search, this.category, this.priority, this.assignee, this.mine, this.late];

            return [...section.querySelectorAll('[data-task-id]')].filter((card) => this.matches(card)).length;
        },

        startDrag(event, taskId) {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', String(taskId));
            this.dragHeight = event.currentTarget.offsetHeight;

            setTimeout(() => { this.draggingTaskId = taskId; }, 0);
        },

        dragOver(event, columnId, section) {
            if (this.draggingTaskId === null) {
                return;
            }

            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            const container = section.querySelector('.kanban-cards');

            this.overColumnId = columnId;
            this.autoScroll(event, container);

            const { before, index } = this.locateDropSlot(event.clientY, container);
            this.dropIndex = index;
            this.placePlaceholder(container, before);
        },

        locateDropSlot(pointerY, container) {
            const cards = [...container.querySelectorAll('[data-task-id]')]
                .filter((card) => Number(card.dataset.taskId) !== this.draggingTaskId);

            let index = 0;

            for (const card of cards) {
                if (card.offsetParent !== null) {
                    const rect = card.getBoundingClientRect();

                    if (pointerY < rect.top + rect.height / 2) {
                        return { before: card, index };
                    }
                }

                index++;
            }

            return { before: null, index };
        },

        placePlaceholder(container, before) {
            if (!this.placeholder) {
                this.placeholder = document.createElement('div');
                this.placeholder.className = 'kanban-placeholder';
            }

            this.placeholder.style.setProperty('--placeholder-height', `${this.dragHeight}px`);

            const alreadyThere = this.placeholder.parentNode === container
                && (before ? this.placeholder.nextElementSibling === before : this.placeholder.nextElementSibling === null);

            if (alreadyThere) {
                return;
            }

            container.insertBefore(this.placeholder, before);
        },

        autoScroll(event, container) {
            const board = this.$refs.board;
            const boardRect = board.getBoundingClientRect();

            if (event.clientX < boardRect.left + EDGE_SIZE) {
                board.scrollLeft -= EDGE_SPEED;
            } else if (event.clientX > boardRect.right - EDGE_SIZE) {
                board.scrollLeft += EDGE_SPEED;
            }

            const rect = container.getBoundingClientRect();

            if (event.clientY < rect.top + EDGE_SIZE / 2) {
                container.scrollTop -= EDGE_SPEED;
            } else if (event.clientY > rect.bottom - EDGE_SIZE / 2) {
                container.scrollTop += EDGE_SPEED;
            }
        },

        leaveColumn(event, section) {
            if (section.contains(event.relatedTarget)) {
                return;
            }

            this.overColumnId = null;
            this.removePlaceholder();
        },

        drop(columnId) {
            if (this.draggingTaskId === null) {
                return;
            }

            const taskId = this.draggingTaskId;
            const position = this.dropIndex;
            const card = document.querySelector(`[data-task-id="${taskId}"]`);

            if (card && this.placeholder && this.placeholder.parentNode) {
                this.placeholder.parentNode.insertBefore(card, this.placeholder);
            }

            this.endDrag();
            this.moveTask(taskId, columnId, position);
        },

        async moveTask(taskId, columnId, position) {
            try {
                await api.post(`/api/tasks/${taskId}/move`, { column_id: columnId, position });
            } catch (error) {
                if (!(error instanceof ApiError)) {
                    throw error;
                }

                stashToasts([{ type: 'error', title: error.message, message: '' }]);
                window.location.reload();
            }
        },

        endDrag() {
            this.draggingTaskId = null;
            this.overColumnId = null;
            this.removePlaceholder();
        },

        removePlaceholder() {
            if (this.placeholder && this.placeholder.parentNode) {
                this.placeholder.parentNode.removeChild(this.placeholder);
            }
        },
    }));
});
