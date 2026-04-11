// Lead form handling — scoped per form
document.addEventListener('DOMContentLoaded', function () {
  var CONTACT_EMAIL = 'info@webcodes.ee';
  var forms = document.querySelectorAll('.lead-form');

  forms.forEach(function (form) {
    var notification = form.querySelector('.notification');
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalBtnText = submitBtn ? submitBtn.textContent : '';

    var getT = function () {
      var lang = localStorage.getItem('wc-lang') || 'et';
      return typeof translations !== 'undefined' ? translations[lang] : null;
    };

    // Build notification content via DOM nodes (no innerHTML — safe even if
    // message text ever contains user-controlled characters in the future).
    var renderNotification = function (text, isError) {
      if (!notification) return;
      notification.textContent = text;

      if (isError) {
        var t = getT();
        var fallbackLabel = t && t.notificationErrorFallback
          ? t.notificationErrorFallback
          : 'Või kirjuta otse:';

        // Append " <fallback label> <mailto link>"
        var labelNode = document.createTextNode(' ' + fallbackLabel + ' ');
        var link = document.createElement('a');
        link.href = 'mailto:' + CONTACT_EMAIL;
        link.textContent = CONTACT_EMAIL;
        link.className = 'notification-fallback-link';

        notification.appendChild(labelNode);
        notification.appendChild(link);
      }

      notification.classList.toggle('is-error', !!isError);
      notification.classList.add('is-visible');
      setTimeout(function () {
        notification.classList.remove('is-visible');
      }, 8000);
    };

    var setPending = function (isPending) {
      if (!submitBtn) return;
      submitBtn.disabled = isPending;
      submitBtn.classList.toggle('is-pending', isPending);
      if (isPending) {
        var t = getT();
        submitBtn.textContent = (t && t.btnSending) ? t.btnSending : 'Saadan...';
      } else {
        submitBtn.textContent = originalBtnText;
      }
    };

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (submitBtn && submitBtn.disabled) return;

      var formData = new FormData(form);
      setPending(true);

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
          var t = getT();
          if (data && data.success) {
            var successMsg = t ? t.notificationSuccess : 'Aitäh! Võtame sinuga peagi ühendust';
            renderNotification(successMsg, false);
            form.reset();
          } else {
            var errorMsg = (data && data.message)
              || (t ? t.notificationError : 'Midagi läks valesti. Proovi uuesti.');
            renderNotification(errorMsg, true);
          }
        })
        .catch(function () {
          var t = getT();
          var errorMsg = t ? t.notificationError : 'Midagi läks valesti. Proovi uuesti.';
          renderNotification(errorMsg, true);
        })
        .then(function () {
          setPending(false);
        });
    });
  });
});
