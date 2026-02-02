# FCPC Clinic System - Customization Guide

## 📝 How to Add/Edit Departments, Courses, Tracks & Strands

This guide shows you exactly where to edit when you need to add more departments, courses, or tracks.

---

## 🎓 STUDENT COURSES/TRACKS/STRANDS

### Location: `assets/app.js` (Lines 45-200 approximately)

Look for this comment block:
```javascript
// ==========================================
// PHILIPPINE EDUCATION SYSTEM DATA
// ==========================================
```

### Elementary & Junior High
These use **General Education** only. No changes usually needed.

### Senior High School

Find this section:
```javascript
'Senior High': {
    grades: ['Grade 11', 'Grade 12'],
    courses: [
```

**To add a new track/strand:**

1. Add after the last course in the list
2. Use this format: `'TRACK-STRAND (Full Name)',`
3. Don't forget the comma!

**Example - Adding a new TVL track:**
```javascript
'TVL-AFA - Organic Agriculture',
'TVL-NEW - Your New Track',  // ← Add here with comma
```

**Senior High Tracks Already Included:**
- ✅ STEM
- ✅ ABM
- ✅ HUMSS
- ✅ GAS
- ✅ All TVL tracks (ICT, HE, IA, AFA)
- ✅ Arts and Design
- ✅ Sports

### College Courses

Find this section:
```javascript
College: {
    grades: ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
    courses: [
```

**To add a new college course:**

1. Find the category (Business, Engineering, Health, etc.)
2. Add your course alphabetically within that category
3. Use official CHED abbreviations

**Example - Adding a new business course:**
```javascript
// ========== BUSINESS AND MANAGEMENT ==========
'BS Accountancy (BSA)',
'BS Business Administration (BSBA)',
'BS Your New Course (BSYNC)',  // ← Add here
'BS Entrepreneurship (BSE)',
```

**College Categories Included:**
- Business and Management (9 courses)
- Computer Studies (3 courses)
- Education (13 courses)
- Engineering (7 courses)
- Health Sciences (13 courses)
- Hospitality & Tourism (3 courses)
- Liberal Arts (11 courses)
- Sciences (11 courses)
- Plus Criminology, Social Work, Multimedia, and Others

**Total: 80+ college courses already included!**

---

## 🏢 EMPLOYEE DEPARTMENTS

### Location: `index.php` (Lines 360-430 approximately)

Look for this comment block:
```html
<!-- 
==========================================
EMPLOYEE DEPARTMENTS - EDIT HERE TO ADD MORE
==========================================
-->
```

Then find the dropdown:
```html
<select id="department" name="department">
```

**To add a new department:**

1. Choose the appropriate optgroup (Academic, Administrative, Support, Special)
2. Add your department alphabetically
3. Use this format: `<option value="Department Name">Department Name</option>`

**Example - Adding a new academic department:**
```html
<!-- Academic Departments -->
<optgroup label="Academic Departments">
    <option value="Arts and Sciences">Arts and Sciences</option>
    <option value="Business Administration">Business Administration</option>
    <option value="Your New Department">Your New Department</option>
    <!-- ↑ Add here, keep alphabetical -->
</optgroup>
```

**Department Categories:**

1. **Academic Departments** (14 departments)
   - Subject/degree-specific departments
   - Example: Computer Science, Engineering, Nursing

2. **Administrative Departments** (12 departments)
   - Office-based departments
   - Example: Registrar, HR, Admissions

3. **Support Services** (5 departments)
   - Maintenance and technical support
   - Example: IT, Facilities, Security

4. **Special Offices** (5 departments)
   - Extra-curricular and special programs
   - Example: Athletics, NSTP, Research

**Total: 36 departments already included!**

---

## 🎯 Quick Reference

### What to Edit Where

| What to Add | File to Edit | Search For |
|-------------|--------------|------------|
| **Student Course/Track** | `assets/app.js` | `PHILIPPINE EDUCATION SYSTEM DATA` |
| **Employee Department** | `index.php` | `EMPLOYEE DEPARTMENTS - EDIT HERE` |
| **Grade Level** | `assets/app.js` | Find education level, edit `grades:` array |

---

## 📋 Step-by-Step Examples

### Example 1: Adding a New Senior High TVL Track

**Scenario:** You want to add "TVL - Animation"

**Step 1:** Open `assets/app.js`

**Step 2:** Find the Senior High section (around line 70)

**Step 3:** Scroll to TVL tracks area

**Step 4:** Add your track:
```javascript
'TVL-IA - Welding',
'TVL-Animation',  // ← NEW TRACK ADDED HERE
```

**Step 5:** Save file

**Step 6:** Refresh browser - Done! ✅

---

### Example 2: Adding a New College Program

**Scenario:** You want to add "BS Aviation"

**Step 1:** Open `assets/app.js`

**Step 2:** Find College section (around line 120)

