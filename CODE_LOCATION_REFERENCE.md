# Responsive Design Code Location Reference

**Quick lookup guide to find and verify responsive design code**

---

## 📍 Main File Location

**File:** `resources/views/layouts/app.blade.php`

This is the ONLY file that was modified to implement responsive design. All responsive behavior is in this single file.

---

## 🗂️ Code Sections in Order

### 1. NAVBAR (Lines 32-101)
**What:** Top navigation bar (always visible, sticky)

**Key Lines:**
- Line 32: `<nav class="bg-indigo-600 text-white shadow-lg sticky top-0 z-40">`
  - `sticky top-0` - Stays at top when scrolling
  - `z-40` - Above other content

- Lines 37-40: Mobile hamburger button
  ```html
  <button id="mobile-menu-btn" class="md:hidden">
  ```
  - `md:hidden` - HIDDEN on desktop (md+ breakpoint)
  - Shows on mobile and tablet

- Lines 52-56: Desktop nav links
  ```html
  <div class="hidden md:block">
  ```
  - `hidden md:block` - Hidden by default, shown on desktop

- Lines 61-64: User profile info
  ```html
  <div class="hidden sm:flex">
  ```
  - `hidden sm:flex` - Hidden on mobile, shown on tablet+

- Lines 78-100: Mobile menu dropdown
  ```html
  <div id="mobile-menu" class="hidden md:hidden">
  ```
  - `hidden md:hidden` - Shown on mobile/tablet, hidden on desktop

---

### 2. MAIN LAYOUT CONTAINER (Line 104)
**What:** Flex container for sidebar + main content

```html
<div class="flex flex-1 overflow-hidden">
```

**Responsive Behavior:**
- `flex` - Enables flexbox (sidebar and main content side-by-side)
- `flex-1` - Takes full available height
- `overflow-hidden` - Prevents body scrollbar

---

### 3. ADMIN SIDEBAR (Lines 106-113) ⭐ KEY RESPONSIVE SECTION
**What:** Desktop-only sidebar with admin links

```html
<aside id="admin-sidebar" class="hidden md:flex flex-col w-64 bg-slate-900 text-white shadow-xl border-r border-slate-800">
    <div class="p-6 border-b border-slate-700">
        <h3 class="font-bold text-lg text-indigo-400">Menu Admin</h3>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-2" id="sidebar-links">
        <!-- Injected by JavaScript -->
    </nav>
</aside>
```

**Responsive Classes:**
- `hidden` - Hidden by default
- `md:flex` - Shows as flex on md+ (1024px+)
- `w-64` - Width: 256 pixels
- `flex-col` - Stack items vertically
- `overflow-y-auto` - Scrollable if content long

**This is the KEY line:**
```html
class="hidden md:flex flex-col w-64 bg-slate-900 ..."
        └─ Hidden by default, visible on md+ (1024px+)
```

---

### 4. MAIN CONTENT (Lines 116-129)
**What:** Page content area that adjusts to sidebar

```html
<main class="flex-1 flex flex-col overflow-hidden">
    <div class="flex-grow overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            @yield('content')
        </div>
    </div>
```

**Responsive Behavior:**
- `flex-1` - Takes remaining width after sidebar (or full if sidebar hidden)
- `px-4 sm:px-6 lg:px-8` - Responsive padding:
  - Default: `px-4` (16px) - Mobile
  - sm+: `sm:px-6` (24px) - Tablet
  - lg+: `lg:px-8` (32px) - Desktop

---

### 5. FOOTER (Lines 124-128)
**What:** Page footer

```html
<footer class="bg-white border-t border-slate-200 w-full">
    <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} Sistem Presensi Mahasiswa. All rights reserved.
    </div>
</footer>
```

---

## 🔧 JAVASCRIPT LOGIC

### Authorization Check (Lines 157-163)
```javascript
const user = JSON.parse(userStr);

// Set User Info
$('#nav-user-name').text('Hai, ' + user.nama);
$('#nav-user-role').text(user.role);
$('#nav-user-name-mobile').text(user.nama);
$('#nav-user-role-mobile').text(user.role);
$('#nav-mobile-avatar').text(user.nama.charAt(0).toUpperCase());
```

**What:** Loads user data and displays in navbar

---

