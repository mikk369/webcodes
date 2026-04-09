// Lead form handling — scoped per form
document.addEventListener('DOMContentLoaded', function () {
  const forms = document.querySelectorAll('.lead-form');

  forms.forEach((form) => {
    const notification = form.querySelector('.notification');

    const showNotification = (text, isError) => {
      if (!notification) return;
      notification.textContent = text;
      notification.classList.toggle('is-error', !!isError);
      notification.classList.add('is-visible');
      setTimeout(() => notification.classList.remove('is-visible'), 5000);
    };

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData,
      })
        .then((response) => response.json().catch(() => ({ success: false })))
        .then((data) => {
          if (data && data.success) {
            showNotification('Aitäh! Võtame sinuga peagi ühendust 👋', false);
            form.reset();
          } else {
            showNotification((data && data.message) || 'Midagi läks valesti. Proovi uuesti.', true);
          }
        })
        .catch(() => {
          showNotification('Midagi läks valesti. Proovi uuesti.', true);
        });
    });
  });
});
