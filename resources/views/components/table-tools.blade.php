<div class="d-flex align-items-center mb-3 gap-2">
  <input id="q" type="search" class="form-control w-auto" placeholder="Buscar…">
  <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Imprimir</button>
</div>

<script>
(function(){
  const q = document.getElementById('q');
  q && q.addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(tr=>{
      tr.style.display = tr.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
  });

  // Ordenar por columna al hacer click en <th>
  document.querySelectorAll('table thead th').forEach((th, idx) => {
    th.style.cursor = 'pointer';
    let asc = true;
    th.addEventListener('click', () => {
      const rows = Array.from(th.closest('table').querySelectorAll('tbody tr'));
      rows.sort((a,b)=>{
        const A = a.children[idx].innerText.trim().toLowerCase();
        const B = b.children[idx].innerText.trim().toLowerCase();
        return asc ? A.localeCompare(B) : B.localeCompare(A);
      });
      const tbody = th.closest('table').querySelector('tbody');
      rows.forEach(r=>tbody.appendChild(r));
      asc = !asc;
    });
  });
})();
</script>
