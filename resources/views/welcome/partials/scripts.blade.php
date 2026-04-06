<script>
  (function () {
    const maxLen = 50;
    let registerFieldsBound = false;
    let syncFrame = null;
    let submitBound = false;

    function splitFullName(value) {
      const parts = (value || '').trim().split(/\s+/).filter(Boolean);

      return {
        first: parts[0] || '',
        second: parts.slice(1).join(' '),
      };
    }

    function strip(s) {
      return (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function mk(n, a) {
      const first = (n || '').trim().charAt(0);
      const last = (a || '').trim().replace(/\s+/g, '');
      let base = (first + last).toLowerCase();
      base = strip(base).replace(/[^a-z0-9]/g, '');

      return (base || 'user').slice(0, maxLen);
    }

    function bindRegisterSubmit() {
      const form = document.getElementById('welcome-register-form');
      const submit = document.getElementById('welcome-register-submit');

      if (!form || !submit) {
        submitBound = false;
        return;
      }

      if (submitBound) {
        return;
      }

      form.addEventListener('submit', function () {
        submit.setAttribute('disabled', 'disabled');
        submit.classList.add('opacity-70', 'cursor-not-allowed');
        submit.textContent = 'Creando cuenta...';
      });

      submitBound = true;
    }

    function bindRegisterFields() {
      const $fullNames = document.getElementById('NOMBRES_COMPLETOS');
      const $fullSurnames = document.getElementById('APELLIDOS_COMPLETOS');
      const $firstName = document.getElementById('PRIMER_NOMBRE');
      const $secondName = document.getElementById('SEGUNDO_NOMBRE');
      const $firstSurname = document.getElementById('PRIMER_APELLIDO');
      const $secondSurname = document.getElementById('SEGUNDO_APELLIDO');
      const $out2 = document.getElementById('username-preview-inline');

      if (!$fullNames || !$fullSurnames || !$firstName || !$firstSurname) {
        registerFieldsBound = false;
        return;
      }

      function syncHiddenFields() {
        syncFrame = null;

        const names = splitFullName($fullNames.value);
        const surnames = splitFullName($fullSurnames.value);

        $firstName.value = names.first;
        if ($secondName) $secondName.value = names.second;
        $firstSurname.value = surnames.first;
        if ($secondSurname) $secondSurname.value = surnames.second;

        if ($out2) {
          $out2.textContent = ($fullNames.value.trim() || $fullSurnames.value.trim())
            ? mk(names.first, surnames.first)
            : '';
        }
      }

      function scheduleSync() {
        if (syncFrame !== null) {
          cancelAnimationFrame(syncFrame);
        }

        syncFrame = requestAnimationFrame(syncHiddenFields);
      }

      if (!registerFieldsBound) {
        ['input', 'change'].forEach(function (eventName) {
          $fullNames.addEventListener(eventName, scheduleSync);
          $fullSurnames.addEventListener(eventName, scheduleSync);
        });

        registerFieldsBound = true;
      }

      bindRegisterSubmit();
      scheduleSync();
    }

    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-open]');

      if (!btn) return;

      e.preventDefault();

      const which = btn.getAttribute('data-open');

      if (which === 'login') window.dispatchEvent(new CustomEvent('open-login'));
      if (which === 'register') window.dispatchEvent(new CustomEvent('open-register'));
    });

    window.addEventListener('open-register', function () {
      requestAnimationFrame(bindRegisterFields);
    });

    document.addEventListener('alpine:init', function () {
      if (@json((bool) ($errors->register->any() || session('modal') === 'register' || request()->query('modal') === 'register'))) {
        requestAnimationFrame(bindRegisterFields);
      }
    });
  })();
</script>
