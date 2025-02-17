document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.querySelector('#datatablesSimple');
    if (tableElement) {
        const dataTable = new DataTable(tableElement, {
            perPage: 10,
            searchable: true,
        });
    } else {
        console.error('ไม่พบ element table ที่อ้างอิง');
    }
});
