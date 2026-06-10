## 1. PHP — Add auth buttons to the favorites shortcode

- [x] 1.1 In `src/core/functions.php`, modify `render_favorite_cakes_page()` to conditionally render `<div class="gallery-auth-buttons">` with Login and Sign Up links between the `#my-favs-title` heading and `#fav-loading-msg` paragraph, gated by `!is_user_logged_in()`


