function formatRupiah(number) {
    if (isNaN(number)) return 'Rp 0,00';

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}

function formatRupiahReturned(value) {
    const number = value.replace(/[^\d]/g, '');
    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2
    }).format(number / 100);
    return formatted;
}