//STICKI NAVIGATION \\
const sectionHero = document.querySelector('.section-hero');
const obs = new IntersectionObserver(
  function (entries) {
    const ent = entries[0];
    if (ent.isIntersecting === false) {
      document.body.classList.add('sticky');
    } else {
      document.body.classList.remove('sticky');
    }
  },
  {
    //in the viewport (inside the whole browser window)
    root: null,
    threshold: 0,
    rootMargin: '-96px',
  }
);
obs.observe(sectionHero);

/// Open close mobile nav \\\
const openMenu = document.querySelector('.btn-mobile-nav');
const header = document.querySelector('.header');

openMenu.addEventListener('click', (e) => {
  e.preventDefault();
  header.classList.toggle('nav-open');
});

//HANDLE SUBMITED MESSAGE\\
document.addEventListener('DOMContentLoaded', function () {
  // Get a reference to the form and the notification div
  const form = document.querySelector('.cta-form');
  const messageSentNotification = document.getElementById('messageSent');

  // Add an event listener for the form submission
  form.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Serialize the form data
    const formData = new FormData(form);

    // Perform a POST request using fetch
    fetch('php/send_email.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        // Handle the response as needed
        if (data.success) {
          // Display the message sent notification
          messageSentNotification.style.display = 'block';

          // Clear the form fields
          form.reset();
          // Scroll the page 1 rem up
          window.scrollBy(0, -80);
          // Hide the notification after 3 seconds
          setTimeout(function () {
            messageSentNotification.style.display = 'none';
          }, 3000); // 3 seconds
        } else {
          // Handle the case where the email couldn't be sent
          console.error('Message could not be sent.');
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  });

/////COOKIE\\\\\
  let cookieConsent = document.getElementById("cookieConsent");
  let acceptCookiesButton = document.getElementById("acceptCookies");
  let denyCookiesButton = document.getElementById("denyCookies");

    // Kontrolli, kas küpsis on juba määratud
    if (!getCookie("cookiesAccepted")) {
        cookieConsent.style.display = "block";
    }

    // Küpsiste nõusoleku käsitlemine
    acceptCookiesButton.addEventListener("click", function() {
        setCookie("cookiesAccepted", "googleAnalytics: true", 365);
        hideCookieConsent();
    });

    // Küpsiste keeldumise käsitlemine
    denyCookiesButton.addEventListener("click", function() {
        hideCookieConsent();
    });

    //küpsise teavituse peitmine
    function hideCookieConsent() {
      cookieConsent.classList.add("hide");
      setTimeout(function() {
        cookieConsent.style.display = "none;"
      }, 1000);
    }

    // Küpsise seadistamine
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    // Küpsise hankimine
    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for(let i=0;i < ca.length;i++) {
            let c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    // Initialize Google Analytics
    function initializeGoogleAnalytics() {
      gtag('config', 'G-6QNEM2WCLK');
    }

  // Check if cookies are already accepted
  if (getCookie("cookiesAccepted") === "googleAnalytics: true") {
    initializeGoogleAnalytics();
  }
});
