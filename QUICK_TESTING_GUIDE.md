# Quick Testing Guide - Responsive Admin UI

## Quick Start

### 1. Start the Application
```bash
cd c:\laragon\www\AbsensiMahasiswa
# Access: http://localhost:8000 (or configured URL)
# Login as: admin (test credentials)
```

### 2. Open Browser DevTools (F12 / Right-click > Inspect)

### 3. Enable Responsive Design Mode
- Chrome: Ctrl+Shift+M or Device Toolbar icon
- Firefox: Ctrl+Shift+M or Responsive Design Mode
- Edge: Ctrl+Shift+M

---

## Testing URLs by Role

### Admin Testing
- Login URL: `http://localhost:8000/login`
- Admin Home: `http://localhost:8000/dashboard`
- Mahasiswa: `http://localhost:8000/mahasiswa`
- Dosen: `http://localhost:8000/dosen`
- Jurusan: `http://localhost:8000/jurusan`
- Mata Kuliah: `http://localhost:8000/mata-kuliah`
- Laporan: `http://localhost:8000/laporan`
- Profile: `http://localhost:8000/profile`

### Non-Admin Testing
- Dosen Dashboard: `http://localhost:8000/dashboard-dosen`
- Mahasiswa Dashboard: `http://localhost:8000/dashboard-mahasiswa`

---

## Screen Sizes to Test

### Desktop (md+)
```
Set DevTools width to: 1200px or higher
Expected: Sidebar VISIBLE on left, hamburger button HIDDEN
```

### Tablet (sm-md)
```
Set DevTools width to: 800px (between 640px - 1024px)
Expected: Sidebar HIDDEN, hamburger button VISIBLE
```

### Mobile (<sm)
```
Set DevTools width to: 375px or lower
Expected: Hamburger menu prominent, content stacked
```

---

## Step-by-Step Testing Process

### STEP 1: Desktop Test (1200px+)
1. Set responsive mode to 1200px width
2. **Verify Sidebar:**
   - [ ] Sidebar visible on left (dark color)
   - [ ] Width approximately 256px
   - [ ] "Menu Admin" header visible
3. **Verify Sidebar Links (6 items):**
   - [ ] Mahasiswa (highlighted if on that page)
   - [ ] Dosen
   - [ ] Jurusan
   - [ ] Mata Kuliah
   - [ ] Laporan
   - [ ] Profil Saya
4. **Verify Hamburger Button:**
   - [ ] SHOULD BE HIDDEN (not visible)
5. **Verify Navbar:**
   - [ ] Top navigation shows user info on right
   - [ ] Logo on left
   - [ ] Sticky (stays at top when scrolling)
6. **Click a sidebar link:**
   - [ ] Navigation works
   - [ ] Page loads
   - [ ] Link highlights as active
7. **Scroll page:**
   - [ ] Navbar stays at top
   - [ ] Sidebar scrolls with content (if content long)

### STEP 2: Tablet Test (800px)
1. Set responsive mode to 800px width
2. **Verify Sidebar:**
   - [ ] Sidebar HIDDEN (should NOT be visible)
3. **Verify Hamburger Button:**
   - [ ] Button VISIBLE on top-left
   - [ ] Shows 3 horizontal lines (menu icon)
4. **Click Hamburger Button:**
   - [ ] Mobile menu slides in
   - [ ] User card visible with name/role
   - [ ] All 6 admin links visible
   - [ ] Logout button visible
5. **Menu Behavior:**
   - [ ] Click a link - page loads and menu closes (optional)
   - [ ] Click hamburger again - menu closes
   - [ ] Menu icon changes to X when open
6. **Content Area:**
   - [ ] Content uses full width
   - [ ] No sidebar on screen
   - [ ] Content readable

### STEP 3: Mobile Test (375px)
1. Set responsive mode to 375px width
2. **Verify Hamburger:**
   - [ ] Button prominent and large
   - [ ] Easy to tap (>44px)
3. **Verify Navbar:**
   - [ ] User profile info HIDDEN from navbar
   - [ ] Logo visible
   - [ ] Brand name visible
4. **Open Mobile Menu:**
   - [ ] Click hamburger
   - [ ] Menu slides in
   - [ ] User avatar visible with initial letter
5. **Menu Content:**
   - [ ] All links visible and tap-able
   - [ ] Logout button red and prominent