### Navigation Links Rendering (Lines 184-211)
```javascript
let adminLinks = [];

if (user.role === 'admin') {
    adminLinks = [
        { name: 'Mahasiswa', url: '/mahasiswa', icon: '...' },
        { name: 'Dosen', url: '/dosen', icon: '...' },
        { name: 'Jurusan', url: '/jurusan', icon: '...' },
        { name: 'Mata Kuliah', url: '/mata-kuliah', icon: '...' },
        { name: 'Laporan', url: '/laporan', icon: '...' },
        { name: 'Profil Saya', url: '/profile', icon: '...' }
    ];
} else if (user.role === 'dosen') {
    links = [
        { name: 'Dashboard', url: '/dashboard-dosen', ... },
        { name: 'Mata Kuliah', url: '/mata-kuliah', ... },
        { name: 'Laporan', url: '/laporan', ... },
        { name: 'Profil Saya', url: '/profile', ... }
    ];
} else if (user.role === 'mahasiswa') {
    links = [
        { name: 'Beranda', url: '/dashboard-mahasiswa', ... },
        { name: 'Mata Kuliah', url: '/mata-kuliah', ... },
        { name: 'Profil Saya', url: '/profile', ... }
    ];
}
```

**What:** Defines links based on user role
**Key:** Different links for different roles

---

### Links Rendering & Styling (Lines 214-218)
```javascript
const desktopClass = "px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2";
const mobileClass = "flex items-center gap-3 w-full p-4 rounded-xl text-sm font-bold transition-all";
const sidebarClass = "flex items-center gap-3 w-full px-4 py-3 rounded-lg text-sm font-semibold transition-all";

const activeDesktop = "bg-white/20 text-white shadow-sm ring-1 ring-white/30";
const normalDesktop = "text-indigo-100 hover:bg-white/10 hover:text-white";

const activeMobile = "bg-white text-indigo-600 shadow-lg";
const normalMobile = "text-indigo-100 hover:bg-white/10";

const activeSidebar = "bg-indigo-600 text-white shadow-md";
const normalSidebar = "text-slate-300 hover:bg-slate-800 hover:text-white";
```

**What:** CSS classes for different link styles
**Key:** Different styling for desktop, mobile, and sidebar links

---

### Sidebar Population (Lines 220-227) ⭐ KEY LOGIC
```javascript
// Render admin sidebar (only for admin, desktop only)
if (user.role === 'admin') {
    adminLinks.forEach(link => {
        const isActive = path.startsWith(link.url);
        sidebarNav.append(`<a href="${link.url}" class="${sidebarClass} ${isActive ? activeSidebar : normalSidebar}">...`);
    });
    $('#admin-sidebar').removeClass('hidden');  // Show sidebar
}
```

**What:** 
1. Checks if user role is 'admin'
2. If yes, renders all 6 admin links in sidebar
3. Removes 'hidden' class to make sidebar visible

**This is where sidebar only shows for admin**

---

### Mobile Menu Toggle (Lines 229-234)
```javascript
$('#mobile-menu-btn').on('click', function() {
    $('#mobile-menu').toggleClass('hidden');
    $('#menu-icon').toggleClass('hidden');
    $('#close-icon').toggleClass('hidden');
});
```

**What:** Makes hamburger button functional
- Toggles menu visibility
- Switches between menu icon and X icon

---

## 🎯 Finding Specific Responsive Code

### To Find: Sidebar Responsive Behavior
**Look at:** Line 106
```html
class="hidden md:flex"
```
- `hidden` - Sidebar hidden by default
- `md:flex` - Appears on md+ (1024px+)

### To Find: Hamburger Button Visibility
**Look at:** Line 37
```html
class="md:hidden"
```
- Visible by default, hidden on md+ (desktop)

### To Find: Admin-Only Logic
**Look at:** Lines 220-227
```javascript
if (user.role === 'admin') {
    // ... sidebar links rendered here
    $('#admin-sidebar').removeClass('hidden');
}
```
- Only shows sidebar if role is 'admin'

### To Find: Mobile Menu
**Look at:** Line 78
```html
id="mobile-menu" class="hidden md:hidden"
```
- Shown by default on mobile/tablet
- Hidden on desktop (md:hidden)

---

## 📊 What Changed vs. What Stayed Same

### ✅ NEW (Added for Responsive Sidebar)
1. **Sidebar component** (Lines 106-113)
   - Desktop-only navigation
   - Admin-only links
   - Dark background styling

2. **JavaScript sidebar logic** (Lines 220-227)
   - Check if user is admin
   - Render sidebar links
   - Show/hide sidebar based on role

### ✅ EXISTING (Modified for Responsive Layout)
1. **Main container** (Line 104)
   - Changed to flex layout
   - Allows sidebar + content

2. **Navbar** (Lines 32-101)
   - Already had responsive classes
   - Mobile menu already existed
   - Now better integrated with sidebar

---

## 🔍 Code Inspection Checklist

Use this to verify responsive code is correct:

### [ ] Line 104: Main Container
```html
<div class="flex flex-1 overflow-hidden">
```
- ✅ Has `flex` class
- ✅ Has `flex-1` class
- ✅ Has `overflow-hidden` class

