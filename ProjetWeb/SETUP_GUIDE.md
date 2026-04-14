📋 MediLink - Setup Guide
==================================================

## 🎯 What's Been Done

I've converted your application from localStorage (browser storage) to a full **database-driven system**. Now when users add, modify, or delete appointments, they're automatically saved to the database!

---

## ⚡ STEP 1: Create Tables in phpMyAdmin

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select your database: **projetweb**
3. Go to the "SQL" tab
4. Copy the entire content from **setup.sql** file
5. Paste it and click "GO"

✅ This creates:
   - **medecins** table (doctors)
   - **rendezvous** table (appointments)
   - Sample doctors (optional)

---

## ✅ STEP 2: Verify Files Created

The following files have been created/modified:

### New Files:
- `api.php` ← REST API endpoint for database operations
- `setup.sql` ← SQL script for creating tables

### Modified Files:
- `Views/front/addRDV.js` ← Now sends data to database
- `Views/front/modifRDV.js` ← Now updates database
- `Views/front/suppRDV.js` ← Now deletes from database
- `Views/front/listRDV.js` ← Now loads from database

---

## 🔄 How It Works Now

### User adds appointment:
Form → JavaScript validation → API call → Database update → List refreshes

### User modifies appointment:
Click "Modify" → New values checked → API call → Database update → List refreshes

### User deletes appointment:
Click "Delete" → Confirmation → API call → Database delete → List refreshes

### List loads automatically:
Page loads → API fetches all appointments → Display with doctor info

---

## 🧪 Testing Everything

1. **Add appointment**: 
   - Select doctor, pick date (Mon-Sat only, future date)
   - Pick time (8:00-12:30 or 14:00-18:00)
   - Click "Add"
   - Check phpMyAdmin → rendezvous table → data appears!

2. **Modify appointment**:
   - Click "Modify" on an appointment
   - Change date/time
   - Check database → values updated!

3. **Delete appointment**:
   - Click "Delete"
   - Confirm deletion
   - Check database → row removed!

---

## 📊 Database Structure

### medecins table:
- id (Primary Key)
- nom (Doctor name)
- specialite (Specialty)
- email
- telephone
- created_at

### rendezvous table:
- id (Primary Key)
- medecin_id (Foreign Key → medecins.id)
- date_rdv (Appointment date)
- heure_rdv (Appointment time)
- statut (Status: confirmé, annulé, etc.)
- created_at

---

## 🔧 API Endpoints

All in `api.php`:

- **GET** `api.php?action=list` → Get all appointments
- **GET** `api.php?action=get&id=1` → Get one appointment
- **POST** `api.php?action=add` → Add appointment (JSON body)
- **POST** `api.php?action=update` → Update appointment (JSON body)
- **POST** `api.php?action=delete` → Delete appointment (JSON body)
- **GET** `api.php?action=medecins` → Get all doctors

---

## 💡 Important Notes

✅ **All form submissions now sync with database**
✅ **Validation happens on frontend AND backend**
✅ **No more localStorage - everything is persistent**
✅ **Duplicate appointments prevented at database level (UNIQUE constraint)**

---

## ⚠️ If Something Doesn't Work

1. Check browser console (F12) for JavaScript errors
2. Check phpMyAdmin → see if tables exist
3. Verify database name is "projetweb" in config.php
4. Make sure MySQL is running (XAMPP)
5. Check API response in Network tab (F12 → Network)

---

Done! Your appointment system is now fully database-connected! 🎉
