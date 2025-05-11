document.addEventListener('DOMContentLoaded', () => {
    const timeline = document.querySelector('.linea-tiempo-container');
    let draggedItem = null;

    // Eventos para drag
    document.querySelectorAll('.evento').forEach(item => {
        item.setAttribute('draggable', true);
        
        item.addEventListener('dragstart', (e) => {
            draggedItem = item;
            setTimeout(() => item.style.opacity = '0.5', 0);
        });

        item.addEventListener('dragend', () => {
            setTimeout(() => draggedItem.style.opacity = '1', 0);
            draggedItem = null;
        });

        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(timeline, e.clientY);
            if (afterElement) {
                timeline.insertBefore(draggedItem, afterElement);
            } else {
                timeline.appendChild(draggedItem);
            }
        });
    });

    // Lógica para posicionamiento
    function getDragAfterElement(container, y) {
        const elements = [...container.querySelectorAll('.evento:not(.dragging)')];
        
        return elements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Guardar orden al soltar
    timeline.addEventListener('drop', async () => {
        const eventos = Array.from(document.querySelectorAll('.evento'));
        const orden = eventos.map(item => item.dataset.id);
        
        try {
            const response = await fetch('index.php?option=com_lineadetiempo&task=timeline.saveOrder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ orden })
            });
            const result = await response.json();
            if (!result.success) console.error('Error al guardar el orden');
        } catch (error) {
            console.error('Error:', error);
        }
    });
});