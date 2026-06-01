# Responsive Design Testing Checklist - Admin UI

**Test Date:** ___________  
**Tester Name:** ___________  
**Browser:** Chrome/Firefox/Edge  
**Tailwind Breakpoints:**
- Mobile: < 640px (sm)
- Tablet: 640px - 1024px (md)
- Desktop: 1024px+ (lg/xl)

---

## PRE-TEST SETUP

### 1. Start the Application
- [ ] Open terminal and navigate to: `c:\laragon\www\AbsensiMahasiswa`
- [ ] Ensure Laragon is running (check system tray)
- [ ] Access application: `http://localhost:8000` (or your configured URL)
- [ ] Login with **admin** credentials to test sidebar visibility

### 2. Setup Browser DevTools
- [ ] Open DevTools (F12 or Right-click > Inspect)
- [ ] Go to **Device Toolbar** (Ctrl+Shift+M or Mobile icon)
- [ ] Use responsive mode to test different screen sizes

---

## TEST 1: DESKTOP VIEW (md+ / 1024px+)

### Sidebar Visibility & Layout
- [ ] **Sidebar appears on left side** at desktop width (1024px+)
- [ ] Sidebar width is ~264px (w-64 class)
- [ ] Sidebar has dark background (bg-slate-900)
- [ ] Sidebar has "Menu Admin" header in indigo-400 color
- [ ] Sidebar has visible border-right (border-slate-800)

### Sidebar Navigation Links
- [ ] **All 6 admin links visible:**
  - [ ] Mahasiswa (with icon)
  - [ ] Dosen (with icon)
  - [ ] Jurusan (with icon)
  - [ ] Mata Kuliah (with icon)
  - [ ] Laporan (with icon)
  - [ ] Profil Saya (with icon)

### Sidebar Link Styling
- [ ] Inactive links show slate-300 text with hover:bg-slate-800
- [ ] Active link (current page) shows bg-indigo-600 with white text
- [ ] Icons are visible and properly sized (w-4 h-4)
- [ ] Hover effects work smoothly on links

### Main Content Area
- [ ] **Content area expands to fill available space** (flex-1)
- [ ] Main content starts at ~264px from left (after sidebar)
- [ ] Content is readable with proper spacing
- [ ] No overlapping between sidebar and content

### Top Navigation Bar
- [ ] Navbar remains visible and sticky (sticky top-0)
- [ ] Mobile hamburger menu button is **HIDDEN** on desktop
  - Check: hamburger button should have `md:hidden` class
- [ ] Desktop nav links visible if applicable
- [ ] User profile info visible on right side
- [ ] Logout button visible
- [ ] PresensiApp logo visible on left

### Desktop Layout Integrity
- [ ] Overall layout: [navbar] + [sidebar | main content]
- [ ] Page scrolls correctly when content overflows
- [ ] No horizontal scroll at 1200px+ width
- [ ] Footer visible at bottom of main content

---

## TEST 2: TABLET VIEW (sm-md / 640px - 1024px)

### Sidebar Visibility
- [ ] **Sidebar is HIDDEN** at tablet width (sm breakpoint)
- [ ] Sidebar should not be visible anywhere on screen
- [ ] Verify: `md:flex` means hidden below md breakpoint

### Navigation
- [ ] **Hamburger menu button is VISIBLE**
  - Location: top-left of navbar, next to logo
  - Button shows 3 horizontal lines (menu icon)
- [ ] Nav links visible in top navbar (if space allows)
- [ ] User profile visible in navbar

### Content Area
- [ ] Content uses full available width (no sidebar)
- [ ] Content area spans from navbar to footer
- [ ] Pages (Mahasiswa, Dosen, etc.) should still be accessible via hamburger menu
- [ ] Tables and content are readable without horizontal scroll at 900px+

### Hamburger Menu Functionality
- [ ] Click hamburger button to open mobile menu
- [ ] Mobile menu slides in from top
- [ ] All navigation links present in mobile menu
- [ ] User info card visible in mobile menu
- [ ] Logout button visible in mobile menu
- [ ] Click hamburger again to close menu
- [ ] Menu icon changes to X icon when menu is open

### Layout Test
- [ ] Overall layout: [navbar with hamburger] + [full-width content]
- [ ] No sidebar visible
- [ ] Footer visible