**Step 3:** Decide category (let's say "OTHERS")

**Step 4:** Add alphabetically:
```javascript
// ========== OTHERS ==========
'BS Aviation (BSA)',  // ← NEW COURSE ADDED HERE
'BS Customs Administration (BSCA)',
```

**Step 5:** Save file

**Step 6:** Refresh browser - Done! ✅

---

### Example 3: Adding Employee Department

**Scenario:** You want to add "Marketing Department"

**Step 1:** Open `index.php`

**Step 2:** Search for "EMPLOYEE DEPARTMENTS - EDIT HERE"

**Step 3:** Find appropriate optgroup (let's say Academic)

**Step 4:** Add alphabetically:
```html
<optgroup label="Academic Departments">
    <option value="Information Technology">Information Technology</option>
    <option value="Marketing Department">Marketing Department</option>
    <!-- ↑ NEW DEPARTMENT ADDED HERE -->
    <option value="Mathematics Department">Mathematics Department</option>
</optgroup>
```

**Step 5:** Save file

**Step 6:** Refresh browser - Done! ✅

---

## ⚠️ Important Rules

### DO:
✅ Keep items in alphabetical order
✅ Use proper capitalization
✅ Include official abbreviations in parentheses for college courses
✅ Add commas at the end of each line (for JavaScript)
✅ Test after adding by trying to create a student/employee

### DON'T:
❌ Remove the comma after each item
❌ Change the structure of the arrays/options
❌ Use special characters that break code (like quotes inside quotes)
❌ Forget to save the file after editing
❌ Edit the database directly (let the code handle it)

---

## 🧪 Testing Your Changes

After adding new courses/departments:

1. **Refresh Browser** (Ctrl + F5 to clear cache)

2. **Test Student Form:**
   - Go to Patients → Add Patient
   - Select "Student"
   - Choose education level
   - Check if your new course appears in dropdown
   - Try saving a test student

3. **Test Employee Form:**
   - Go to Patients → Add Patient
   - Select "Employee"
   - Check if your new department appears
   - Try saving a test employee

4. **Test Filters:**
   - Go to Patients tab
   - Filter by type
   - Check if new course/department appears in filter dropdown
   - Test filtering

---

## 🔍 Finding the Right Lines

### For `assets/app.js`:

**Search for these text strings:**
- "PHILIPPINE EDUCATION SYSTEM DATA"
- "Senior High"
- "College:"
- "courses: ["

### For `index.php`:

**Search for these text strings:**
- "EMPLOYEE DEPARTMENTS - EDIT HERE"
- "Academic Departments"
- "optgroup label"
- 'id="department"'

---

## 💡 Pro Tips

### Organizing Large Lists

**For College Courses:**
- Group by field of study
- Use comment headers for each category
- Keep related programs together

**For Departments:**
- Use optgroups to categorize
- Academic vs Administrative vs Support
- Makes selection easier for users

### Naming Conventions

**Students:**
- Full track name: "TVL-ICT (Information and Communications Technology)"
- Include abbreviations in parentheses
- Use official DepEd/CHED names

**Employees:**
- Clear, descriptive names: "Information Technology Department"
- Not too short: "IT" → "ICT/IT Department"
- Professional naming

### Backup Before Editing

Before making changes:
1. Copy the file to your desktop
2. Or comment out old code
3. Test new changes
4. Keep backup until verified working

---

## 📞 Common Issues

### "My new course doesn't appear!"

**Solution:**
1. Check you edited `assets/app.js` NOT `index.php`
2. Verify you added a comma after the new line
3. Clear browser cache (Ctrl + Shift + Delete)
4. Check browser console (F12) for JavaScript errors

### "Department dropdown is blank!"

**Solution:**
1. Check you edited `index.php` NOT `app.js`
2. Verify proper `<option>` tag format
3. Make sure you're inside an `<optgroup>`
4. Check for typos in HTML tags

### "Getting JavaScript errors"

**Solution:**
1. Check all commas are in place
2. Verify quotes match (`'` or `"` properly paired)
3. Look for missing brackets `]` or braces `}`
4. Use browser console to see exact error line

---

## 📚 Complete File Locations

```
clinic-system/
├── assets/
│   └── app.js              ← Edit for STUDENT courses/tracks
│
└── index.php               ← Edit for EMPLOYEE departments
```

---

## ✅ Verification Checklist

After making changes:

- [ ] File saved successfully
- [ ] Browser cache cleared
- [ ] System loads without errors
- [ ] New course/department appears in Add form
- [ ] New course/department appears in filter
- [ ] Can save test record with new option
- [ ] Filter works with new option
- [ ] No JavaScript errors in console

---

**Quick Tip:** Always keep this guide handy when customizing! 📖

**Remember:** You're editing configuration, not database. The system will automatically save your new courses/departments to the database when students/employees are added. 🚀
