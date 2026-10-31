import { Tab } from 'bootstrap';

const TAB_CONTAINER_SELECTOR = '[data-tab-hash]';

function showTabFromHash() {
    const target = window.location.hash;

    if (!target) {
        return;
    }

    const trigger = document.querySelector(`${TAB_CONTAINER_SELECTOR} [data-bs-target="${CSS.escape(target)}"]`);

    if (trigger) {
        Tab.getOrCreateInstance(trigger).show();
    }
}

document.addEventListener('shown.bs.tab', (event) => {
    if (event.target.closest(TAB_CONTAINER_SELECTOR)) {
        window.history.replaceState(null, '', event.target.dataset.bsTarget);
    }
});

document.addEventListener('DOMContentLoaded', showTabFromHash);
