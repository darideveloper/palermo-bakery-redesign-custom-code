document.addEventListener('DOMContentLoaded', () => {
    const storageKey = 'my_cake_favs';

    // 1. SYNC TO WORDPRESS
    const syncToServer = (favArray) => {
        if (typeof cakeFavsData === 'undefined' || !cakeFavsData.isLoggedIn) return;

        const formData = new URLSearchParams();
        formData.append('action', 'save_user_favorites');
        formData.append('nonce', cakeFavsData.nonce);
        formData.append('favs', favArray.join(','));

        fetch(cakeFavsData.ajaxUrl, {
            method: 'POST',
            body: formData,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).catch(err => console.error('Sync Error:', err));
    };

    // 2. UPDATE VISUAL UI (Hearts & Header)
    const updateUI = (favArray) => {
        document.querySelectorAll('.my-custom-fav-btn').forEach(btn => {
            const productBlock = btn.closest('.product-inner');
            if (!productBlock) return;

            const yithEl = productBlock.querySelector('.yith-wcwl-add-to-wishlist');
            if (yithEl && favArray.includes(yithEl.dataset.fragmentRef)) {
                btn.classList.add('is-favorited');
                btn.innerHTML = '❤️';
            } else {
                btn.classList.remove('is-favorited');
                btn.innerHTML = '🤍';
            }
        });

        const headerCounter = document.querySelector('.mini-wishlist .number');
        if (headerCounter) headerCounter.textContent = favArray.length;
    };

    // 3. TOGGLE FAVORITE LOGIC
    window.testToggleFav = (productId) => {
        productId = String(productId);
        let favs = JSON.parse(localStorage.getItem(storageKey)) || [];

        if (favs.includes(productId)) {
            favs = favs.filter(id => id !== productId);
        } else {
            favs.push(productId);
        }

        localStorage.setItem(storageKey, JSON.stringify(favs));
        updateUI(favs);
        syncToServer(favs);
    };

    // 4. CLICK EVENT LISTENER
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.my-custom-fav-btn');
        if (!btn) return;
        e.preventDefault();

        const productBlock = btn.closest('.product-inner');
        if (!productBlock) return;

        const yithElement = productBlock.querySelector('.yith-wcwl-add-to-wishlist');
        if (yithElement) window.testToggleFav(yithElement.dataset.fragmentRef);
    });

    // 5. RENDER THE MASONRY FAVORITES PAGE
    const renderFavoritesPage = (favs) => {
        const listContainer = document.getElementById('favorite-cakes-list');
        const loadingMsg = document.getElementById('fav-loading-msg');
        const sharePageBtn = document.getElementById('share-favs-page-btn');

        if (!listContainer) return; // Exit if we aren't on the favorites page

        if (favs.length === 0) {
            loadingMsg.textContent = "Your favorites list is empty.";
            return;
        }

        const formData = new URLSearchParams();
        formData.append('action', 'render_favorite_products');
        formData.append('nonce', cakeFavsData.nonce);
        formData.append('favs', favs.join(','));

        fetch(cakeFavsData.ajaxUrl, {
            method: 'POST',
            body: formData,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                loadingMsg.style.display = 'none';
                listContainer.innerHTML = response.data;
                sharePageBtn.style.display = 'inline-block';
            } else {
                loadingMsg.textContent = "Error loading favorites.";
            }
        })
        .catch(() => { loadingMsg.textContent = "Connection error."; });

        // Share Button Logic
        if (sharePageBtn) {
            sharePageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const shareUrl = window.location.origin + '/gallery/?shared_favs=' + favs.join(',');
                navigator.clipboard.writeText(shareUrl).then(() => {
                    const originalText = sharePageBtn.innerHTML;
                    sharePageBtn.innerHTML = '✅ Link Copied!';
                    setTimeout(() => { sharePageBtn.innerHTML = originalText; }, 2000);
                });
            });
        }
    };

    // 6. INITIALIZE ON LOAD
    const initFavorites = () => {
        let localFavs = JSON.parse(localStorage.getItem(storageKey)) || [];

        // Check URL for shared links
        const urlParams = new URLSearchParams(window.location.search);
        const sharedIds = urlParams.get('shared_favs');

        if (sharedIds) {
            if (confirm("Would you like to add these shared cakes to your favorites?")) {
                const incomingFavs = sharedIds.split(',').filter(Boolean);
                localFavs = [...new Set([...localFavs, ...incomingFavs])];
                localStorage.setItem(storageKey, JSON.stringify(localFavs));
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // Sync with server if logged in
        if (typeof cakeFavsData !== 'undefined' && cakeFavsData.isLoggedIn) {
            const formData = new URLSearchParams();
            formData.append('action', 'get_user_favorites');
            formData.append('nonce', cakeFavsData.nonce);

            fetch(cakeFavsData.ajaxUrl, {
                method: 'POST',
                body: formData,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data) {
                    const serverFavs = response.data.split(',').filter(Boolean);
                    const mergedFavs = [...new Set([...localFavs, ...serverFavs])];
                    localStorage.setItem(storageKey, JSON.stringify(mergedFavs));

                    updateUI(mergedFavs);
                    renderFavoritesPage(mergedFavs);

                    if (mergedFavs.length > serverFavs.length) syncToServer(mergedFavs);
                } else {
                    updateUI(localFavs);
                    renderFavoritesPage(localFavs);
                    if (localFavs.length > 0) syncToServer(localFavs);
                }
            })
            .catch(() => {
                updateUI(localFavs);
                renderFavoritesPage(localFavs);
            });
        } else {
            updateUI(localFavs);
            renderFavoritesPage(localFavs);
        }
    };

    // 7. INJECT CUSTOM HEART BUTTONS DYNAMICALLY
    const injectHeartButtons = () => {
        document.querySelectorAll('.product-inner').forEach(card => {
            // Skip if we already added a button to this card
            if (card.querySelector('.my-custom-fav-btn')) return;

            // Find the hidden YITH element to grab the Product ID
            const yithEl = card.querySelector('.yith-wcwl-add-to-wishlist');
            if (!yithEl) return;

            // Create our custom button element
            const heartBtn = document.createElement('button');
            heartBtn.className = 'my-custom-fav-btn';
            heartBtn.innerHTML = '🤍';
            heartBtn.setAttribute('aria-label', 'Add to favorites');

            // Inject it directly into the image container
            const imgContainer = card.querySelector('.item-img-info');
            if (imgContainer) {
                imgContainer.appendChild(heartBtn);
            }
        });

        // Immediately update the UI so previously saved cakes show the red heart
        let currentFavs = JSON.parse(localStorage.getItem(storageKey)) || [];
        if (typeof updateUI === 'function') {
            updateUI(currentFavs);
        }
    };

    // Run immediately on page load
    injectHeartButtons();

    // Set up a MutationObserver to re-inject buttons if AJAX filters change the grid
    const gridContainer = document.querySelector('.products');
    if (gridContainer) {
        const observer = new MutationObserver((mutations, obs) => {
            obs.disconnect(); // Disconnect to prevent infinite loops

            if (typeof injectHeartButtons === 'function') {
                injectHeartButtons();
            }

            obs.observe(gridContainer, { childList: true, subtree: true }); // Reconnect
        });

        observer.observe(gridContainer, { childList: true, subtree: true });
    }

    initFavorites();
});
