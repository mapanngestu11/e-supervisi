const timeoutMinutes = {{ config('app.session_timeout_minutes', 15) }};
let inactivityTimer;
let warningTimer;
const warningTime = 60; // Show warning 1 minute before lock

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    clearTimeout(warningTimer);
    
    // Show warning before lock
    warningTimer = setTimeout(() => {
        showWarning();
    }, (timeoutMinutes * 60 - warningTime) * 1000);
    
    // Lock session
    inactivityTimer = setTimeout(() => {
        lockSession();
    }, timeoutMinutes * 60 * 1000);
}

function showWarning() {
    if (confirm(`Sesi akan terkunci dalam ${warningTime} detik karena tidak aktif.\n\nKlik OK untuk melanjutkan sesi.`)) {
        resetInactivityTimer();
    }
}

function lockSession() {
    // Redirect to PIN verification page
    window.location.href = '/verify-pin';
}

// Activity events to reset timer
const activityEvents = [
    'mousedown',
    'mousemove',
    'keypress',
    'scroll',
    'touchstart',
    'click'
];

activityEvents.forEach(event => {
    document.addEventListener(event, resetInactivityTimer);
});

// Initial timer start
resetInactivityTimer();
