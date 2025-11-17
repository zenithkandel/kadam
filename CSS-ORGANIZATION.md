# KADAM Styles - CSS Organization

## 📁 File Structure

### **style.css** (Universal Styles)
All shared styles across login, signup, and password reset pages:
- ✅ CSS Variables (colors, shadows, transitions)
- ✅ Dark mode theme
- ✅ Reset & base styles
- ✅ Animations (fadeInUp, slideIn, pulse, spin, shake, scaleIn, checkmark)
- ✅ Theme toggle button
- ✅ Back button
- ✅ Logo & headers
- ✅ Containers
- ✅ Form elements (inputs, labels, wrappers)
- ✅ Password strength indicator
- ✅ Buttons (primary, secondary, loading states)
- ✅ Error messages & validation
- ✅ Links
- ✅ Checkboxes
- ✅ Accessibility features
- ✅ Responsive design (mobile breakpoints)

### **signup-styles.css** (Signup-Specific)
- ✅ Progress bar with percentage indicator
- ✅ Username generation button
- ✅ Success icon positioning for validation
- ✅ Mobile responsive adjustments for signup

### **password-reset-styles.css** (Password Reset-Specific)
- ✅ Step progress indicator (4-step flow)
- ✅ Step circles and labels
- ✅ Form step transitions
- ✅ OTP input fields (6-digit)
- ✅ Success screen animation
- ✅ Redirect timer
- ✅ Mobile responsive adjustments for reset flow

## 🔗 HTML Linking

### login.html
```html
<link rel="stylesheet" href="style.css">
```

### signup.html
```html
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="signup-styles.css">
```

### password-reset.html
```html
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="password-reset-styles.css">
```

## ✅ Benefits

1. **No Code Duplication** - Shared styles in one place
2. **Easy Maintenance** - Update universal styles once
3. **Smaller File Sizes** - HTML files are cleaner
4. **Better Performance** - CSS files cached by browser
5. **Consistent Theme** - All pages use same variables
6. **Organized Structure** - Clear separation of concerns

## 🎨 Color Variables

```css
--primary-color: #d4a574
--primary-dark: #c49565
--success-color: #a8b89f
--error-color: #d89a9a
--bg-white: #fdfcfb
--bg-light: #f5f3f0
```

## 🌙 Dark Mode

All three pages support dark mode with `data-theme="dark"` attribute.
Theme preference saved in localStorage and synced across pages.

---
**Last Updated:** November 17, 2025
**Pages:** login.html, signup.html, password-reset.html
**Total CSS Files:** 3 (1 universal + 2 page-specific)
