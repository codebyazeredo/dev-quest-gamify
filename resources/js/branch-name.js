const MAX_SLUG_LENGTH = 50;

/** Prefixo da branch conforme o tipo (categoria) da tarefa; categorias desconhecidas usam "feature". */
const PREFIX_BY_CATEGORY = [
    [/bug|fix|corre/i, 'fix'],
    [/refactor|refator/i, 'refactor'],
    [/doc/i, 'docs'],
    [/improve|melhor/i, 'improvement'],
    [/test/i, 'test'],
    [/chore|manuten/i, 'chore'],
];

const DEFAULT_PREFIX = 'feature';

function slugify(text) {
    const slug = text
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    // Corta na última palavra inteira para a branch não terminar no meio de um termo.
    return slug.length > MAX_SLUG_LENGTH ? slug.slice(0, MAX_SLUG_LENGTH).replace(/-[^-]*$/, '') : slug;
}

/** Ex.: feature/dq-12-melhorar-acessibilidade-dos-formularios (sem o "dq-12" enquanto a tarefa não existe). */
export function suggestBranchName({ title, category, taskId }) {
    const slug = slugify(title ?? '');

    if (slug === '') {
        return '';
    }

    const prefix = PREFIX_BY_CATEGORY.find(([pattern]) => pattern.test(category ?? ''))?.[1] ?? DEFAULT_PREFIX;
    const key = taskId ? `dq-${taskId}-` : '';

    return `${prefix}/${key}${slug}`;
}