---

## TEST 3: MOBILE VIEW (<sm / < 640px)

### Navigation
- [ ] **Hamburger menu button VISIBLE** on far left of navbar
- [ ] Menu button is prominent and easily clickable
- [ ] Hamburger icon (3 lines) visible initially
- [ ] Brand/logo visible next to hamburger button
- [ ] User profile info **HIDDEN** on navbar (sm:hidden class)
- [ ] Logout button **HIDDEN** on navbar

### Mobile Menu
- [ ] Click hamburger button opens menu
- [ ] Menu slides in from top with animation
- [ ] User avatar card visible in menu (initials displayed)
- [ ] User name and role visible in menu
- [ ] All 6 admin links visible in menu (if logged in as admin):
  - [ ] Mahasiswa
  - [ ] Dosen
  - [ ] Jurusan
  - [ ] Mata Kuliah
  - [ ] Laporan
  - [ ] Profil Saya
- [ ] "Logout Akun" button prominent and red at bottom
- [ ] X icon visible (close icon)
- [ ] Click X or menu item closes the menu
- [ ] Menu closes when navigating to a page

### Content Layout
- [ ] Content uses full screen width
- [ ] No sidebar present
- [ ] Text is readable and not cramped
- [ ] Images/tables scale appropriately

### Specific Content Areas
- [ ] **Mahasiswa page:** Table is scrollable horizontally if needed
- [ ] **Dosen page:** Content visible and accessible
- [ ] **Jurusan page:** Content visible
- [ ] **Mata Kuliah page:** Content visible
- [ ] **Laporan page:** Content visible
- [ ] **Profile page:** Content visible and editable
- [ ] No content cut off at edges

### Mobile User Interactions
- [ ] All buttons are large enough to tap (min 44x44px)
- [ ] Forms are usable on mobile
- [ ] Dropdown selects work properly
- [ ] Links are clickable without accidental taps

---

## TEST 4: ROLE-BASED ACCESS CONTROL

### Admin Role Verification
- [ ] Login as admin user
- [ ] Check browser DevTools > Application > LocalStorage
- [ ] Verify: `user.role === "admin"`
- [ ] **At desktop view (md+):** Sidebar is visible ✓
- [ ] **At mobile/tablet:** Hamburger menu shows admin links ✓

### Non-Admin Role (Dosen/Mahasiswa)
- [ ] Logout and login as **dosen** user
- [ ] Verify: `user.role === "dosen"`
- [ ] **At desktop view:** Sidebar should be **HIDDEN**
  - Note: Sidebar should only show for admin role
- [ ] At mobile: Hamburger menu shows dosen-specific links only
  - [ ] Dashboard
  - [ ] Mata Kuliah
  - [ ] Laporan
  - [ ] Profil Saya
- [ ] No admin-only links visible

- [ ] Logout and login as **mahasiswa** user
- [ ] Verify: `user.role === "mahasiswa"`
- [ ] **At desktop view:** Sidebar should be **HIDDEN**
- [ ] At mobile: Hamburger menu shows mahasiswa-specific links:
  - [ ] Beranda
  - [ ] Mata Kuliah
  - [ ] Profil Saya

---

## TEST 5: RESPONSIVE BREAKPOINT TRANSITIONS

### Testing Transitions (Desktop → Tablet → Mobile)

**Using Chrome DevTools Responsive Mode:**

1. **Start at 1400px width (Desktop)**
   - [ ] Sidebar visible
   - [ ] Hamburger button hidden
   - [ ] Content aligned with sidebar

2. **Resize to 1024px (md breakpoint exact)**
   - [ ] Sidebar transitions to hidden smoothly
   - [ ] Hamburger button appears
   - [ ] Content expands to full width
   - [ ] No layout jumping or glitches

3. **Resize to 900px (tablet)**
   - [ ] Sidebar remains hidden
   - [ ] Content readable
   - [ ] All elements properly sized

4. **Resize to 640px (sm breakpoint exact)**
   - [ ] All elements adjust correctly
   - [ ] Hamburger menu functional
   - [ ] User info hidden from navbar

5. **Resize to 375px (mobile)**
   - [ ] Content stack vertically
   - [ ] No horizontal scroll
   - [ ] Touch-friendly spacing

