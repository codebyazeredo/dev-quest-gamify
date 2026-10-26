import { crudTable } from './crud-table';

const EMPTY_CHALLENGE = {
    name: '',
    description: '',
    icon: 'flag',
    type: 1,
    target: 1,
    xp_reward: 10,
    starts_at: '',
    ends_at: '',
    active: true,
    award_title: false,
    reward_title_name: '',
    reward_title_icon: 'medal',
};

function toDatetimeLocal(date) {
    const pad = (value) => String(value).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function startOfDay(date) {
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
}

function endOfDay(date) {
    return new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59);
}

/** Início e fim de cada atalho de período do formulário. */
const PERIODS = {
    today: (now) => [startOfDay(now), endOfDay(now)],
    week: (now) => {
        const daysSinceMonday = (now.getDay() + 6) % 7;
        const monday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - daysSinceMonday);
        const sunday = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + 6);

        return [startOfDay(monday), endOfDay(sunday)];
    },
    next7: (now) => [now, endOfDay(new Date(now.getFullYear(), now.getMonth(), now.getDate() + 7))],
    month: (now) => [
        new Date(now.getFullYear(), now.getMonth(), 1),
        endOfDay(new Date(now.getFullYear(), now.getMonth() + 1, 0)),
    ],
};

export function challengesTable() {
    const table = crudTable({
        endpoint: '/api/challenges',
        emptyForm: EMPTY_CHALLENGE,
        filters: { search: '', status: '' },
    });

    return {
        ...table,

        openCreate() {
            table.openCreate.call(this);
            this.applyPeriod('week');
        },

        applyPeriod(preset) {
            const [start, end] = PERIODS[preset](new Date());

            this.form.starts_at = toDatetimeLocal(start);
            this.form.ends_at = toDatetimeLocal(end);
        },
    };
}
