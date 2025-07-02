document.addEventListener('DOMContentLoaded', () => {
    const messages = document.querySelectorAll('.flash-message');
    messages.forEach(msg => {
        msg.style.display = 'block';
        msg.style.opacity = '1';
        setTimeout(() => {
            msg.style.opacity = '0';
            setTimeout(() => {
                msg.style.display = 'none';
            }, 1000);
        }, 5000);
    });
});
