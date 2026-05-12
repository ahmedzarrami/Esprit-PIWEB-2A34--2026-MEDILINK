/**
 * face-auth.js — Gestion de la reconnaissance faciale avec face-api.js
 *
 * Fournit deux contextes :
 *  1. Connexion (modal login)     → openFaceLoginModal / captureFaceLogin
 *  2. Enrôlement (page profil)   → startFaceEnrollment / captureFaceEnrollment
 *
 * Les modèles sont chargés depuis le CDN GitHub Pages de face-api.js.
 * Une connexion internet est nécessaire au premier chargement.
 */

'use strict';

// URL racine des modèles face-api.js (GitHub Pages CDN)
const FACE_MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';

// Streams webcam actifs (référencés pour pouvoir les arrêter proprement)
let loginStream   = null;
let enrollStream  = null;

// Indicateur de chargement des modèles (singleton : ne charge qu'une fois)
let modelsLoaded  = false;
let modelsLoading = false;

// ──────────────────────────────────────────────────────────────────────────────
// Chargement des modèles IA (partagé entre les deux contextes)
// ──────────────────────────────────────────────────────────────────────────────

async function loadFaceModels() {
  if (modelsLoaded) return true;
  if (modelsLoading) {
    // Attendre que le chargement en cours se termine
    while (modelsLoading) await sleep(200);
    return modelsLoaded;
  }

  modelsLoading = true;
  try {
    // Les trois modèles nécessaires pour la détection + reconnaissance
    await Promise.all([
      faceapi.nets.ssdMobilenetv1.loadFromUri(FACE_MODEL_URL),
      faceapi.nets.faceLandmark68Net.loadFromUri(FACE_MODEL_URL),
      faceapi.nets.faceRecognitionNet.loadFromUri(FACE_MODEL_URL),
    ]);
    modelsLoaded  = true;
    modelsLoading = false;
    return true;
  } catch (e) {
    modelsLoading = false;
    console.error('Erreur chargement modèles face-api :', e);
    return false;
  }
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// ──────────────────────────────────────────────────────────────────────────────
// Helpers communs
// ──────────────────────────────────────────────────────────────────────────────

function setFaceStatus(elId, type, text) {
  const el = document.getElementById(elId);
  if (!el) return;
  el.className   = 'face-status face-status-' + type;
  el.querySelector('span').textContent = text;
  el.classList.remove('hidden');
}

async function startCamera(videoEl) {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
    });
    videoEl.srcObject = stream;
    await new Promise(resolve => { videoEl.onloadedmetadata = resolve; });
    return stream;
  } catch (e) {
    throw new Error('Impossible d\'accéder à la caméra : ' + (e.message || e));
  }
}

function stopStream(stream) {
  if (stream) {
    stream.getTracks().forEach(t => t.stop());
  }
}

/**
 * Détecte un visage unique dans la vidéo et retourne le descripteur (Float32Array→Array).
 * Retourne null si aucun visage (ou plus d'un) n'est détecté.
 */
