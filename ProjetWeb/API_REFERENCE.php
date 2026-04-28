<?php
/**
 * API_REFERENCE.php — Documentation for MediLink API
 * 
 * Base URL: http://localhost/ProjetWeb/api.php
 * 
 * All responses are JSON format:
 * {
 *   "success": true/false,
 *   "message": "Human readable message",
 *   "data": {} or [] (only on successful GET requests),
 *   "id": number (only when creating new record)
 * }
 */

// ============================================
// 1️⃣ LIST ALL APPOINTMENTS (GET)
// ============================================
/*
URL: api.php?action=list
Method: GET
Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "medecin_id": 1,
      "date_rdv": "2026-04-20",
      "heure_rdv": "10:00",
      "statut": "confirmé",
      "medecin_nom": "Dr. Dupont Jean",
      "specialite": "Cardiologue",
      "created_at": "2026-04-14T10:30:00"
    }
  ]
}

JavaScript Example:
fetch('api.php?action=list')
  .then(r => r.json())
  .then(data => console.log(data));
*/

// ============================================
// 2️⃣ GET SINGLE APPOINTMENT (GET)
// ============================================
/*
URL: api.php?action=get&id=1
Method: GET
Parameters: id = appointment ID
Response:
{
  "success": true,
  "data": {
    "id": 1,
    "medecin_id": 1,
    "date_rdv": "2026-04-20",
    "heure_rdv": "10:00:00",
    "statut": "confirmé"
  }
}

JavaScript Example:
const id = 1;
fetch(`api.php?action=get&id=${id}`)
  .then(r => r.json())
  .then(data => console.log(data));
*/

// ============================================
// 3️⃣ ADD APPOINTMENT (POST)
// ============================================
/*
URL: api.php?action=add
Method: POST
Headers: Content-Type: application/json
Body:
{
  "medecin_id": 1,
  "date_rdv": "2026-04-20",
  "heure_rdv": "10:00",
  "statut": "confirmé"
}

Response (Success):
{
  "success": true,
  "message": "Rendez-vous ajouté",
  "id": 42
}

JavaScript Example:
fetch('api.php?action=add', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    medecin_id: 1,
    date_rdv: '2026-04-20',
    heure_rdv: '10:00',
    statut: 'confirmé'
  })
})
.then(r => r.json())
.then(data => console.log(data));
*/

// ============================================
// 4️⃣ UPDATE APPOINTMENT (POST)
// ============================================
/*
URL: api.php?action=update
Method: POST
Headers: Content-Type: application/json
Body:
{
  "id": 1,
  "medecin_id": 2,
  "date_rdv": "2026-04-21",
  "heure_rdv": "14:00",
  "statut": "confirmé"
}

Response (Success):
{
  "success": true,
  "message": "Rendez-vous modifié"
}

JavaScript Example:
fetch('api.php?action=update', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    id: 1,
    medecin_id: 2,
    date_rdv: '2026-04-21',
    heure_rdv: '14:00',
    statut: 'confirmé'
  })
})
.then(r => r.json())
.then(data => console.log(data));
*/

// ============================================
// 5️⃣ DELETE APPOINTMENT (POST)
// ============================================
/*
URL: api.php?action=delete
Method: POST
Headers: Content-Type: application/json
Body:
{
  "id": 1
}

Response (Success):
{
  "success": true,
  "message": "Rendez-vous supprimé"
}

JavaScript Example:
fetch('api.php?action=delete', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ id: 1 })
})
.then(r => r.json())
.then(data => console.log(data));
*/

// ============================================
// 6️⃣ GET ALL DOCTORS (GET)
// ============================================
/*
URL: api.php?action=medecins
Method: GET
Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Dr. Dupont Jean",
      "specialite": "Cardiologue"
    },
    {
      "id": 2,
      "nom": "Dr. Martin Claire",
      "specialite": "Dermatologue"
    }
  ]
}

JavaScript Example:
fetch('api.php?action=medecins')
  .then(r => r.json())
  .then(data => console.log(data));
*/

// ============================================
// ⚠️ ERROR RESPONSES
// ============================================
/*
Missing Required Fields (400):
{
  "success": false,
  "message": "Missing required fields"
}

Not Found (404):
{
  "success": false,
  "message": "Rendez-vous not found"
}

Server Error (500):
{
  "success": false,
  "message": "Server error: [error details]"
}

Invalid Action (400):
{
  "success": false,
  "message": "Invalid action"
}
*/

// ============================================
// 📝 DATA FORMATS
// ============================================
/*
Date Format: YYYY-MM-DD (e.g., "2026-04-20")
Time Format: HH:MM or HH:MM:SS (e.g., "10:00" or "10:00:00")
Status Values: "confirmé", "annulé", "en attente"
*/

?>