6. **Resize back to 1400px (Desktop)**
   - [ ] Sidebar reappears smoothly
   - [ ] No content shift or overlap
   - [ ] Everything returns to normal

---

## TEST 6: SPECIFIC PAGES & CONTENT

### Mahasiswa Page (Admin)
- [ ] Desktop (md+):
  - [ ] Sidebar visible with "Mahasiswa" highlighted
  - [ ] Data table visible and readable
  - [ ] Add button prominent
  - [ ] Filters/search working
- [ ] Mobile:
  - [ ] Table scrollable horizontally
  - [ ] Pagination visible
  - [ ] Action buttons (Edit/Delete) accessible

### Dosen Page (Admin)
- [ ] Similar structure as Mahasiswa page
- [ ] Responsive table layout
- [ ] All CRUD operations accessible

### Jurusan, Mata Kuliah, Laporan Pages
- [ ] All pages responsive
- [ ] Content visible at all breakpoints
- [ ] Navigation links work

### Profile Page
- [ ] Desktop: Form beside sidebar
- [ ] Tablet/Mobile: Full-width form
- [ ] All input fields functional

---

## TEST 7: VISUAL & UX CHECKS

### Color & Styling Consistency
- [ ] Navbar: Indigo-600 background throughout
- [ ] Sidebar: Slate-900 background (admin only, desktop)
- [ ] Active links: Proper highlighting
- [ ] Hover states: Smooth transitions
- [ ] Text contrast: Readable (WCAG compliant)

### Spacing & Alignment
- [ ] Content padding consistent (px-4 sm:px-6 lg:px-8)
- [ ] Sidebar links properly spaced (px-4 py-3)
- [ ] No text overlapping
- [ ] Proper margins between sections

### Animation & Transitions
- [ ] Menu slide-in animation smooth (animate-in slide-in-from-top)
- [ ] Hover effects not jarring
- [ ] Loading states visible if applicable

### Footer
- [ ] Footer visible at all breakpoints
- [ ] Footer text readable
- [ ] Copyright year correct ({{ date('Y') }})

---

## TEST 8: BROWSER COMPATIBILITY

Test in each browser (if possible):
- [ ] **Chrome** (DevTools for responsive testing)
- [ ] **Firefox** (Responsive Design Mode)
- [ ] **Edge** (Device Emulation)

### Consistency Check
- [ ] Layout identical across browsers
- [ ] Colors render consistently
- [ ] SVG icons render correctly
- [ ] Fonts load properly

---

## TEST 9: ACCESSIBILITY CHECKS

- [ ] Hamburger button has aria-label or is clearly labeled
- [ ] Navigation links have proper contrast
- [ ] Buttons are keyboard accessible (Tab key)
- [ ] No keyboard traps
- [ ] Focus states visible
- [ ] Screen reader can identify nav structure

---

## ISSUES FOUND

| Issue # | Description | Screen Size | Severity | Fix Required |
|---------|-------------|-------------|----------|--------------|
| 1       |             |             |          |              |
| 2       |             |             |          |              |
| 3       |             |             |          |              |

---

## NOTES & OBSERVATIONS

```
[Space for detailed notes about layout behavior, edge cases, etc.]




```

---

## TEST RESULTS SUMMARY

**Overall Status:** 
- [ ] ✅ ALL TESTS PASS - Responsive design working correctly
- [ ] ⚠️ SOME ISSUES - See issues table above
- [ ] ❌ BLOCKING ISSUES - Responsive design needs fixes

**Sidebar (Desktop/md+):**
- [ ] ✅ Visible and functional
- [ ] ⚠️ Partially working
- [ ] ❌ Not working

**Mobile Menu:**
- [ ] ✅ Functional and accessible
- [ ] ⚠️ Partially working
- [ ] ❌ Not working

**Content Responsiveness:**
- [ ] ✅ Fully responsive
- [ ] ⚠️ Minor issues at some breakpoints
- [ ] ❌ Not responsive

**Admin-Only Access Control:**
- [ ] ✅ Sidebar shows only for admin
- [ ] ⚠️ Inconsistent visibility
- [ ] ❌ Shows for all roles

---

## SIGN-OFF

**Tester:** _______________________  
**Date:** _______________________  
**All Tests Passed:** ☐ Yes ☐ No  
**Ready for Production:** ☐ Yes ☐ No
