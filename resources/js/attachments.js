import { pageActions } from './page-actions';

export function attachmentUploader(taskId, canUpload) {
    return {
        ...pageActions(),

        isDragging: false,

        pickFiles(event) {
            this.upload(Array.from(event.target.files));
            event.target.value = '';
        },

        dropFiles(event) {
            this.isDragging = false;
            this.upload(Array.from(event.dataTransfer.files));
        },

        pasteFiles(event) {
            const files = Array.from(event.clipboardData?.files ?? []);

            if (!canUpload || files.length === 0 || event.target.closest('input, textarea')) {
                return;
            }

            this.upload(files);
        },

        upload(files) {
            if (!canUpload || files.length === 0) {
                return null;
            }

            const body = new FormData();
            files.forEach((file) => body.append('files[]', file));

            return this.act('post', `/api/tasks/${taskId}/attachments`, { body });
        },

        remove(attachmentId, name) {
            return this.act('delete', `/api/attachments/${attachmentId}`, { confirmation: `Remover o anexo "${name}"?` });
        },
    };
}
