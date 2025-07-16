<!-- Firebase App Check Scripts -->
<!-- Include this partial in your main views -->

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-check-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>

<!-- App Check Configuration -->
<script src="{{ asset('js/firebase-app-check-dynamic.js') }}"></script>

<!-- Initialize Firebase App Check -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Firebase to initialize
    setTimeout(function() {
        if (window.firebaseApp) {
            console.log('Firebase App Check initialized successfully');
        } else {
            console.warn('Firebase App Check initialization may have failed');
        }
    }, 1000);
});
</script> 