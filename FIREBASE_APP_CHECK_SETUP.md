# Firebase App Check Setup Guide

## 🛡️ What is Firebase App Check?

Firebase App Check is an additional security layer that helps protect your Firebase resources from abuse. It ensures that requests to your Firebase services come from your legitimate app, not from malicious actors.

## 🚀 Step-by-Step Setup

### Step 1: Register Your Web App in Firebase Console

1. **Go to Firebase Console** → Your Project → **App Check**
2. **Click "Register"** next to your Housesync Web App
3. **Select "Web" platform**
4. **Fill in the details**:
   - App nickname: `HouseSync Web App`
   - App domain: 
     - Development: `localhost:8000`
     - Production: `yourdomain.com`

### Step 2: Choose Attestation Provider

**For Development:**
- Use **Debug tokens** (temporary, for testing only)

**For Production:**
- Use **reCAPTCHA v3** (recommended for most web apps)

### Step 3: Set Up reCAPTCHA v3

1. **Go to [Google reCAPTCHA](https://www.google.com/recaptcha/admin)**
2. **Create a new site**:
   - Label: `HouseSync App Check`
   - reCAPTCHA type: **v3**
   - Domains: 
     - `localhost` (for development)
     - `yourdomain.com` (for production)
3. **Copy the Site Key** (you'll need this)

### Step 4: Configure Environment Variables

Add these to your `.env` file:

```env
# Firebase App Check
FIREBASE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
FIREBASE_APP_CHECK_DEBUG_TOKEN=true  # Set to false in production
```

### Step 5: Include Firebase SDK and App Check

Add this to your main layout file (e.g., `resources/views/layouts/app.blade.php`):

```html
<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-check-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>

<!-- App Check Configuration -->
<script src="{{ asset('js/firebase-app-check-simple.js') }}"></script>
```

### Step 6: Update Your Firebase Configuration

Edit `public/js/firebase-app-check-simple.js` and replace the placeholders:

```javascript
const firebaseConfig = {
    apiKey: "{{ env('FIREBASE_WEB_API_KEY') }}",
    authDomain: "housesync-dd86e.firebaseapp.com",
    databaseURL: "https://housesync-dd86e-default-rtdb.firebaseio.com",
    projectId: "housesync-dd86e",
    storageBucket: "housesync-dd86e.appspot.com",
    messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
    appId: "{{ env('FIREBASE_APP_ID') }}"
};
```

### Step 7: Configure Debug Tokens (Development Only)

1. **In Firebase Console** → App Check → Your Web App
2. **Click "Debug tokens"**
3. **Add a debug token** for your development environment
4. **Copy the debug token**
5. **Add to your local development**:

```javascript
// In your JavaScript console or app
self.FIREBASE_APPCHECK_DEBUG_TOKEN = 'your-debug-token-here';
```

### Step 8: Update Your AJAX Requests

For any AJAX requests to Firebase, include the App Check token:

```javascript
// For jQuery AJAX
const ajaxOptions = {
    url: '/api/apartments',
    method: 'GET',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
};

// Add App Check token
addAppCheckToAjax(ajaxOptions);

// For Fetch API
addAppCheckToFetch('/api/apartments', {
    method: 'GET',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});
```

### Step 9: Update Your Laravel Middleware

Modify your Firebase middleware to validate App Check tokens:

```php
// In app/Http/Middleware/FirebaseAuthMiddleware.php
private function validateAppCheckToken(Request $request): bool
{
    $appCheckToken = $request->header('X-Firebase-AppCheck');
    
    if (!$appCheckToken) {
        return false; // or true if you want to make it optional initially
    }
    
    // Validate the App Check token with Firebase
    try {
        $firebaseService = app(FirebaseService::class);
        return $firebaseService->verifyAppCheckToken($appCheckToken);
    } catch (\Exception $e) {
        Log::error('App Check token validation failed: ' . $e->getMessage());
        return false;
    }
}
```

### Step 10: Test Your Setup

1. **Open your web app in the browser**
2. **Check the browser console** for App Check initialization messages
3. **Make an API request** and verify the App Check token is included
4. **Check Firebase Console** → App Check → Metrics for request counts

## 🔧 Troubleshooting

### Common Issues

**1. "App Check token is missing" error**
- Ensure the Firebase SDK is loaded before your app code
- Check that the reCAPTCHA site key is correct
- Verify the domain is registered in reCAPTCHA settings

**2. "App Check token is invalid" error**
- Check that the site key matches your reCAPTCHA configuration
- Ensure the domain is whitelisted in both Firebase and reCAPTCHA
- For development, make sure debug tokens are properly configured

**3. "reCAPTCHA verification failed" error**
- Verify the site key is correct
- Check that the domain is registered in reCAPTCHA console
- Ensure reCAPTCHA v3 is selected (not v2)

### Debug Commands

```bash
# Check Firebase configuration
php artisan firebase:secure --test

# Check App Check status
curl -H "X-Firebase-AppCheck: test-token" http://localhost:8000/test-firebase
```

## 🌍 Environment-Specific Configuration

### Development Environment
```javascript
// Use debug tokens
self.FIREBASE_APPCHECK_DEBUG_TOKEN = 'your-debug-token';
appCheck = firebase.appCheck().activate('site-key', true);
```

### Production Environment
```javascript
// Use reCAPTCHA v3
appCheck = firebase.appCheck().activate('site-key', false);
```

## 📊 Monitoring

After setup, monitor your App Check usage:

1. **Firebase Console** → App Check → Metrics
2. **Check request counts** and success rates
3. **Monitor for unusual patterns** or high failure rates
4. **Set up alerts** for App Check failures

## 🔐 Security Best Practices

1. **Never commit debug tokens** to version control
2. **Use environment variables** for all configuration
3. **Rotate debug tokens** regularly
4. **Monitor App Check metrics** for anomalies
5. **Test thoroughly** before deploying to production

## 📝 Next Steps

1. **Register your web app** in Firebase Console
2. **Set up reCAPTCHA v3** with your domain
3. **Configure debug tokens** for development
4. **Update your JavaScript** to include App Check tokens
5. **Test the implementation** thoroughly
6. **Deploy to production** with proper reCAPTCHA configuration

---

**⚠️ Important**: App Check is an additional security layer. It works alongside your Firebase security rules, not as a replacement for them. 