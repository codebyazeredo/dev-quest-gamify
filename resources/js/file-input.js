import './jquery-global.js';
import 'bootstrap-fileinput/js/fileinput.js';
import 'bootstrap-fileinput/js/locales/pt-BR.js';
import 'bootstrap-fileinput/themes/bs5/theme.js';

const $ = window.jQuery;

const INPUT_SELECTOR = 'input[data-file-input]';
const KILOBYTES_PER_MEGABYTE = 1024;

const FILE_ICONS = {
    pdf: 'bi-file-earmark-pdf text-danger',
    doc: 'bi-file-earmark-word text-primary',
    docx: 'bi-file-earmark-word text-primary',
    xls: 'bi-file-earmark-spreadsheet text-success',
    xlsx: 'bi-file-earmark-spreadsheet text-success',
    csv: 'bi-file-earmark-spreadsheet text-success',
    ppt: 'bi-file-earmark-ppt text-warning',
    pptx: 'bi-file-earmark-ppt text-warning',
    txt: 'bi-file-earmark-text text-secondary',
    zip: 'bi-file-earmark-zip text-secondary',
};

const previewFileIconSettings = Object.fromEntries(
    Object.entries(FILE_ICONS).map(([extension, classes]) => [extension, `<i class="bi ${classes}" aria-hidden="true"></i>`]),
);

function optionsFor(input) {
    const { maxFiles, maxSizeKb, extensions } = input.dataset;
    const maxSizeMb = maxSizeKb / KILOBYTES_PER_MEGABYTE;

    return {
        theme: 'bs5',
        language: 'pt-BR',
        uploadUrl: '#',
        showUpload: false,
        showCancel: false,
        showClose: false,
        showCaption: false,
        showRemove: false,
        browseOnZoneClick: true,
        browseClass: 'btn btn-outline-primary btn-sm',
        browseLabel: 'Escolher arquivos',
        browseIcon: '<i class="bi bi-paperclip me-1" aria-hidden="true"></i>',
        dropZoneTitle: '<i class="bi bi-cloud-arrow-up d-block fs-3 mb-1" aria-hidden="true"></i>Arraste os arquivos aqui',
        dropZoneClickTitle: `<span class="d-block small">ou clique para escolher · até ${maxFiles} arquivos de ${maxSizeMb} MB</span>`,
        maxFileCount: Number(maxFiles),
        maxFileSize: Number(maxSizeKb),
        allowedFileExtensions: extensions.split(','),
        allowedPreviewTypes: ['image', 'video'],
        previewFileIcon: '<i class="bi bi-file-earmark" aria-hidden="true"></i>',
        previewFileIconSettings,
        fileActionSettings: { showZoom: false, showDrag: false, showUpload: false },
        removeFromPreviewOnError: true,
        autoReplace: false,
    };
}

function syncFormSubmission(input) {
    input.form?.addEventListener('submit', () => {
        const transfer = new DataTransfer();
        selectedFiles(input).forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    });
}

export function selectedFiles(input) {
    return $(input).fileinput('getFileList');
}

export function addFiles(input, files) {
    $(input).fileinput('readFiles', files);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(INPUT_SELECTOR).forEach((input) => {
        $(input).fileinput(optionsFor(input));
        syncFormSubmission(input);
    });
});