### [ ] Line 106: Sidebar Opening Tag
```html
<aside id="admin-sidebar" class="hidden md:flex flex-col w-64 bg-slate-900 text-white shadow-xl border-r border-slate-800">
```
- ✅ Has `hidden` class (default hidden)
- ✅ Has `md:flex` class (show on md+)
- ✅ Has `w-64` class (256px width)
- ✅ Has `bg-slate-900` (dark background)
- ✅ Has id="admin-sidebar" (JavaScript reference)

### [ ] Line 37: Hamburger Button
```html
<button id="mobile-menu-btn" class="md:hidden">
```
- ✅ Has `md:hidden` (hidden on desktop)
- ✅ Has id="mobile-menu-btn" (JavaScript reference)

### [ ] Line 78: Mobile Menu
```html
<div id="mobile-menu" class="hidden md:hidden">
```
- ✅ Has `hidden md:hidden` (hidden by default, stays hidden on desktop)
- ✅ Has id="mobile-menu" (JavaScript reference)

### [ ] Line 220-227: Admin Check
```javascript
if (user.role === 'admin') {
```
- ✅ Checks user role
- ✅ Only shows sidebar for admin
- ✅ Line 226: `$('#admin-sidebar').removeClass('hidden');`

---

## 🧪 Quick Test Using DevTools

### Test 1: Check Sidebar CSS at Desktop (1200px)
```javascript
// In DevTools Console:
$('#admin-sidebar').css('display')
// Expected output: "flex" (shown)

$('#mobile-menu-btn').css('display')
// Expected output: "none" (hidden)
```

### Test 2: Check Sidebar CSS at Mobile (375px)
```javascript
$('#admin-sidebar').css('display')
// Expected output: "none" (hidden)

$('#mobile-menu-btn').css('display')
// Expected output: "block" (shown)
```

### Test 3: Check Admin Role
```javascript
JSON.parse(localStorage.getItem('user')).role
// Expected output: "admin"

// Then check if sidebar is shown:
$('#admin-sidebar').hasClass('hidden')
// Expected output: false (not hidden, so visible)
```

---

## 📝 Line Number Reference

| Feature | Lines | Class | Purpose |
|---------|-------|-------|---------|
| Navbar | 32-101 | sticky, z-40 | Top nav, always visible |
| Hamburger Button | 37-40 | md:hidden | Toggle mobile menu |
| Desktop Nav Links | 52-56 | hidden md:block | Desktop navigation |
| User Info Navbar | 61-64 | hidden sm:flex | User details on navbar |
| Mobile Menu | 78-100 | hidden md:hidden | Dropdown menu on mobile |
| Main Container | 104 | flex flex-1 | Layout flex container |
| **Sidebar** | **106-113** | **hidden md:flex** | **Admin desktop nav** |
| Sidebar Header | 107-109 | p-6 border-b | Section title |
| Sidebar Links | 110-112 | flex-1 overflow-y-auto | Navigation links area |
| Main Content | 116-129 | flex-1 flex flex-col | Page content area |
| Content Padding | 118 | px-4 sm:px-6 lg:px-8 | Responsive padding |
| Footer | 124-128 | bg-white border-t | Page footer |

---

## 🎓 Understanding the Responsive Classes

### Hidden/Visible Classes
```
hidden          = display: none (default)
md:flex         = display: flex on md+ (1024px+)
md:hidden       = display: none on md+ (1024px+)
sm:flex         = display: flex on sm+ (640px+)
hidden md:block = hidden by default, block on md+
```

### Layout Classes
```
flex         = display: flex
flex-col     = flex-direction: column (vertical stack)
flex-1       = flex: 1 1 0% (grows to fill space)
flex-grow    = flex-grow: 1
overflow-hidden = hidden overflow
overflow-y-auto = vertical scroll if needed
```

### Size Classes
```
w-64         = width: 16rem (256px)
w-full       = width: 100%
max-w-7xl    = max-width: 80rem
```

### Spacing Classes
```
px-4         = padding-left/right: 1rem (16px)
sm:px-6      = padding-left/right: 1.5rem (24px) on sm+
lg:px-8      = padding-left/right: 2rem (32px) on lg+
```

---

## ✅ Verification Checklist

- [ ] Sidebar code exists at Line 106-113
- [ ] Sidebar has `hidden md:flex` classes
- [ ] Hamburger has `md:hidden` class
- [ ] Main container has `flex` class
- [ ] Admin check exists at Line 220-227
- [ ] Mobile menu has `hidden md:hidden` classes
- [ ] All IDs match JavaScript references
- [ ] CSS classes are spelled correctly

---

## 🎯 Summary

**One file, one key section:**

**File:** `resources/views/layouts/app.blade.php`
**Lines:** 106-113 (sidebar) + 220-227 (JavaScript logic)

**Key classes:**
- `hidden md:flex` - Sidebar responsive behavior
- `md:hidden` - Hamburger responsive behavior

**Everything else is supporting the responsive sidebar!**

---

**Last Updated:** [Timestamp]  
**Status:** Ready for Code Review
