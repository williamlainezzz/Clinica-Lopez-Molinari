<script>
  (function () {
    document.addEventListener('click', function (event) {
      const button = event.target.closest('[data-open]');

      if (!button) {
        return;
      }

      event.preventDefault();

      const target = button.getAttribute('data-open');

      if (target === 'login') {
        window.dispatchEvent(new CustomEvent('open-login'));
      }
    });
  })();
</script>
