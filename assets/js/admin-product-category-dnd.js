(function () {
    'use strict';

    const dndConfig = window.bccProductCategoryDnd || {};
    const ajaxUrl = dndConfig.ajaxUrl;
    const nonce = dndConfig.nonce;

    if (!ajaxUrl || !nonce) {
        return;
    }

    const tableBody = document.querySelector('#the-list');
    if (!tableBody) {
        return;
    }

    const style = document.createElement('style');
    style.textContent = `
        #the-list tr { cursor: grab; }
        #the-list tr.bcc-dnd-dragging { opacity: 0.45; }
        #the-list tr.bcc-dnd-target { outline: 2px dashed #2271b1; outline-offset: -2px; }
    `;
    document.head.appendChild(style);

    let dragTermId = 0;

    const getTermIdFromRow = function (row) {
        if (!row || !row.id || row.id.indexOf('tag-') !== 0) {
            return 0;
        }
        const value = parseInt(row.id.replace('tag-', ''), 10);
        return Number.isFinite(value) ? value : 0;
    };

    const setRowsDraggable = function () {
        const rows = tableBody.querySelectorAll('tr[id^="tag-"]');
        rows.forEach(function (row) {
            row.setAttribute('draggable', 'true');

            row.addEventListener('dragstart', function () {
                dragTermId = getTermIdFromRow(row);
                row.classList.add('bcc-dnd-dragging');
            });

            row.addEventListener('dragend', function () {
                row.classList.remove('bcc-dnd-dragging');
                dragTermId = 0;
                tableBody.querySelectorAll('.bcc-dnd-target').forEach(function (item) {
                    item.classList.remove('bcc-dnd-target');
                });
            });

            row.addEventListener('dragover', function (event) {
                if (!dragTermId) {
                    return;
                }
                event.preventDefault();
                row.classList.add('bcc-dnd-target');
            });

            row.addEventListener('dragleave', function () {
                row.classList.remove('bcc-dnd-target');
            });

            row.addEventListener('drop', function (event) {
                event.preventDefault();
                row.classList.remove('bcc-dnd-target');

                const targetTermId = getTermIdFromRow(row);
                if (!dragTermId || !targetTermId || dragTermId === targetTermId) {
                    return;
                }

                const payload = new URLSearchParams();
                payload.set('action', 'bcc_set_product_category_parent');
                payload.set('nonce', nonce);
                payload.set('term_id', String(dragTermId));
                payload.set('new_parent', String(targetTermId));

                fetch(ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    credentials: 'same-origin',
                    body: payload.toString()
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (result) {
                        if (!result || !result.success) {
                            const message = result && result.data && result.data.message ? result.data.message : 'Не удалось обновить иерархию.';
                            alert(message);
                            return;
                        }

                        window.location.reload();
                    })
                    .catch(function () {
                        alert('Ошибка сети при обновлении иерархии.');
                    });
            });
        });
    };

    setRowsDraggable();
})();