6. **Page Content:**
   - [ ] Scrolls vertically
   - [ ] No horizontal scroll
   - [ ] Tables scroll horizontally if needed
   - [ ] All text readable
7. **Navigation:**
   - [ ] Click "Mahasiswa" - page loads
   - [ ] Sidebar never appears on desktop
   - [ ] All pages accessible

### STEP 4: Role-Based Testing

#### Admin User
1. Login as admin
2. Desktop (1200px):
   - [ ] Sidebar visible with all 6 admin links
3. Mobile (375px):
   - [ ] Hamburger menu shows 6 admin links

#### Dosen User
1. Logout and login as dosen
2. Desktop (1200px):
   - [ ] Sidebar should be HIDDEN (no sidebar for dosen)
3. Mobile (375px):
   - [ ] Hamburger menu shows dosen-specific links (Dashboard, Mata Kuliah, Laporan, Profil)
   - [ ] No admin links like "Mahasiswa"

#### Mahasiswa User
1. Logout and login as mahasiswa
2. Desktop (1200px):
   - [ ] Sidebar should be HIDDEN
3. Mobile (375px):
   - [ ] Hamburger menu shows mahasiswa links (Beranda, Mata Kuliah, Profil)

### STEP 5: Responsive Transition Test
1. Start at 1400px (desktop)
   - [ ] Sidebar visible
   - [ ] Note layout

2. Gradually drag to resize down to 1024px
   - [ ] Watch sidebar transition to hidden at md breakpoint
   - [ ] Watch hamburger appear
   - [ ] Content smoothly expands

3. Continue to 800px (tablet)
   - [ ] No layout jumping
   - [ ] All content accessible

4. Resize to 375px (mobile)
   - [ ] Smooth transition
   - [ ] Mobile menu works

5. Resize back to 1400px (desktop)
   - [ ] Sidebar reappears smoothly
   - [ ] Hamburger disappears
   - [ ] Layout returns to normal

### STEP 6: Content Page Testing

#### Test Each Admin Page:
1. **Mahasiswa Page:**
   - Desktop: Data visible, sidebar shows highlight
   - Mobile: Table scrollable, all actions accessible
   
2. **Dosen Page:**
   - Desktop: Sidebar shows highlight
   - Mobile: Content readable
   
3. **Jurusan Page:**
   - Desktop: Sidebar shows highlight
   - Mobile: Content readable
   
4. **Mata Kuliah Page:**
   - Desktop: Sidebar shows highlight
   - Mobile: Tables display correctly
   
5. **Laporan Page:**
   - Desktop: Sidebar shows highlight
   - Mobile: Charts/content responsive
   
6. **Profile Page:**
   - Desktop: Form beside sidebar
   - Mobile: Form stacked, full width
   - All fields editable

### STEP 7: DevTools Inspector Checks

**At Desktop (1200px):**
1. Right-click on sidebar, Inspect
2. Find the `<aside>` element
3. In DevTools, check computed styles:
   - [ ] `display: flex` (showing)
   - [ ] `width: 256px` (w-64)
   - [ ] `background-color: rgb(15, 23, 42)` (bg-slate-900)

4. Right-click on hamburger button, Inspect
5. Check computed styles:
   - [ ] `display: none` (md:hidden means hidden at this size)

**At Mobile (375px):**
1. Right-click on sidebar, Inspect
2. Check computed styles:
   - [ ] `display: none` (hidden)

3. Right-click on hamburger, Inspect
4. Check computed styles:
   - [ ] `display: block` (visible)

---

## What Each Breakpoint Should Look Like

### Desktop (1024px+)
```
+------+ ┌──────────────────────────────┐
|      | │ NAVBAR                       │
+------+ ├──────────────────────────────┤
│      │ │                              │
│SIDE  │ │ MAIN CONTENT                │
│BAR   │ │                              │
│      │ │ - Tables                     │
│ • MH │ │ - Forms                      │
│ • DO │ │ - Charts                     │
│ • JU │ │                              │
│ • MK │ ├──────────────────────────────┤
│ • LA │ │ FOOTER                       │
│ • PR │ │                              │
│      │ │                              │
└──────┘ └──────────────────────────────┘
```

### Tablet (640-1024px)
```
┌──────────────────────────────┐
│ NAVBAR ☰                     │
├──────────────────────────────┤
│                              │
│ MAIN CONTENT                │
│ (NO SIDEBAR)                │
│                              │
│ - Full width tables          │
│ - Full width forms           │
│                              │
├──────────────────────────────┤
│ FOOTER                       │
│                              │
└──────────────────────────────┘

[MENU drops from navbar when ☰ clicked]
```

