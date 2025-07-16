// Firebase App Check Configuration
// This file should be included in your main layout

// Import Firebase App Check
import { initializeApp } from 'firebase/app';
import { initializeAppCheck, ReCaptchaV3Provider, getToken } from 'firebase/app-check';

// Your Firebase configuration
const firebaseConfig = {
    apiKey: "{{ env('FIREBASE_WEB_API_KEY') }}",
    authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
    databaseURL: "{{ env('FIREBASE_DATABASE_URL') }}",
    projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
    storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
    messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
    appId: "{{ env('FIREBASE_APP_ID') }}"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize App Check
let appCheck;

// For development - use debug tokens
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    // Debug mode - set this in Firebase Console under App Check
    self.FIREBASE_APPCHECK_DEBUG_TOKEN = true;
    
    appCheck = initializeAppCheck(app, {
        provider: new ReCaptchaV3Provider('YOUR_RECAPTCHA_SITE_KEY'), // Replace with your reCAPTCHA site key
        isTokenAutoRefreshEnabled: true
    });
} else {
    // Production mode
    appCheck = initializeAppCheck(app, {
        provider: new ReCaptchaV3Provider('YOUR_RECAPTCHA_SITE_KEY'), // Replace with your reCAPTCHA site key
        isTokenAutoRefreshEnabled: true
    });
}

// Function to get App Check token for API requests
window.getAppCheckToken = async function() {
    try {
        const appCheckTokenResponse = await getToken(appCheck);
        return appCheckTokenResponse.token;
    } catch (error) {
        console.error('Error getting App Check token:', error);
        return null;
    }
};

// Function to add App Check token to AJAX requests
window.addAppCheckToRequest = async function(requestOptions) {
    const token = await getAppCheckToken();
    if (token) {
        requestOptions.headers = requestOptions.headers || {};
        requestOptions.headers['X-Firebase-AppCheck'] = token;
    }
    return requestOptions;
};

export { app, appCheck }; 