async function detectSingleFace(videoEl) {
  const detection = await faceapi
    .detectSingleFace(videoEl, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
    .withFaceLandmarks()
    .withFaceDescriptor();

  return detection ? Array.from(detection.descriptor) : null;
}

/**
 * Dessine le contour du visage détecté sur le canvas en temps réel.
 */
async function startFaceTracking(videoEl, canvasEl, statusTextId) {
  const displaySize = { width: videoEl.videoWidth, height: videoEl.videoHeight };
  faceapi.matchDimensions(canvasEl, displaySize);

  const intervalId = setInterval(async () => {
    if (!videoEl.srcObject) { clearInterval(intervalId); return; }

    const detections = await faceapi
      .detectAllFaces(videoEl, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
      .withFaceLandmarks();

    const resized = faceapi.resizeResults(detections, displaySize);
    const ctx = canvasEl.getContext('2d');
    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
    faceapi.draw.drawFaceLandmarks(canvasEl, resized);

    if (detections.length === 1) {
      const stEl = document.getElementById(statusTextId);
      if (stEl) stEl.textContent = 'Visage détecté — cliquez sur le bouton pour continuer.';
    } else if (detections.length === 0) {
      const stEl = document.getElementById(statusTextId);
      if (stEl) stEl.textContent = 'Positionnez votre visage face à la caméra.';
    } else {
      const stEl = document.getElementById(statusTextId);
      if (stEl) stEl.textContent = 'Un seul visage doit être visible.';
    }
  }, 200);

  return intervalId;
}

// ──────────────────────────────────────────────────────────────────────────────
// 1. CONNEXION PAR VISAGE (modal sur la page login)
// ──────────────────────────────────────────────────────────────────────────────

let loginTrackInterval = null;

async function openFaceLoginModal() {
  document.getElementById('faceLoginModal').classList.remove('hidden');
  const statusEl = document.getElementById('faceLoginStatusText');
  const captureBtn = document.getElementById('faceLoginCaptureBtn');
  const video = document.getElementById('faceLoginVideo');

  setFaceStatus('faceLoginStatus', 'info', 'Chargement des modèles IA…');
  captureBtn.disabled = true;

  try {
    const ok = await loadFaceModels();
    if (!ok) throw new Error('Modèles IA non disponibles. Vérifiez votre connexion internet.');

    setFaceStatus('faceLoginStatus', 'info', 'Démarrage de la caméra…');
    loginStream = await startCamera(video);

    setFaceStatus('faceLoginStatus', 'info', 'Positionnez votre visage face à la caméra.');
    captureBtn.disabled = false;

    // Suivi temps réel
    loginTrackInterval = await startFaceTracking(
      video,
      document.getElementById('faceLoginCanvas'),
      'faceLoginStatusText'
    );

  } catch (err) {
    setFaceStatus('faceLoginStatus', 'error', err.message);
    captureBtn.disabled = true;
  }
}

function closeFaceModal() {
  clearInterval(loginTrackInterval);
  stopStream(loginStream);
  loginStream = null;
  const video = document.getElementById('faceLoginVideo');
  if (video) video.srcObject = null;
  document.getElementById('faceLoginModal').classList.add('hidden');
}

async function captureFaceLogin() {
  const captureBtn = document.getElementById('faceLoginCaptureBtn');
  captureBtn.disabled = true;

  setFaceStatus('faceLoginStatus', 'info', 'Analyse du visage en cours…');

  const video = document.getElementById('faceLoginVideo');
  const descriptor = await detectSingleFace(video);

  if (!descriptor) {
    setFaceStatus('faceLoginStatus', 'warning', 'Aucun visage détecté. Réessayez.');
    captureBtn.disabled = false;
    return;
  }

  setFaceStatus('faceLoginStatus', 'info', 'Identification en cours…');

  try {
    const resp = await fetch('index.php?action=face_login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ descriptor })
    });
    const data = await resp.json();

    if (data.success) {
      setFaceStatus('faceLoginStatus', 'success', 'Bienvenue ' + data.nom + ' !');
      clearInterval(loginTrackInterval);
      stopStream(loginStream);
      loginStream = null;

      // Redirection selon le rôle
      setTimeout(() => {
        if (data.role === 'Administrateur') {
          window.location.href = 'index.php?module=rdv&action=admin';
        } else if (data.role === 'Professionnel') {
          window.location.href = 'index.php?page=professionnel';
        } else {
          window.location.href = 'index.php?page=profile';
        }
      }, 800);
    } else {
      setFaceStatus('faceLoginStatus', 'error', data.error || 'Visage non reconnu.');
      captureBtn.disabled = false;
    }
  } catch (e) {
    setFaceStatus('faceLoginStatus', 'error', 'Erreur réseau. Réessayez.');
    captureBtn.disabled = false;
  }
}

// ──────────────────────────────────────────────────────────────────────────────
// 2. ENRÔLEMENT DU VISAGE (onglet Biométrie de la page profil)
// ──────────────────────────────────────────────────────────────────────────────

let enrollTrackInterval = null;

async function startFaceEnrollment() {
  const startBtn   = document.getElementById('faceEnrollStartBtn');
  const captureBtn = document.getElementById('faceEnrollCaptureBtn');
  const cancelBtn  = document.getElementById('faceEnrollCancelBtn');
  const cameraWrap = document.getElementById('faceEnrollCameraWrap');
  const video      = document.getElementById('faceEnrollVideo');
  const statusEl   = document.getElementById('faceEnrollStatus');

  startBtn.classList.add('hidden');
  statusEl.classList.remove('hidden');
  setFaceStatus('faceEnrollStatus', 'info', 'Chargement des modèles IA…');

  try {
    const ok = await loadFaceModels();
    if (!ok) throw new Error('Modèles IA non disponibles. Vérifiez votre connexion internet.');

    setFaceStatus('faceEnrollStatus', 'info', 'Démarrage de la caméra…');
    enrollStream = await startCamera(video);

    cameraWrap.classList.remove('hidden');
    captureBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
    captureBtn.disabled = false;
    setFaceStatus('faceEnrollStatus', 'info', 'Positionnez votre visage face à la caméra.');

    enrollTrackInterval = await startFaceTracking(
      video,
      document.getElementById('faceEnrollCanvas'),
      'faceEnrollStatusText'
    );

  } catch (err) {
    setFaceStatus('faceEnrollStatus', 'error', err.message);
    startBtn.classList.remove('hidden');
  }
}

function cancelFaceEnrollment() {
  clearInterval(enrollTrackInterval);
  stopStream(enrollStream);
  enrollStream = null;
  const video = document.getElementById('faceEnrollVideo');
  if (video) video.srcObject = null;

  document.getElementById('faceEnrollCameraWrap').classList.add('hidden');
  document.getElementById('faceEnrollCaptureBtn').classList.add('hidden');
  document.getElementById('faceEnrollCancelBtn').classList.add('hidden');
  document.getElementById('faceEnrollStartBtn').classList.remove('hidden');
  document.getElementById('faceEnrollStatus').classList.add('hidden');
}

async function captureFaceEnrollment() {
  const captureBtn = document.getElementById('faceEnrollCaptureBtn');
  captureBtn.disabled = true;

  setFaceStatus('faceEnrollStatus', 'info', 'Capture du descripteur facial…');

  const video = document.getElementById('faceEnrollVideo');
  const descriptor = await detectSingleFace(video);

  if (!descriptor) {
    setFaceStatus('faceEnrollStatus', 'warning', 'Aucun visage détecté clairement. Réessayez.');
    captureBtn.disabled = false;
    return;
  }

  setFaceStatus('faceEnrollStatus', 'info', 'Enregistrement en cours…');

  try {
    const resp = await fetch('index.php?action=save_face', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ descriptor })
    });
    const data = await resp.json();

    if (data.success) {
      clearInterval(enrollTrackInterval);
      stopStream(enrollStream);
      enrollStream = null;
      setFaceStatus('faceEnrollStatus', 'success', data.message || 'Visage enregistré avec succès !');
      // Recharger la page pour mettre à jour le statut
      setTimeout(() => window.location.reload(), 1200);
    } else {
      setFaceStatus('faceEnrollStatus', 'error', data.error || 'Erreur lors de l\'enregistrement.');
      captureBtn.disabled = false;
    }
  } catch (e) {
    setFaceStatus('faceEnrollStatus', 'error', 'Erreur réseau. Réessayez.');
    captureBtn.disabled = false;
  }
}

async function deleteFaceDescriptor() {
  if (!confirm('Supprimer votre visage ? Vous ne pourrez plus vous connecter par reconnaissance faciale.')) return;

  try {
    const resp = await fetch('index.php?action=delete_face', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: '{}'
    });
    const data = await resp.json();
    if (data.success) {
      toast('Reconnaissance faciale désactivée.', 'success');
      setTimeout(() => window.location.reload(), 800);
    } else {
      toast('Erreur lors de la suppression.', 'error');
    }
  } catch (e) {
    toast('Erreur réseau.', 'error');
  }
}
