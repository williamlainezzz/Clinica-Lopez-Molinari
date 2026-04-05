<script>
  (function () {
    const maxLen = 50;

    function bindUsernamePreview() {
      const $n = document.getElementById('PRIMER_NOMBRE');
      const $a = document.getElementById('PRIMER_APELLIDO');
      const $pill = document.getElementById('username-pill');
      const $out1 = document.getElementById('username-preview');
      const $out2 = document.getElementById('username-preview-inline');

      if (!$n || !$a) return;

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

      function up() {
        const u = mk($n.value, $a.value);
        if (($n.value.trim() || $a.value.trim())) {
          $pill?.classList.remove('hidden');
          if ($out1) $out1.textContent = u;
          if ($out2) $out2.textContent = u;
        } else {
          $pill?.classList.add('hidden');
          if ($out1) $out1.textContent = '';
          if ($out2) $out2.textContent = '';
        }
      }

      ['input', 'change'].forEach(e => {
        $n.addEventListener(e, up);
        $a.addEventListener(e, up);
      });

      up();
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
      requestAnimationFrame(bindUsernamePreview);
    });

    document.addEventListener('alpine:init', function () {
      if (@json((bool) ($errors->register->any() || session('modal') === 'register' || request()->query('modal') === 'register'))) {
        requestAnimationFrame(bindUsernamePreview);
      }
    });
  })();
</script>
