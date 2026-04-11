// Lead form handling — scoped per form
document.addEventListener('DOMContentLoaded', function () {
  var forms = document.querySelectorAll('.lead-form');

  forms.forEach(function (form) {
    var notification = form.querySelector('.notification');

    var showNotification = function (text, isError) {
      if (!notification) return;
      notification.textContent = text;
      notification.classList.toggle('is-error', !!isError);
      notification.classList.add('is-visible');
      setTimeout(function () {
        notification.classList.remove('is-visible');
      }, 5000);
    };

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var formData = new FormData(form);
      var lang = localStorage.getItem('wc-lang') || 'et';
      var t = typeof translations !== 'undefined' ? translations[lang] : null;

      fetch(form.action, {
        method: 'POST',
        body: formData,
      })
        .then(function (response) {
          return response.json().catch(function () {
            return { success: false };
          });
        })
        .then(function (data) {
          if (data && data.success) {
            var msg = t ? t.notificationSuccess : 'Aitäh! Võtame sinuga peagi ühendust 👋';
            showNotification(msg, false);
            form.reset();
          } else {
            var fallback = t ? t.notificationError : 'Midagi läks valesti. Proovi uuesti.';
            showNotification((data && data.message) || fallback, true);
          }
        })
        .catch(function () {
          var msg = t ? t.notificationError : 'Midagi läks valesti. Proovi uuesti.';
          showNotification(msg, true);
        });
    });
  });
});
