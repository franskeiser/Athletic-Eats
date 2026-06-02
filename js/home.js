document.addEventListener('DOMContentLoaded', () => {

    // --- Hero reveal animation ---
    const heroTitle = document.querySelector('.hero h1');
    const heroText  = document.querySelector('.hero p');
    const heroBtn   = document.querySelector('.hero .btn-primary');

    [heroTitle, heroText, heroBtn].forEach((el, i) => {
        if (!el) return;
        el.style.opacity   = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `all 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) ${i * 0.2}s`;
        setTimeout(() => {
            el.style.opacity   = '1';
            el.style.transform = 'translateY(0)';
        }, 100);
    });

    const logo = document.querySelector('.logo a');
    logo.addEventListener('click', (e) => {
        if (window.location.pathname.endsWith('index.html') || window.location.pathname === '/') {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // --- Featured Recipes (AJAX) ---
    const featuredGrid = document.getElementById('featured-grid');
    if (!featuredGrid) return;

    async function loadFeaturedRecipes() {
        try {
            const res = await fetch('backend/api.php');
            if (!res.ok) throw new Error('Server error');

            const data     = await res.json();
            const featured = data.recipes.slice(0, 3);

            if (featured.length === 0) {
                featuredGrid.innerHTML = '<p style="color:var(--text-muted)">No recipes in the database yet.</p>';
                return;
            }

            featuredGrid.innerHTML = featured.map(r => `
                <a href="recipes.html" class="featured-card">
                    <img src="${r.image}" alt="${r.title}" onerror="this.style.display='none'">
                    <div class="featured-card-body">
                        <span class="featured-badge">${r.category}</span>
                        <h3>${r.title}</h3>
                        <p>${r.macros.cal} Cal &bull; ${r.macros.pro}g Protein</p>
                    </div>
                </a>`).join('');

        } catch (err) {
            // Server not running — hide the section gracefully
            document.getElementById('featured-section')?.remove();
        }
    }

    loadFeaturedRecipes();
});
