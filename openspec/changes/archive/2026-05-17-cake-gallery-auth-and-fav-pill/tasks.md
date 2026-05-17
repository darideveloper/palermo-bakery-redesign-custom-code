## 1. Auth Buttons — PHP (functions.php)

- [x] 1.1 Add a `woocommerce_before_shop_loop` action hook in `functions.php` that outputs the `.gallery-auth-buttons` HTML block, guarded by `_palermo_is_gallery_view()` and `!is_user_logged_in()`
- [x] 1.2 The HTML block must contain a "Login" `<a>` linking to `/login` and a "Sign Up" `<a>` linking to `/register`, both with class `gallery-auth-btn`

## 2. Auth Buttons — CSS (category-filter-menu.css)

- [x] 2.1 Add `.gallery-auth-buttons` wrapper styles: `text-align: center`, `margin-bottom: 24px`, `display: flex`, `justify-content: center`, `gap: 12px`
- [x] 2.2 Add `.gallery-auth-btn` base styles matching pill aesthetic: `padding: 10px 24px`, `border-radius: 50px`, `border: 1px solid rgba(0,0,0,0.1)`, `font-size: 14px`, `color: #333333`, `text-decoration: none`, `transition: all 0.3s ease`
- [x] 2.3 Add `.gallery-auth-btn:hover` state: `background-color: #f5f5f5`, `border-color: #d1d1d1`, `transform: scale(1.05)`
- [x] 2.4 Add `.gallery-auth-btn.signup` filled variant: `background-color: #333333`, `color: #ffffff`, `border-color: #333333` (Sign Up button is filled, Login is outlined)
- [x] 2.5 Add `.gallery-auth-btn.signup:hover` state: `background-color: #555555`, `border-color: #555555`

## 3. DOM Injection and Favorite Cakes Pill — JS (category-loader.js)

- [x] 3.1 The theme forces `woocommerce_before_shop_loop` content into a hidden `.toolbar-top` container. In the `jQuery(document).ready` block, add JS to move `.gallery-auth-buttons` using `.insertAfter('#woocommerce_product_categories-3')` so they are visible below the filter row.
- [x] 3.2 Prepend a new `<li>` as the first child of `#woocommerce_product_categories-3 ul.product-categories` containing `<a href="/favorite-cakes" class="fav-pill-link">♥ Favorite Cakes</a>`
- [x] 3.3 Update the existing category pill click listener to use event delegation (`$('#woocommerce_product_categories-3 ul.product-categories').on('click', 'li a', function (e) { ... })`) so the dynamically injected `.fav-pill-link` automatically inherits the loading spinner logic without duplicating the handler

## 4. Favorite Cakes Pill — CSS (category-filter-menu.css)

- [x] 4.1 The `.fav-pill-link` class requires no overrides (inherits all base pill styles from the existing `ul.product-categories li a` rule) — verify by visual inspection that it renders correctly
- [x] 4.2 If visual inspection shows any gap, add targeted `.fav-pill-link` overrides (e.g., letter-spacing or icon spacing) — only add what is needed
