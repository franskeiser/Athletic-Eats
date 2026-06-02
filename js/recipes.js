document.addEventListener('DOMContentLoaded', () => {
    const RECIPES_PER_PAGE = 6;
    let visibleCount      = RECIPES_PER_PAGE;
    let currentCategory   = 'all';
    let currentQuery      = '';
    let allFetchedRecipes = [];
    let debounceTimer     = null;

    // --- DOM ---
    const recipeGrid    = document.querySelector('.recipe-grid');
    const filterLinks   = document.querySelectorAll('.filter-bar a');
    const searchInput   = document.getElementById('gallery-search');
    const modal         = document.getElementById('recipe-modal');
    const modalBody     = document.getElementById('modal-body');
    const closeModalBtn = document.querySelector('.close-modal');

    // See More button
    const seeMoreBtn = document.createElement('button');
    seeMoreBtn.className   = 'btn-primary';
    seeMoreBtn.id          = 'see-more-btn';
    seeMoreBtn.textContent = 'See More Recipes';
    seeMoreBtn.style.cssText = 'margin:3rem auto;display:block;';
    recipeGrid.after(seeMoreBtn);

    // Status bar elements — defined in recipes.html, referenced here
    const spinnerEl = document.getElementById('search-spinner');
    const statusEl  = document.getElementById('search-status-text');

    // --- URL helpers ---

    // Read ?q= and ?category= from the current URL
    function readURLParams() {
        const params = new URLSearchParams(location.search);
        return {
            query:    params.get('q')        ?? '',
            category: params.get('category') ?? 'all',
        };
    }

    // Write search state into the URL bar without reloading the page
    function pushURL(query, category) {
        const params = new URLSearchParams();
        if (query)                          params.set('q',        query);
        if (category && category !== 'all') params.set('category', category);

        const newURL = params.toString()
            ? `${location.pathname}?${params}`
            : location.pathname;               // clean URL when no filters active

        history.pushState({ query, category }, '', newURL);
    }

    // Sync the search input and active filter button to match a given state
    function syncUI(query, category) {
        searchInput.value = query;
        filterLinks.forEach(link => {
            const text = link.textContent.trim().toLowerCase();
            link.classList.toggle('active',
                text === category || (category === 'all' && text === 'all')
            );
        });
    }

    // --- UI state helpers ---
    let spinnerTimer = null;

    function setLoading(on) {
        if (on) {
            spinnerEl.classList.add('visible');
            statusEl.className   = 'loading';
            statusEl.textContent = 'Loading…';
        } else {
            clearTimeout(spinnerTimer);
            spinnerTimer = setTimeout(() => spinnerEl.classList.remove('visible'), 400);
        }
    }

    function setStatus(count, query) {
        statusEl.className   = count > 0 ? 'success' : 'empty';
        statusEl.textContent = count === 0
            ? (query ? `No results for "${query}"` : 'No recipes found.')
            : `${count} recipe${count !== 1 ? 's' : ''} found`;
    }

    function setError(msg) {
        statusEl.className   = 'error';
        statusEl.textContent = msg;
    }

    // --- Card builder ---
    const createCard = (r) => {
        const article = document.createElement('article');
        article.className = 'recipe-card';
        article.setAttribute('data-category', r.category);
        article.setAttribute('data-id', r.id);
        article.innerHTML = `
            <img src="${r.image}" alt="${r.title}" onerror="this.style.display='none'">
            <div class="recipe-card-content">
                <h3>${r.title}</h3>
                <p>${r.description}</p>
            </div>`;
        article.addEventListener('click', () => openModal(r.id));
        return article;
    };

    // --- Render paginated cards from cached results ---
    const renderGallery = () => {
        recipeGrid.innerHTML = '';
        const toShow = allFetchedRecipes.slice(0, visibleCount);

        if (toShow.length === 0) {
            recipeGrid.innerHTML = '<p style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">No recipes match your search.</p>';
            seeMoreBtn.style.display = 'none';
            return;
        }
        toShow.forEach(r => recipeGrid.appendChild(createCard(r)));
        seeMoreBtn.style.display = visibleCount >= allFetchedRecipes.length ? 'none' : 'block';
    };

    // --- Core AJAX fetch ---
    async function fetchRecipes(query, category) {
        setLoading(true);

        const params = new URLSearchParams();
        if (query)                          params.set('q',        query);
        if (category && category !== 'all') params.set('category', category);

        try {
            const res = await fetch(`backend/search.php?${params}`);
            if (!res.ok) throw new Error(`Server error (${res.status})`);

            const data = await res.json();
            if (!data.success) throw new Error(data.error ?? 'Unexpected server error');

            allFetchedRecipes = data.recipes;
            visibleCount      = RECIPES_PER_PAGE;
            renderGallery();
            setStatus(data.count, query);
        } catch (err) {
            recipeGrid.innerHTML = `<p style="grid-column:1/-1;text-align:center;padding:3rem;color:#d93025;">${err.message}</p>`;
            seeMoreBtn.style.display = 'none';
            setError('Request failed — make sure XAMPP is running.');
        } finally {
            setLoading(false);
        }
    }

    // --- Event listeners ---
    seeMoreBtn.addEventListener('click', () => {
        visibleCount += RECIPES_PER_PAGE;
        renderGallery();
    });

    filterLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            currentCategory = e.target.textContent.trim().toLowerCase();
            syncUI(currentQuery, currentCategory);
            pushURL(currentQuery, currentCategory);   // update URL immediately
            fetchRecipes(currentQuery, currentCategory);
        });
    });

    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        currentQuery = e.target.value.trim();
        pushURL(currentQuery, currentCategory);       
        debounceTimer = setTimeout(() => fetchRecipes(currentQuery, currentCategory), 300);
    });

    window.addEventListener('popstate', (e) => {
        const state     = e.state ?? readURLParams();
        currentQuery    = state.query    ?? '';
        currentCategory = state.category ?? 'all';
        syncUI(currentQuery, currentCategory);
        fetchRecipes(currentQuery, currentCategory);
    });

    // --- Recipe modal ---
    const openModal = (id) => {
        const r = allFetchedRecipes.find(r => r.id === id);
        if (!r) return;

        modalBody.innerHTML = `
            <img src="${r.image}" alt="${r.title}" class="modal-hero" onerror="this.style.display='none'">
            <div class="modal-info">
                <h2>${r.title}</h2>
                <div class="modal-macros">
                    <div class="macro-item"><span class="macro-val">${r.macros.cal}</span><span class="macro-label">Calories</span></div>
                    <div class="macro-item"><span class="macro-val">${r.macros.pro}g</span><span class="macro-label">Protein</span></div>
                    <div class="macro-item"><span class="macro-val">${r.macros.carb}g</span><span class="macro-label">Carbs</span></div>
                    <div class="macro-item"><span class="macro-val">${r.macros.fat}g</span><span class="macro-label">Fat</span></div>
                </div>
                <div class="modal-section">
                    <h3>Ingredients</h3>
                    <ul class="modal-ingredients">${r.ingredients.map(i => `<li>${i}</li>`).join('')}</ul>
                </div>
                <div class="modal-section">
                    <h3>Instructions</h3>
                    <ol class="modal-steps">${r.steps.map(s => `<li>${s}</li>`).join('')}</ol>
                </div>
            </div>`;
        modal.style.display          = 'block';
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.style.display          = 'none';
        document.body.style.overflow = 'auto';
    };

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // --- Initial load: restore from URL if params exist ---
    const initial = readURLParams();
    currentQuery    = initial.query;
    currentCategory = initial.category;
    syncUI(currentQuery, currentCategory);

    // Replace the initial history entry with state so popstate works on first back press
    history.replaceState({ query: currentQuery, category: currentCategory }, '', location.href);

    fetchRecipes(currentQuery, currentCategory);
});
