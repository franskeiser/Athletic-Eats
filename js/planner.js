/**
 * planner.js
 * Handles meal slot selection, extras, macro totals, and remaining targets.
 * Recipe modal search uses AJAX (backend/search.php) for live filtering.
 */

document.addEventListener('DOMContentLoaded', async () => {

    // --- Initial data load (all recipes + extras) ---
    let recipes = [];
    let extras  = [];
    try {
        const res  = await fetch('backend/api.php');
        const data = await res.json();
        recipes = data.recipes;
        extras  = data.extras;
    } catch (e) {
        console.error('Could not load recipe data:', e);
    }

    // --- State ---
    let currentSlot   = null;
    let selectionMode = 'recipe';
    let modalDebounce = null;

    let plan = {
        breakfast: { recipe: null, extras: [] },
        lunch:     { recipe: null, extras: [] },
        dinner:    { recipe: null, extras: [] },
        snacks:    { recipe: null, extras: [] }
    };

    // --- DOM ---
    const modal       = document.getElementById('selection-modal');
    const modalTitle  = document.getElementById('modal-title');
    const modalGrid   = document.getElementById('modal-recipe-grid');
    const searchInput = document.getElementById('recipe-search');
    const closeBtn    = document.querySelector('.close-modal');

    const totalCalEl  = document.querySelector('.stats-grid .stat-item:nth-child(1) .stat-val');
    const totalProEl  = document.querySelector('.stats-grid .stat-item:nth-child(2) .stat-val');
    const totalCarbEl = document.querySelector('.stats-grid .stat-item:nth-child(3) .stat-val');
    const totalFatEl  = document.querySelector('.stats-grid .stat-item:nth-child(4) .stat-val');

    const remainingStats = document.getElementById('remaining-stats');
    const calcPrompt     = document.getElementById('calculator-prompt');
    const remCalEl       = document.getElementById('rem-cal');
    const remProEl       = document.getElementById('rem-pro');
    const remCarbEl      = document.getElementById('rem-carb');
    const remFatEl       = document.getElementById('rem-fat');

    // --- Macro totals + remaining ---
    const updateUI = () => {
        let totals = { cal: 0, pro: 0, carb: 0, fat: 0 };

        Object.values(plan).forEach(slot => {
            if (slot.recipe) {
                totals.cal  += slot.recipe.macros.cal;
                totals.pro  += slot.recipe.macros.pro;
                totals.carb += slot.recipe.macros.carb;
                totals.fat  += slot.recipe.macros.fat;
            }
            slot.extras.forEach(extra => {
                totals.cal  += extra.macros.cal;
                totals.pro  += extra.macros.pro;
                totals.carb += extra.macros.carb;
                totals.fat  += extra.macros.fat;
            });
        });

        totalCalEl.textContent  = totals.cal;
        totalProEl.textContent  = `${totals.pro}g`;
        totalCarbEl.textContent = `${totals.carb}g`;
        totalFatEl.textContent  = `${totals.fat}g`;

        const stored = localStorage.getItem('athleticEatsMacros');
        if (stored) {
            const targets = JSON.parse(stored);
            calcPrompt.classList.add('hidden');
            remainingStats.classList.remove('hidden');

            const rem = {
                cal:  targets.calories - totals.cal,
                pro:  targets.protein  - totals.pro,
                carb: targets.carbs    - totals.carb,
                fat:  targets.fats     - totals.fat
            };

            remCalEl.textContent  = rem.cal;
            remProEl.textContent  = `${rem.pro}g`;
            remCarbEl.textContent = `${rem.carb}g`;
            remFatEl.textContent  = `${rem.fat}g`;

            [remCalEl, remProEl, remCarbEl, remFatEl].forEach((el, i) => {
                el.style.color = Object.values(rem)[i] < 0 ? '#e74c3c' : '#2ecc71';
            });
        }
    };

    // --- Modal: render recipe items (local array, no fetch) ---
    const renderLocalRecipes = (list) => {
        modalGrid.innerHTML = list.map(r => `
            <div class="modal-recipe-item" data-id="${r.id}">
                <img src="${r.image}" alt="${r.title}" onerror="this.style.display='none'">
                <h4>${r.title}</h4>
                <p>${r.category.toUpperCase()} • ${r.macros.cal} Cal</p>
            </div>`).join('');
        attachModalItemListeners();
    };

    // --- Modal: AJAX recipe search ---
    const searchRecipesAjax = async (query) => {
        modalGrid.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);grid-column:1/-1;">Searching…</div>';

        try {
            const params = new URLSearchParams({ q: query });
            const res    = await fetch(`backend/search.php?${params}`);
            if (!res.ok) throw new Error(`Server error (${res.status})`);

            const data = await res.json();
            if (!data.success) throw new Error(data.error);

            if (data.recipes.length === 0) {
                modalGrid.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--text-muted);grid-column:1/-1;">No recipes found for "${query}".</div>`;
                return;
            }

            modalGrid.innerHTML = data.recipes.map(r => `
                <div class="modal-recipe-item" data-id="${r.id}">
                    <img src="${r.image}" alt="${r.title}" onerror="this.style.display='none'">
                    <h4>${r.title}</h4>
                    <p>${r.category.toUpperCase()} • ${r.macros.cal} Cal</p>
                </div>`).join('');
            attachModalItemListeners();

        } catch (err) {
            modalGrid.innerHTML = `<div style="text-align:center;padding:2rem;color:#d93025;grid-column:1/-1;">Search failed — ${err.message}</div>`;
        }
    };

    // --- Modal: render extras 
    const renderExtras = (query) => {
        const filtered = extras.filter(e => e.title.toLowerCase().includes(query.toLowerCase()));
        modalGrid.innerHTML = filtered.map(e => `
            <div class="modal-recipe-item" data-id="${e.id}" style="height:auto;">
                <div style="background:#f0f0f0;height:100px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2rem;">+</div>
                <h4 style="margin-top:1rem;">${e.title}</h4>
                <p>${e.macros.cal} Cal | ${e.macros.pro}g Pro</p>
            </div>`).join('');
        attachModalItemListeners();
    };

    // --- Unified render dispatch ---
    const renderModalItems = (query = '') => {
        if (selectionMode === 'extra') {
            renderExtras(query);
            return;
        }
        if (query === '') {
            renderLocalRecipes(recipes);
        } else {
            searchRecipesAjax(query);
        }
    };

    const attachModalItemListeners = () => {
        modalGrid.querySelectorAll('.modal-recipe-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = item.getAttribute('data-id');
                if (selectionMode === 'recipe') selectRecipe(id);
                else selectExtra(id);
            });
        });
    };

    // --- Select recipe ---
    const selectRecipe = (id) => {
        // Check full recipes array first; fall back to whatever is currently rendered
        let recipe = recipes.find(r => r.id === id);

        // If it came from an AJAX search result not in the initial load 
        if (!recipe) {
            console.warn('Recipe not found in local cache — id:', id);
            closeModal();
            return;
        }

        plan[currentSlot].recipe = recipe;

        const slotEl      = document.getElementById(`slot-${currentSlot}`);
        const cardEl      = slotEl.querySelector('.selected-meal-card');
        const descEl      = slotEl.querySelector('.meal-desc');
        const btnEl       = slotEl.querySelector('.btn-select');
        const addExtraBtn = slotEl.querySelector('.btn-add-extra');

        cardEl.innerHTML = `
            <img src="${recipe.image}" alt="${recipe.title}" onerror="this.style.display='none'">
            <div>
                <h4>${recipe.title}</h4>
                <p class="macro-preview">${recipe.macros.cal} Cal | ${recipe.macros.pro}g Protein</p>
            </div>`;

        cardEl.classList.remove('hidden');
        descEl.classList.add('hidden');
        addExtraBtn.classList.remove('hidden');
        btnEl.textContent      = 'Change Recipe';
        btnEl.style.background = '#eee';
        btnEl.style.color      = '#333';

        closeModal();
        updateUI();
    };

    const selectExtra = (id) => {
        const extra = extras.find(e => e.id === id);
        if (!extra) return;
        plan[currentSlot].extras.push(extra);
        renderExtrasList(currentSlot);
        closeModal();
        updateUI();
    };

    const renderExtrasList = (slot) => {
        const container = document.getElementById(`slot-${slot}`).querySelector('.extras-container');
        container.innerHTML = '';

        if (plan[slot].extras.length > 0) {
            container.classList.remove('hidden');
            plan[slot].extras.forEach((extra, index) => {
                const tag = document.createElement('div');
                tag.className = 'extra-tag';
                tag.innerHTML = `
                    <span>${extra.title}</span>
                    <span class="remove-extra" data-index="${index}">&times;</span>`;
                tag.querySelector('.remove-extra').addEventListener('click', () => {
                    plan[slot].extras.splice(index, 1);
                    renderExtrasList(slot);
                    updateUI();
                });
                container.appendChild(tag);
            });
        } else {
            container.classList.add('hidden');
        }
    };

    const openModal = (slot, mode) => {
        currentSlot   = slot;
        selectionMode = mode;
        modalTitle.textContent       = mode === 'recipe' ? 'Select a Recipe' : 'Add Extra Side Dish';
        searchInput.placeholder      = mode === 'recipe' ? 'Search recipes…' : 'Search sides (rice, egg, etc.)';
        modal.style.display          = 'block';
        document.body.style.overflow = 'hidden';
        searchInput.value = '';
        renderModalItems('');
    };

    const closeModal = () => {
        modal.style.display          = 'none';
        document.body.style.overflow = 'auto';
    };

    // --- Event listeners ---
    document.querySelectorAll('.btn-select').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-slot'), 'recipe'));
    });
    document.querySelectorAll('.btn-add-extra').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-slot'), 'extra'));
    });

    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    searchInput.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        clearTimeout(modalDebounce);
        if (selectionMode === 'extra') {
            renderModalItems(q);
        } else {
            modalDebounce = setTimeout(() => renderModalItems(q), 300);
        }
    });

    updateUI();
});
