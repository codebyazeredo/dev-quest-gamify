import { addFiles, selectedFiles } from './file-input';
import { pageActions } from './page-actions';

export function attachmentUploader(taskId, canUpload) {
    return {
        ...pageActions(),

        pasteFiles(event) {
            const files = Array.from(event.clipboardData?.files ?? []);

            if (!canUpload || files.length === 0 || event.target.closest('input, textarea')) {
                return;
            }

            addFiles(this.$refs.picker, files);
        },

        upload() {
            const files = selectedFiles(this.$refs.picker);

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
