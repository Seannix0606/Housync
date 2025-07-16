// Simple Firebase App Check Configuration
// Include this after Firebase SDK scripts

// Your Firebase configuration
const firebaseConfig = {
    apiKey: "YOUR_API_KEY", // Replace with your actual API key
    authDomain: "housesync-dd86e.firebaseapp.com",
    databaseURL: "https://housesync-dd86e-default-rtdb.firebaseio.com",
    projectId: "housesync-dd86e",
    storageBucket: "housesync-dd86e.appspot.com",
    messagingSenderId: "YOUR_SENDER_ID", // Replace with your actual sender ID
    appId: "YOUR_APP_ID" // Replace with your actual app ID
};

// Initialize Firebase
const app = firebase.initializeApp(firebaseConfig);

// Initialize App Check
let appCheck;

// Check if we're in development or production
const isDevelopment = window.location.hostname === 'localhost' || 
                     window.location.hostname === '127.0.0.1' || 
                     window.location.hostname.includes('localhost');

if (isDevelopment) {
    // Development mode - use debug tokens
    console.log('Firebase App Check: Development mode');
    
    // Set debug token (you'll get this from Firebase Console)
    self.FIREBASE_APPCHECK_DEBUG_TOKEN = true;
    
    // For development, you can use a debug provider
    appCheck = firebase.appCheck().activate('YOUR_RECAPTCHA_SITE_KEY', true);
} else {
    // Production mode - use reCAPTCHA
    console.log('Firebase App Check: Production mode');
    
    appCheck = firebase.appCheck().activate('YOUR_RECAPTCHA_SITE_KEY', false);
}

// Function to get App Check token for API requests
window.getAppCheckToken = async function() {
    try {
        const token = await firebase.appCheck().getToken();
        return token.token;
    } catch (error) {
        console.error('Error getting App Check token:', error);
        return null;
    }
};

// Function to add App Check token to fetch requests
window.addAppCheckToFetch = async function(url, options = {}) {
    const token = await getAppCheckToken();
    if (token) {
        options.headers = options.headers || {};
        options.headers['X-Firebase-AppCheck'] = token;
    }
    return fetch(url, options);
};

// Function to add App Check token to jQuery AJAX requests
if (typeof $ !== 'undefined') {
    window.addAppCheckToAjax = async function(ajaxOptions) {
        const token = await getAppCheckToken();
        if (token) {
            ajaxOptions.headers = ajaxOptions.headers || {};
            ajaxOptions.headers['X-Firebase-AppCheck'] = token;
        }
        return $.ajax(ajaxOptions);
    };
}

console.log('Firebase App Check initialized successfully'); 