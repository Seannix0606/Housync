// Dynamic Firebase App Check Configuration
// This fetches configuration from your Laravel backend

let firebaseApp;
let appCheck;

// Function to initialize Firebase with App Check
async function initializeFirebaseWithAppCheck() {
    try {
        // Fetch configuration from Laravel backend
        const response = await fetch('/firebase-config');
        const config = await response.json();
        
        const firebaseConfig = config.firebase_config;
        const appCheckConfig = config.app_check_config;
        
        console.log('Firebase Config loaded:', firebaseConfig);
        console.log('App Check Config loaded:', appCheckConfig);
        
        // Initialize Firebase
        firebaseApp = firebase.initializeApp(firebaseConfig);
        
        // Initialize App Check
        if (appCheckConfig.debug_mode) {
            console.log('Firebase App Check: Development mode');
            
            // Set debug token if available
            if (appCheckConfig.debug_token) {
                self.FIREBASE_APPCHECK_DEBUG_TOKEN = appCheckConfig.debug_token;
            } else {
                self.FIREBASE_APPCHECK_DEBUG_TOKEN = true;
            }
            
            // Initialize with debug mode
            appCheck = firebase.appCheck().activate(appCheckConfig.recaptcha_site_key, true);
        } else {
            console.log('Firebase App Check: Production mode');
            
            // Initialize with reCAPTCHA v3
            appCheck = firebase.appCheck().activate(appCheckConfig.recaptcha_site_key, false);
        }
        
        console.log('Firebase App Check initialized successfully');
        
        // Set up global functions for token management
        setupAppCheckHelpers();
        
    } catch (error) {
        console.error('Failed to initialize Firebase App Check:', error);
        
        // Fallback: Initialize Firebase without App Check
        try {
            const fallbackConfig = {
                apiKey: "YOUR_API_KEY",
                authDomain: "housesync-dd86e.firebaseapp.com",
                databaseURL: "https://housesync-dd86e-default-rtdb.firebaseio.com",
                projectId: "housesync-dd86e",
                storageBucket: "housesync-dd86e.appspot.com",
                messagingSenderId: "YOUR_SENDER_ID",
                appId: "YOUR_APP_ID"
            };
            
            firebaseApp = firebase.initializeApp(fallbackConfig);
            console.log('Firebase initialized without App Check (fallback mode)');
        } catch (fallbackError) {
            console.error('Failed to initialize Firebase even in fallback mode:', fallbackError);
        }
    }
}

// Set up helper functions for App Check
function setupAppCheckHelpers() {
    // Function to get App Check token
    window.getAppCheckToken = async function() {
        try {
            if (!appCheck) {
                console.warn('App Check not initialized');
                return null;
            }
            
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
        
        // Set up global AJAX setup to automatically include App Check token
        $.ajaxSetup({
            beforeSend: async function(xhr, settings) {
                const token = await getAppCheckToken();
                if (token) {
                    xhr.setRequestHeader('X-Firebase-AppCheck', token);
                }
            }
        });
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFirebaseWithAppCheck);
} else {
    initializeFirebaseWithAppCheck();
}

// Export for use in other scripts
window.firebaseApp = firebaseApp;
window.appCheck = appCheck; 