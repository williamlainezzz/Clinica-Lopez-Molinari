<script>
  (function () {
    const maxLen = 50;

    function splitFullName(value) {
      const parts = (value || '').trim().split(/\s+/).filter(Boolean);
      return {
        first: parts[0] || '',
        second: parts.slice(1).join(' '),
      };
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
        return;
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

      function syncHiddenFields() {
        const names = splitFullName($fullNames.value);
        const surnames = splitFullName($fullSurnames.value);

        $firstName.value = names.first;
        if ($secondName) $secondName.value = names.second;
        $firstSurname.value = surnames.first;
        if ($secondSurname) $secondSurname.value = surnames.second;

        const username = mk(names.first, surnames.first);
        if (($fullNames.value.trim() || $fullSurnames.value.trim())) {
          if ($out2) $out2.textContent = username;
        } else {
          if ($out2) $out2.textContent = '';
        }
      }

      ['input', 'change'].forEach(e => {
        $fullNames.addEventListener(e, syncHiddenFields);
        $fullSurnames.addEventListener(e, syncHiddenFields);
      });

      syncHiddenFields();
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
