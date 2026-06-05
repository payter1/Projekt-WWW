
document.addEventListener("DOMContentLoaded", function() {
    // 1. Pobieramy aktualną ścieżkę z paska adresu (np. /aktualnosci.html)
    const currentPath = window.location.pathname;

    // 2. Pobieramy wszystkie linki z naszego menu
    const navLinks = document.querySelectorAll('.nav-link');

    // 3. Sprawdzamy każdy link
    navLinks.forEach(link => {
      // Usuwamy klasę active ze wszystkich (na wypadek, gdyby gdzieś została)
      link.classList.remove('active');
      link.removeAttribute('aria-current');

      // Jeśli atrybut href linku zawiera naszą aktualną ścieżkę
      if (link.getAttribute('href') === currentPath || currentPath.includes(link.getAttribute('href'))) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
  });

  // Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()