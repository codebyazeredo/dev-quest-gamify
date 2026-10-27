export function onlyDigits(value) {
    return String(value ?? '').replace(/\D/g, '');
}

export function formatCpf(value) {
    return onlyDigits(value)
        .slice(0, 11)
        .replace(/^(\d{3})(\d)/, '$1.$2')
        .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1-$2');
}

export function formatPhone(value) {
    const digits = onlyDigits(value).slice(0, 11);

    return digits
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(digits.length > 10 ? /(\d{5})(\d)/ : /(\d{4})(\d)/, '$1-$2');
}

export function formatCnpj(value) {
    return onlyDigits(value)
        .slice(0, 14)
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}
