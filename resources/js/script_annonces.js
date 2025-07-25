import axios from 'axios'; // si tu utilises Axios via Vite

// Favoris
async function toggleFavorite(annonceId) {
  try {
    const res = await axios.post(`/favoris/${annonceId}/toggle`);
    const btn = document.getElementById(`favorite-btn-${annonceId}`);
    const icon = btn.querySelector('svg');

    if (res.data.is_favorite) {
      icon.setAttribute('fill', 'currentColor');
      icon.classList.add('text-red-500');
      btn.classList.add('animate-bounce');
      setTimeout(() => btn.classList.remove('animate-bounce'), 600);
      showToast('Ajouté aux favoris !', 'success');
    } else {
      icon.setAttribute('fill', 'none');
      icon.classList.remove('text-red-500');
      showToast('Retiré des favoris', 'info');
    }
  } catch (e) {
    showToast('Erreur', 'error');
  }
}

// Toast
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  const color = type === 'success' ? 'border-green-500' : type === 'error' ? 'border-red-500' : 'border-blue-500';
  toast.className = `fixed top-24 right-4 z-50 bg-white/10 backdrop-blur border-l-4 ${color} border border-white/20 rounded-xl p-4 text-white transform translate-x-full transition-transform duration-300`;

  toast.innerHTML = `
    <div class="flex items-center space-x-2">
      <span>${message}</span>
      <button onclick="this.parentElement.parentElement.remove()" class="text-white/60 hover:text-white">&times;</button>
    </div>
  `;
  document.body.appendChild(toast);
  setTimeout(() => toast.classList.remove('translate-x-full'), 100);
  setTimeout(() => {
    toast.classList.add('translate-x-full');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Scroll animation
const observer = new IntersectionObserver(
  (entries) => entries.forEach((e) => {
    if (e.isIntersecting) {
      e.target.classList.add('opacity-100', 'translate-y-0');
      e.target.classList.remove('opacity-0', 'translate-y-4');
      observer.unobserve(e.target);
    }
  }),
  { threshold: 0.1 }
);

document.querySelectorAll('.animate-on-scroll').forEach((el) => {
  el.classList.add('opacity-0', 'translate-y-4', 'transition-all', 'duration-500');
  observer.observe(el);
});

// Check favoris au chargement
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[id^="favorite-btn-"]').forEach(async (btn) => {
    const id = btn.id.split('-')[2];
    try {
      const res = await axios.get(`/favoris/${id}/check`);
      const icon = btn.querySelector('svg');
      if (res.data.is_favorite) {
        icon.setAttribute('fill', 'currentColor');
        icon.classList.add('text-red-500');
      }
    } catch (e) {
      console.error(e);
    }
  });
});

// Rendre disponible globalement (si Blade inline)
window.toggleFavorite = toggleFavorite;
window.showToast = showToast;