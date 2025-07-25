/* Script page détail annonce */
document.addEventListener('DOMContentLoaded', () => {
  // Favori
  const favBtn = document.getElementById('favBtn');
  if (favBtn) {
    favBtn.addEventListener('click', async () => {
      const id = favBtn.dataset.id || favBtn.id.split('-').pop();
      try {
        const res = await fetch(`/favoris/${id}/toggle`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const data = await res.json();
        const svg = favBtn.querySelector('svg');
        svg.setAttribute('fill', data.is_favorite ? 'currentColor' : 'none');
        svg.classList.toggle('text-red-500', data.is_favorite);
      } catch (e) {
        console.error(e);
      }
    });
  }

  // Modals
  window.openImageModal = (src) => { document.getElementById('imageModal').classList.remove('hidden'); document.getElementById('modalImg').src = src; };
  window.closeImageModal = () => document.getElementById('imageModal').classList.add('hidden');

  window.showContactModal = () => document.getElementById('contactModal').classList.remove('hidden');
  window.closeContactModal = () => document.getElementById('contactModal').classList.add('hidden');

  // Partage
  window.shareAnnonce = () => {
    navigator.clipboard.writeText(location.href)
      .then(() => alert('Lien copié !'))
      .catch(() => alert('Impossible de copier le lien'));
  };

  // Suppression
  window.confirmDelete = () => {
    if (confirm('Supprimer cette annonce ?')) document.getElementById('deleteForm').submit();
  };

  // Vues simulées
  setTimeout(() => {
    const v = document.getElementById('view-count');
    if (v) v.textContent = `${parseInt(v.textContent) + 1} vues`;
  }, 3000);
});