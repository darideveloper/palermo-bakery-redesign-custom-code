// fav-button.js

document.addEventListener("DOMContentLoaded", () => {
  const storageKey = "my_cake_favs";
  let currentLightboxProductId = null;

  // 1. SYNC TO WORDPRESS
  const syncToServer = (favArray) => {
    if (typeof cakeFavsData === "undefined" || !cakeFavsData.isLoggedIn) return;
    const formData = new URLSearchParams();
    formData.append("action", "save_user_favorites");
    formData.append("nonce", cakeFavsData.nonce);
    formData.append("favs", favArray.join(","));
    fetch(cakeFavsData.ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    }).catch((err) => console.error(err));
  };

  // 2. LIGHTBOX FAV BUTTON HELPERS
  const updateLightboxFavBtn = () => {
    const btn = document.getElementById("lightbox-fav-btn");
    if (!btn) return;
    const favs = JSON.parse(localStorage.getItem(storageKey)) || [];
    const isFav = favs.includes(currentLightboxProductId);
    btn.innerHTML = isFav ? "❤️" : "🤍";
    btn.classList.toggle("is-favorited", isFav);
  };

  const getLightboxProductId = (imgSrc) => {
    const cleanSrc = imgSrc.split("?")[0];
    const links = document.querySelectorAll('a[data-rel^="prettyPhoto"]');
    for (const link of links) {
      if (link.href.split("?")[0] === cleanSrc) {
        const productBlock = link.closest(".product-inner");
        if (!productBlock) continue;
        const yithEl = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
        if (yithEl) return yithEl.dataset.fragmentRef;
      }
    }
    return null;
  };

  const injectLightboxFavBtn = () => {
    const container = document.getElementById("pp_full_res");
    if (!container) return;
    if (document.getElementById("lightbox-btn-container")) {
      updateLightboxFavBtn();
      return;
    }

    const btnWrapper = document.createElement("div");
    btnWrapper.id = "lightbox-btn-container";
    container.appendChild(btnWrapper);

    // Share button — left
    const shareBtn = document.createElement("button");
    shareBtn.id = "lightbox-share-btn";
    shareBtn.className = "my-custom-lightbox-btn";
    shareBtn.setAttribute("aria-label", "Share cake");
    shareBtn.innerHTML = '<i class="fa fa-share-alt"></i>';
    btnWrapper.appendChild(shareBtn);

    const showShareToast = (message) => {
      const existing = document.getElementById("lightbox-share-toast");
      if (existing) existing.remove();
      const toast = document.createElement("div");
      toast.id = "lightbox-share-toast";
      toast.textContent = message;
      btnWrapper.appendChild(toast);
      setTimeout(() => toast.remove(), 2000);
    };

    shareBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (!currentLightboxProductId) return;
      const shareUrl =
        window.location.origin +
        "/favorite-cakes/?shared_favs=" +
        currentLightboxProductId;
      navigator.clipboard
        .writeText(shareUrl)
        .then(() => showShareToast("Link Copied!"))
        .catch(() => showShareToast("Copy failed"));
    });

    // Fav button — right
    const btn = document.createElement("button");
    btn.id = "lightbox-fav-btn";
    btn.className = "my-custom-fav-btn";
    btn.setAttribute("aria-label", "Add to favorites");
    btn.innerHTML = "🤍";
    btnWrapper.appendChild(btn);

    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (currentLightboxProductId) {
        window.testToggleFav(currentLightboxProductId);
        updateLightboxFavBtn();
      }
    });

    const img = container.querySelector("img");
    if (img) {
      const attrObserver = new MutationObserver(() => {
        const newId = getLightboxProductId(img.src);
        if (newId && newId !== currentLightboxProductId) {
          currentLightboxProductId = newId;
          updateLightboxFavBtn();
        }
      });
      attrObserver.observe(img, { attributes: true, attributeFilter: ["src"] });

      const childObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
          for (const node of mutation.addedNodes) {
            if (node.tagName === "IMG") {
              const newId = getLightboxProductId(node.src);
              if (newId && newId !== currentLightboxProductId) {
                currentLightboxProductId = newId;
                updateLightboxFavBtn();
              }
            }
          }
        }
      });
      childObserver.observe(container, { childList: true });
    }

    updateLightboxFavBtn();
  };

  // 3. UPDATE MAIN GALLERY UI (Hearts & Counter)
  const updateUI = (favArray) => {
    document.querySelectorAll(".my-custom-fav-btn").forEach((btn) => {
      const productBlock = btn.closest(".product-inner");
      if (!productBlock) return;
      const yithEl = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
      if (yithEl && favArray.includes(yithEl.dataset.fragmentRef)) {
        btn.classList.add("is-favorited");
        btn.innerHTML = "❤️";
      } else {
        btn.classList.remove("is-favorited");
        btn.innerHTML = "🤍";
      }
    });
    const headerCounter = document.querySelector(".mini-wishlist .number");
    if (headerCounter) headerCounter.textContent = favArray.length;
  };

  // 3. TOGGLE FAVORITE LOGIC
  window.testToggleFav = (productId) => {
    productId = String(productId);
    let favs = JSON.parse(localStorage.getItem(storageKey)) || [];
    if (favs.includes(productId)) {
      favs = favs.filter((id) => id !== productId);
    } else {
      favs.push(productId);
    }
    localStorage.setItem(storageKey, JSON.stringify(favs));
    updateUI(favs);
    syncToServer(favs);
  };

  // 4. THE AJAX RENDERER (Builds the Masonry Grids)
  const renderGrid = (favs, containerId, isShared) => {
    const listContainer = document.getElementById(containerId);
    if (!listContainer) return Promise.resolve();

    if (!favs || favs.length === 0) {
      listContainer.innerHTML = "";
      return Promise.resolve();
    }

    const formData = new URLSearchParams();
    formData.append("action", "render_favorite_products");
    formData.append("nonce", cakeFavsData.nonce);
    formData.append("favs", favs.join(","));
    formData.append("is_shared", isShared ? "true" : "false");

    return fetch(cakeFavsData.ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((res) => res.json())
      .then((response) => {
        if (response.success) listContainer.innerHTML = response.data;
      })
      .catch((err) => console.error(err));
  };

  // Builds the user's specific favorites grid
  const renderUserFavoritesGrid = () => {
    let favs = JSON.parse(localStorage.getItem(storageKey)) || [];
    const loadingMsg = document.getElementById("fav-loading-msg");
    const sharePageBtn = document.getElementById("share-favs-page-btn");

    if (favs.length === 0) {
      if (loadingMsg) {
        loadingMsg.style.display = "block";
        loadingMsg.textContent = "Your favorites list is empty.";
      }
      if (sharePageBtn) sharePageBtn.style.display = "none";
      const listContainer = document.getElementById("favorite-cakes-list");
      if (listContainer) listContainer.innerHTML = "";
      return;
    }

    renderGrid(favs, "favorite-cakes-list", false).then(() => {
      if (loadingMsg) loadingMsg.style.display = "none";
      if (sharePageBtn) sharePageBtn.style.display = "inline-block";
    });
  };

  // 5. MASTER CLICK EVENT LISTENER
  document.addEventListener("click", function (e) {
    // A. Main Gallery Hearts
    const btn = e.target.closest(".my-custom-fav-btn");
    if (btn) {
      e.preventDefault();
      const productBlock = btn.closest(".product-inner");
      if (!productBlock) return;
      const yithElement = productBlock.querySelector(
        ".yith-wcwl-add-to-wishlist",
      );
      if (yithElement) window.testToggleFav(yithElement.dataset.fragmentRef);
      return;
    }

    // B. Remove Buttons (On Favorites Page)
    const removeBtn = e.target.closest(".remove-fav-btn");
    if (removeBtn) {
      e.preventDefault();
      const productId = removeBtn.getAttribute("data-product-id");
      if (productId && typeof window.testToggleFav === "function") {
        window.testToggleFav(productId); // Removes from DB/Storage

        // Visual fade out
        const masonryItem = removeBtn.closest(".masonry-item");
        if (masonryItem) {
          masonryItem.style.transition = "opacity 0.3s ease";
          masonryItem.style.opacity = "0";
          setTimeout(() => masonryItem.remove(), 300);
        }

        // Check for empty state
        let favs = JSON.parse(localStorage.getItem(storageKey)) || [];
        if (favs.length === 0) setTimeout(renderUserFavoritesGrid, 300);
      }
      return;
    }

    // C. Save Buttons (On Shared Section)
    const saveSharedBtn = e.target.closest(".save-shared-btn");
    if (saveSharedBtn) {
      e.preventDefault();
      const productId = saveSharedBtn.getAttribute("data-product-id");
      if (productId && typeof window.testToggleFav === "function") {
        let favs = JSON.parse(localStorage.getItem(storageKey)) || [];

        // Only save if it's not already in their list
        if (!favs.includes(productId)) {
          window.testToggleFav(productId);

          // Visual feedback
          saveSharedBtn.innerHTML = "❤️ Saved";
          saveSharedBtn.style.color = "#d93025";

          // Automatically re-render the user's grid below to show the newly saved cake
          renderUserFavoritesGrid();
        }
      }
    }
  });

  // 9. LIGHTBOX OPEN DETECTION & PRODUCT ID CAPTURE
  document.addEventListener(
    "click",
    (e) => {
      const link = e.target.closest('a[data-rel^="prettyPhoto"]');
      if (!link) return;
      const productBlock = link.closest(".product-inner");
      if (!productBlock) return;
      const yithEl = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
      if (yithEl) {
        currentLightboxProductId = yithEl.dataset.fragmentRef;
        setTimeout(injectLightboxFavBtn, 250);
      }
    },
    true,
  );

  const lightboxBodyObserver = new MutationObserver(() => {
    if (document.querySelector(".pp_pic_holder") && !document.getElementById("lightbox-btn-container")) {
      injectLightboxFavBtn();
    }
  });
  lightboxBodyObserver.observe(document.body, { childList: true, subtree: false });

  // Share Button Event
  const sharePageBtn = document.getElementById("share-favs-page-btn");
  if (sharePageBtn) {
    sharePageBtn.addEventListener("click", (e) => {
      e.preventDefault();
      let favs = JSON.parse(localStorage.getItem(storageKey)) || [];

      // Generate the link pointing directly to the favorites page
      const shareUrl =
        window.location.origin +
        "/favorite-cakes/?shared_favs=" +
        favs.join(",");

      navigator.clipboard.writeText(shareUrl).then(() => {
        const originalText = sharePageBtn.innerHTML;
        sharePageBtn.innerHTML = "✅ Link Copied!";
        setTimeout(() => {
          sharePageBtn.innerHTML = originalText;
        }, 2000);
      });
    });
  }

  // 6. INITIALIZE ON LOAD
  const initFavorites = () => {
    let localFavs = JSON.parse(localStorage.getItem(storageKey)) || [];

    // Check URL for Shared Links
    const urlParams = new URLSearchParams(window.location.search);
    const sharedIdsStr = urlParams.get("shared_favs");
    const sharedIds = sharedIdsStr
      ? sharedIdsStr.split(",").filter(Boolean)
      : [];

    // If a shared link is opened, display the new section
    if (sharedIds.length > 0) {
      const sharedSection = document.getElementById("shared-section");
      if (sharedSection) {
        sharedSection.style.display = "block";
        renderGrid(sharedIds, "shared-cakes-list", true); // isShared = true
      }
    }

    // Sync with server if logged in
    if (typeof cakeFavsData !== "undefined" && cakeFavsData.isLoggedIn) {
      const formData = new URLSearchParams();
      formData.append("action", "get_user_favorites");
      formData.append("nonce", cakeFavsData.nonce);

      fetch(cakeFavsData.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      })
        .then((res) => res.json())
        .then((response) => {
          if (response.success && response.data) {
            const serverFavs = response.data.split(",").filter(Boolean);
            const mergedFavs = [...new Set([...localFavs, ...serverFavs])];
            localStorage.setItem(storageKey, JSON.stringify(mergedFavs));

            updateUI(mergedFavs);
            renderUserFavoritesGrid();
            if (mergedFavs.length > serverFavs.length) syncToServer(mergedFavs);
          } else {
            updateUI(localFavs);
            renderUserFavoritesGrid();
            if (localFavs.length > 0) syncToServer(localFavs);
          }
        })
        .catch(() => {
          updateUI(localFavs);
          renderUserFavoritesGrid();
        });
    } else {
      updateUI(localFavs);
      renderUserFavoritesGrid();
    }
  };

  // 7. INJECT CUSTOM HEART BUTTONS DYNAMICALLY
  const injectHeartButtons = () => {
    document.querySelectorAll(".product-inner").forEach((card) => {
      if (card.querySelector(".my-custom-fav-btn")) return;
      const yithEl = card.querySelector(".yith-wcwl-add-to-wishlist");
      if (!yithEl) return;
      const heartBtn = document.createElement("button");
      heartBtn.className = "my-custom-fav-btn";
      heartBtn.innerHTML = "🤍";
      heartBtn.setAttribute("aria-label", "Add to favorites");
      const imgContainer = card.querySelector(".item-img-info");
      if (imgContainer) imgContainer.appendChild(heartBtn);
    });

    let currentFavs = JSON.parse(localStorage.getItem(storageKey)) || [];
    if (typeof updateUI === "function") updateUI(currentFavs);
  };

  injectHeartButtons();

  const gridContainer = document.querySelector(".products");
  if (gridContainer) {
    const observer = new MutationObserver((mutations, obs) => {
      obs.disconnect();
      if (typeof injectHeartButtons === "function") injectHeartButtons();
      obs.observe(gridContainer, { childList: true, subtree: true });
    });
    observer.observe(gridContainer, { childList: true, subtree: true });
  }

  initFavorites();
});
