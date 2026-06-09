document.addEventListener("DOMContentLoaded", function() {
  
  // --- 1. OBSŁUGA AKTYWNEGO LINKU W MENU ---
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('.nav-link');

  navLinks.forEach(link => {
    link.classList.remove('active');
    link.removeAttribute('aria-current');

    if (link.getAttribute('href') === currentPath || currentPath.includes(link.getAttribute('href'))) {
      link.classList.add('active');
      link.setAttribute('aria-current', 'page');
    }
  });

  // --- 2. WALIDACJA FORMULARZA I HASEŁ ---
  const forms = document.querySelectorAll('.needs-validation');
  const password = document.getElementById('password');
  const passwordR = document.getElementById('passwordR');

  // Funkcja sprawdzająca hasła
  function validatePasswords() {
      if (password && passwordR) {
          if (password.value !== passwordR.value) {
              passwordR.setCustomValidity('Hasła nie są identyczne!');
          } else {
              passwordR.setCustomValidity(''); // Hasła zgodne, usuwamy błąd
          }
      }
  }

  // Sprawdzanie podczas wpisywania
  if (password && passwordR) {
      password.addEventListener('input', validatePasswords);
      passwordR.addEventListener('input', validatePasswords);
  }

  // Aktywacja walidacji Bootstrap w momencie wysyłania formularza (submit)
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      
      // KRYTYCZNE: Wymuszamy sprawdzenie haseł w ułamku sekundy przed wysłaniem
      validatePasswords();

      // Jeśli formularz ma błędy (w tym różniące się hasła), blokujemy wysłanie
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      // Dodajemy klasę, która maluje na czerwono/zielono
      form.classList.add('was-validated');
    }, false);
  });

});