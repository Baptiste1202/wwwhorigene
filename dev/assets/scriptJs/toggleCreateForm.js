document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleForm');
    const formDiv = document.getElementById('form');
    const closeBtn = document.getElementById('closeForm');

    if (!toggleBtn || !formDiv || !closeBtn) {
        return;
    }

    toggleBtn.addEventListener('click', () => {
        formDiv.classList.remove('hidden');
        formDiv.classList.add('show');
        toggleBtn.style.display = 'none';
    });

    closeBtn.addEventListener('click', () => {
        formDiv.classList.remove('show');
        formDiv.classList.add('hidden');
        toggleBtn.style.display = 'inline-block';
    });
});