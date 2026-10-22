const timeoutMinutes = {{ config('app.session_timeout_minutes', 15) }};
let inactivityTimer;
let warningTimer;
let countdownTimer;
let remainingSeconds = timeoutMinutes * 60;
const warningTime = 60;

function resetInactivityTimer() {
clearTimeout(inactivityTimer);
clearTimeout(warningTimer);
clearInterval(countdownTimer);

remainingSeconds = timeoutMinutes * 60;
updateTimerDisplay();
startCountdown();

warningTimer = setTimeout(() => {
showWarning();
}, (timeoutMinutes * 60 - warningTime) * 1000);

inactivityTimer = setTimeout(() => {
lockSession();
}, timeoutMinutes * 60 * 1000);
}

function startCountdown() {
const timerElement = document.getElementById('session-timer');
const timerDisplay = document.getElementById('timer-display');

if (timerElement && timerDisplay) {
timerElement.classList.remove('hidden');
timerElement.classList.add('flex');

countdownTimer = setInterval(() => {
remainingSeconds--;
updateTimerDisplay();

if (remainingSeconds <= 0) { clearInterval(countdownTimer); } }, 1000); } } function updateTimerDisplay() { const
    timerDisplay=document.getElementById('timer-display'); if (timerDisplay) { const minutes=Math.floor(remainingSeconds
    / 60); const seconds=remainingSeconds % 60;
    timerDisplay.textContent=`${minutes}:${seconds.toString().padStart(2, '0' )}`; const
    timerElement=document.getElementById('session-timer'); if (remainingSeconds <=120) {
    timerElement.classList.remove('bg-[#fbbf24]', 'bg-[#f97316]' );
    timerElement.classList.add('bg-[#ef4444]', 'text-white' ); } else if (remainingSeconds <=300) {
    timerElement.classList.remove('bg-[#fbbf24]', 'bg-[#ef4444]' );
    timerElement.classList.add('bg-[#f97316]', 'text-white' ); } else {
    timerElement.classList.remove('bg-[#f97316]', 'bg-[#ef4444]' , 'text-white' );
    timerElement.classList.add('bg-[#fbbf24]', 'text-gray-800' ); } } } function showWarning() { if (confirm(`Sesi akan
    terkunci dalam ${warningTime} detik karena tidak aktif.\n\nKlik OK untuk melanjutkan sesi.`)) {
    resetInactivityTimer(); } } function lockSession() { window.location.href='/verify-pin' ; } function resetSession()
    { if (confirm('Reset timer sesi?')) { resetInactivityTimer(); } } const activityEvents=['mousedown', 'mousemove'
    , 'keypress' , 'scroll' , 'touchstart' , 'click' ]; activityEvents.forEach(event=> {
    document.addEventListener(event, resetInactivityTimer);
    });

    resetInactivityTimer();
