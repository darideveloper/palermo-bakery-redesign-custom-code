// fav-button.js

document.addEventListener("DOMContentLoaded", () => {
  let userFavs = [];
  let isFavsLoaded = false;
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
    const isFav = userFavs.includes(String(currentLightboxProductId));
    btn.innerHTML = isFav ? "❤️" : "🤍";
    btn.classList.toggle("is-favorited", isFav);
  };

  const getLightboxProductId = (imgSrc) => {
    const cleanSrc = imgSrc.split("?")[0];
    const links = document.querySelectorAll('a[data-rel^="prettyPhoto"]');
    for (const link of links) {
      if (link.href.split("?")[0] === cleanSrc) {
        const productBlock = link.closest(".product-inner");
        if (!productBlock) {
          // Favorites page: product ID is on the link itself
          if (link.dataset.productId) return link.dataset.productId;
          continue;
        }
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

      // Enforce Login
      if (typeof cakeFavsData === "undefined" || !cakeFavsData.isLoggedIn) {
        if (typeof cakeFavsData !== "undefined") window.location.href = cakeFavsData.loginUrl;
        return;
      }

      if (currentLightboxProductId) {
        window.testToggleFav(currentLightboxProductId);
        updateLightboxFavBtn();
        // Favorites page: fade out and remove the matching card
        const card = document.getElementById("fav-item-" + currentLightboxProductId);
        if (card) {
          card.style.transition = "opacity 0.3s ease";
          card.style.opacity = "0";
          setTimeout(() => {
            card.remove();
            if (userFavs.length === 0) renderUserFavoritesGrid();
          }, 300);
        }
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
    const list = favArray || userFavs;
    document.querySelectorAll(".my-custom-fav-btn, .save-shared-btn").forEach((btn) => {
      let productId = null;
      
      if (btn.classList.contains("save-shared-btn")) {
        productId = btn.dataset.productId;
      } else {
        const productBlock = btn.closest(".product-inner");
        if (!productBlock) return;
        const yithEl = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
        if (yithEl) productId = yithEl.dataset.fragmentRef;
      }

      if (productId && list.includes(String(productId))) {
        btn.classList.add("is-favorited");
        btn.innerHTML = "❤️";
      } else {
        btn.classList.remove("is-favorited");
        btn.innerHTML = "🤍";
      }
    });
    const headerCounter = document.querySelector(".mini-wishlist .number");
    if (headerCounter) headerCounter.textContent = list.length;
  };

  // 3. TOGGLE FAVORITE LOGIC
  window.testToggleFav = (productId) => {
    // Enforce Login
    if (typeof cakeFavsData === "undefined" || !cakeFavsData.isLoggedIn) {
      if (typeof cakeFavsData !== "undefined") window.location.href = cakeFavsData.loginUrl;
      return;
    }

    // Guard: Don't allow toggling until server sync is complete
    if (!isFavsLoaded) return;

    productId = String(productId);
    if (userFavs.includes(productId)) {
      userFavs = userFavs.filter((id) => id !== productId);
    } else {
      userFavs.push(productId);
    }
    
    userFavs = [...new Set(userFavs)];
    updateUI(userFavs);
    syncToServer(userFavs);
  };

  // 4. THE AJAX RENDERER (Builds the Masonry Grids)
  const renderGrid = (favs, containerId, isShared) => {
    if (typeof cakeFavsData === "undefined") return Promise.resolve();
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
        if (response.success) {
          listContainer.innerHTML = response.data;
          updateUI(userFavs);
          if (window.palermoInitLightbox && typeof jQuery !== "undefined") {
            window.palermoInitLightbox(jQuery(listContainer));
          }
        }
      })
      .catch((err) => console.error(err));
  };

  // Builds the user's specific favorites grid
  const renderUserFavoritesGrid = () => {
    if (typeof cakeFavsData === "undefined") return;
    const loadingMsg = document.getElementById("fav-loading-msg");
    const sharePageBtn = document.getElementById("share-favs-page-btn");
    const listContainer = document.getElementById("favorite-cakes-list");

    if (!cakeFavsData.isLoggedIn) {
        if (loadingMsg) {
            loadingMsg.style.display = "block";
            loadingMsg.innerHTML = 'Please <a href="' + cakeFavsData.loginUrl + '">login</a> to save your favorite cakes.';
        }
        if (sharePageBtn) sharePageBtn.style.display = "none";
        if (listContainer) listContainer.innerHTML = "";
        return;
    }

    if (userFavs.length === 0) {
      if (loadingMsg) {
        loadingMsg.style.display = "block";
        loadingMsg.textContent = "Your favorites list is empty.";
      }
      if (sharePageBtn) sharePageBtn.style.display = "none";
      if (listContainer) listContainer.innerHTML = "";
      return;
    }

    renderGrid(userFavs, "favorite-cakes-list", false).then(() => {
      if (loadingMsg) loadingMsg.style.display = "none";
      if (sharePageBtn) sharePageBtn.style.display = "inline-flex";
    });
  };

  // 5. MASTER CLICK EVENT LISTENER
  document.addEventListener("click", function (e) {
    // A. Heart Buttons — gallery cards AND favorites page cards
    const btn = e.target.closest(".my-custom-fav-btn");
    if (btn) {
      e.preventDefault();

      // Enforce Login
      if (typeof cakeFavsData === "undefined" || !cakeFavsData.isLoggedIn) {
        if (typeof cakeFavsData !== "undefined") window.location.href = cakeFavsData.loginUrl;
        return;
      }

      const productBlock = btn.closest(".product-inner");
      if (productBlock) {
        // Gallery context
        const yithElement = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
        if (yithElement) window.testToggleFav(yithElement.dataset.fragmentRef);
        return;
      }
      // Favorites page context
      const masonryItem = btn.closest(".masonry-item");
      if (masonryItem) {
        const productId = btn.dataset.productId;
        if (productId) {
          window.testToggleFav(productId);
          masonryItem.style.transition = "opacity 0.3s ease";
          masonryItem.style.opacity = "0";
          setTimeout(() => {
            masonryItem.remove();
            if (userFavs.length === 0) renderUserFavoritesGrid();
          }, 300);
        }
      }
      return;
    }

    // B. Save Buttons (On Shared Section)
    const saveSharedBtn = e.target.closest(".save-shared-btn");
    if (saveSharedBtn) {
      e.preventDefault();

      // Enforce Login
      if (typeof cakeFavsData === "undefined" || !cakeFavsData.isLoggedIn) {
        if (typeof cakeFavsData !== "undefined") window.location.href = cakeFavsData.loginUrl;
        return;
      }

      const productId = saveSharedBtn.getAttribute("data-product-id");
      if (productId && typeof window.testToggleFav === "function") {
        window.testToggleFav(productId);
        // Automatically re-render the user's grid below to show/remove the cake
        renderUserFavoritesGrid();
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
      if (productBlock) {
        // Gallery context: resolve via YITH wishlist element
        const yithEl = productBlock.querySelector(".yith-wcwl-add-to-wishlist");
        if (yithEl) currentLightboxProductId = yithEl.dataset.fragmentRef;
      } else {
        // Favorites page context: product ID is on the link
        currentLightboxProductId = link.dataset.productId || null;
      }
      if (currentLightboxProductId) setTimeout(injectLightboxFavBtn, 250);
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

      // Generate the link pointing directly to the favorites page
      const shareUrl =
        window.location.origin +
        "/favorite-cakes/?shared_favs=" +
        userFavs.join(",");

      navigator.clipboard.writeText(shareUrl).then(() => {
        const originalHTML = sharePageBtn.innerHTML;
        sharePageBtn.innerHTML = '<span class="share-btn-icon">✅</span><span class="share-btn-text">Link Copied!</span>';
        setTimeout(() => {
          sharePageBtn.innerHTML = originalHTML;
        }, 2000);
      });
    });
  }

  // 6. INITIALIZE ON LOAD
  const initFavorites = () => {
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
            const rawFavs = response.data.split(",").filter(Boolean);
            userFavs = [...new Set(rawFavs)];
            
            // Legacy Cleanup
            localStorage.removeItem("my_cake_favs");

            isFavsLoaded = true;
            updateUI(userFavs);
            renderUserFavoritesGrid();
          } else {
            isFavsLoaded = true;
            updateUI(userFavs);
            renderUserFavoritesGrid();
          }
        })
        .catch(() => {
          isFavsLoaded = true;
          updateUI(userFavs);
          renderUserFavoritesGrid();
        });
    } else {
      isFavsLoaded = true;
      updateUI(userFavs);
      renderUserFavoritesGrid();
      // Even for guests, clean up any old storage to be safe
      localStorage.removeItem("my_cake_favs");
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

    updateUI(userFavs);
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