### Mobile (<640px)
```
┌──────────────────────────────┐
│ ☰ Logo                    👤│
├──────────────────────────────┤
│                              │
│ MAIN CONTENT                │
│ (Single column, scrolls V)  │
│                              │
│ - Responsive tables          │
│ - Stacked forms              │
│                              │
├──────────────────────────────┤
│ FOOTER                       │
│                              │
└──────────────────────────────┘
```

---

## Common Issues & Fixes

### Issue: Sidebar visible on tablet/mobile
**Expected:** Sidebar hidden at 800px, 375px
**Cause:** Tailwind not loading or CSS override
**Fix:** Clear browser cache (Ctrl+Shift+Delete), hard refresh (Ctrl+Shift+R)

### Issue: Hamburger not working
**Expected:** Click hamburger to open/close menu
**Cause:** JavaScript error
**Fix:** Check DevTools Console (F12 > Console) for errors, check jQuery loaded

### Issue: Sidebar shows for dosen/mahasiswa on desktop
**Expected:** Sidebar only for admin at md+
**Cause:** JavaScript not checking role
**Fix:** Check localStorage user role: `JSON.parse(localStorage.getItem('user')).role`

### Issue: Content overlaps sidebar
**Expected:** Sidebar + content both visible without overlap
**Cause:** Wrong flex layout
**Fix:** Verify main container has `flex` and main content has `flex-1`

### Issue: Hamburger button always visible
**Expected:** Hidden on desktop (md:hidden)
**Cause:** CSS not applied
**Fix:** Resize to 1200px and refresh, check DevTools for md:hidden

---

## Browser Console Debug Commands

Run these in DevTools Console (F12 > Console tab):

### Check User Role
```javascript
JSON.parse(localStorage.getItem('user')).role
```
Should output: `"admin"` or `"dosen"` or `"mahasiswa"`

### Check Window Width
```javascript
window.innerWidth
```
Should help identify which breakpoint you're at

### Check Sidebar Display
```javascript
$('#admin-sidebar').css('display')
```
Should be: `"flex"` on desktop (1024px+) for admin

### Check Hamburger Display
```javascript
$('#mobile-menu-btn').css('display')
```
Should be: `"none"` on desktop, `"block"` on mobile

### Manually Toggle Menu
```javascript
$('#mobile-menu').toggleClass('hidden')
```
This will toggle the mobile menu visibility

### Check if Sidebar Hidden Class
```javascript
$('#admin-sidebar').hasClass('hidden')
```
Should be `true` on tablet/mobile, `false` on desktop (admin only)

---

## Final Checklist Before Sign-Off

### Desktop (1024px+)
- [ ] Sidebar visible
- [ ] 6 admin links in sidebar
- [ ] Main content beside sidebar
- [ ] Hamburger button NOT visible
- [ ] All navigation works

### Tablet (640-1024px)
- [ ] Sidebar NOT visible
- [ ] Hamburger button visible
- [ ] Mobile menu contains all links
- [ ] Content full width
- [ ] Menu toggle works

### Mobile (<640px)
- [ ] Hamburger visible and prominent
- [ ] Mobile menu works
- [ ] Content readable
- [ ] No horizontal scroll
- [ ] Touch-friendly spacing

### Role-Based
- [ ] Admin sees sidebar on desktop
- [ ] Dosen/Mahasiswa don't see sidebar
- [ ] Mobile menu shows correct links per role

### Overall
- [ ] No console errors
- [ ] Smooth transitions
- [ ] All links work
- [ ] Good performance
- [ ] Ready for production

---

## Report Template

**Testing Date:** _____________  
**Browser:** _____________  
**Tested URLs:** _____________  

**Results:**
- Desktop (1024px+): ☐ Pass ☐ Fail
- Tablet (640-1024px): ☐ Pass ☐ Fail
- Mobile (<640px): ☐ Pass ☐ Fail
- Admin Role: ☐ Pass ☐ Fail
- Non-Admin Roles: ☐ Pass ☐ Fail

**Issues Found:**
```
[List any issues here]
```

**Overall Status:** ☐ Ready for Production ☐ Needs Fixes

**Tester:** _____________
