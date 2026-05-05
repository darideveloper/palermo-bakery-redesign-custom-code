# Add Custom Popup Form

## Summary
Add a custom HTML, CSS, and JS popup contact form to integrate with a proprietary email API. The trigger button will be a visually appealing cupcake with an "Ask Me" toothpick flag. The form itself will gather Name, Email, Phone, and Message. It will feature responsive styling, turning into a full-screen modal on mobile devices for ease of use.

## Motivation
The site needs a popup contact form to allow users to easily order cakes or ask questions. Using a custom solution instead of plugins like Contact Form 7 or ElementsKit ensures:
- Precise control over z-index (`9999`).
- Custom visuals for the trigger (a cupcake with a toothpick flag that says "Ask Me").
- High performance without loading heavy plugin libraries.
- Seamless integration with the proprietary API by including specific hidden fields (`api_key`, `user`, `subject`, `redirect`).
- Consistent styling by reusing existing form styles by adding matching selectors.