<script>
  (function () {
    function isInvalidWebAuthnHost(hostname) {
      return /^(?:\d{1,3}\.){3}\d{1,3}$/.test(hostname) || hostname.includes(':');
    }

    function showWebAuthnMessage(modal, text, type) {
      const message = modal.querySelector('[data-webauthn-login-message]');

      if (!message) {
        return;
      }

      message.textContent = text;
      message.className = 'mb-3 rounded-2xl border px-4 py-3 text-sm font-medium';

      if (type === 'error') {
        message.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
        return;
      }

      message.classList.add('border-blue-200', 'bg-blue-50', 'text-blue-700');
    }

    function base64UrlToBuffer(value) {
      const base64 = value.replace(/-/g, '+').replace(/_/g, '/');
      const padded = base64.padEnd(base64.length + ((4 - base64.length % 4) % 4), '=');
      const binary = atob(padded);
      const bytes = new Uint8Array(binary.length);

      for (let i = 0; i < binary.length; i += 1) {
        bytes[i] = binary.charCodeAt(i);
      }

      return bytes.buffer;
    }

    function bufferToBase64Url(buffer) {
      const bytes = new Uint8Array(buffer);
      let binary = '';

      bytes.forEach(byte => {
        binary += String.fromCharCode(byte);
      });

      return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
    }

    async function postJson(url, body) {
      const token = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify(body),
      });
      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(data.message || 'No se pudo completar la verificacion biometrica.');
      }

      if (!response.headers.get('content-type')?.includes('application/json')) {
        throw new Error('La sesion cambio mientras se solicitaba la biometria. Recarga la pagina e intenta de nuevo.');
      }

      return data;
    }

    function friendlyWebAuthnError(error) {
      const message = error?.message || '';
      const name = error?.name || '';

      if (name === 'NotAllowedError' || message.includes('timed out') || message.includes('not allowed')) {
        return 'Cancelaste la verificacion biometrica o se agoto el tiempo. Puedes intentarlo de nuevo cuando quieras.';
      }

      if (name === 'AbortError') {
        return 'La verificacion biometrica fue cancelada. Puedes intentarlo nuevamente.';
      }

      if (name === 'NotSupportedError') {
        return 'Este dispositivo o navegador no permite usar biometria para iniciar sesion.';
      }

      return message || 'No se pudo iniciar sesion con biometria.';
    }

    function initializeWebAuthnButtons(root) {
      root.querySelectorAll('[data-webauthn-login-button]').forEach(button => {
        if (button.dataset.initialized === '1') {
          return;
        }

        button.dataset.initialized = '1';
        button.classList.remove('hidden');
        button.classList.add('inline-flex');

        const modal = button.closest('.modal-panel') || document;

        if (!window.PublicKeyCredential) {
          button.disabled = true;
          showWebAuthnMessage(modal, 'Este navegador no soporta inicio con biometria.', 'error');
          return;
        }

        if (isInvalidWebAuthnHost(window.location.hostname)) {
          button.disabled = true;
          showWebAuthnMessage(modal, 'Para usar biometria en pruebas locales, abre el sistema como http://localhost:8000 en lugar de 127.0.0.1.', 'error');
        }
      });
    }

    document.addEventListener('click', function (event) {
      const webauthnButton = event.target.closest('[data-webauthn-login-button]');

      if (webauthnButton) {
        const modal = webauthnButton.closest('.modal-panel') || document;
        const loginInput = modal.querySelector('input[name="login"]');
        const login = loginInput ? loginInput.value.trim() : '';

        event.preventDefault();

        webauthnButton.disabled = true;
        showWebAuthnMessage(modal, login
          ? 'Solicitando verificacion biometrica del dispositivo...'
          : 'Buscando biometria registrada en este dispositivo...');

        (async () => {
          try {
            const optionsResponse = await postJson('{{ route('webauthn.authentication-options') }}', login ? { login } : {});
            const publicKey = optionsResponse.publicKey;

            if (!publicKey || !publicKey.challenge) {
              throw new Error(optionsResponse.message || 'No se recibio el reto biometrico. Recarga la pagina e intenta de nuevo.');
            }

            publicKey.challenge = base64UrlToBuffer(publicKey.challenge);
            if (Array.isArray(publicKey.allowCredentials)) {
              publicKey.allowCredentials = publicKey.allowCredentials.map(credential => ({
                ...credential,
                id: base64UrlToBuffer(credential.id),
              }));
            }

            const assertion = await navigator.credentials.get({ publicKey });
            const result = await postJson('{{ route('webauthn.authenticate') }}', {
              id: assertion.id,
              rawId: bufferToBase64Url(assertion.rawId),
              type: assertion.type,
              response: {
                clientDataJSON: bufferToBase64Url(assertion.response.clientDataJSON),
                authenticatorData: bufferToBase64Url(assertion.response.authenticatorData),
                signature: bufferToBase64Url(assertion.response.signature),
              },
            });

            window.location.href = result.redirect || '{{ route('dashboard') }}';
          } catch (error) {
            showWebAuthnMessage(modal, friendlyWebAuthnError(error), 'error');
            webauthnButton.disabled = false;
          }
        })();

        return;
      }

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

    initializeWebAuthnButtons(document);

    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
          if (node instanceof HTMLElement) {
            initializeWebAuthnButtons(node);
          }
        });
      });
    });

    observer.observe(document.body, { childList: true, subtree: true });
  })();
</script>
