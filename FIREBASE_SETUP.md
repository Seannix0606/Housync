# Firebase Setup Guide for HouseSync

## Prerequisites

### 1. Enable PHP Sodium Extension

The Firebase PHP SDK requires the `sodium` extension. To enable it:

1. Open your PHP configuration file: `D:\apache and php\php\php.ini`
2. Find the line `;extension=sodium` and uncomment it by removing the semicolon:
   ```ini
   extension=sodium
   ```
3. Restart your web server (Apache/Nginx)
4. Verify the extension is loaded: `php -m | grep sodium`

### 2. Install Firebase PHP SDK

Once the sodium extension is enabled, install the Firebase SDK:

```bash
composer require kreait/firebase-php
```

## Firebase Console Setup

### Step 1: Create Firebase Project

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click "Add project"
3. Enter project name (e.g., "housesync")
4. Choose whether to enable Google Analytics
5. Click "Create project"

### Step 2: Enable Authentication

1. In your Firebase Console, go to "Authentication" → "Sign-in method"
2. Enable the sign-in providers you want to use:
   - Email/Password
   - Google
   - Facebook
   - etc.

### Step 3: Set up Realtime Database

1. Go to "Database" → "Realtime Database"
2. Click "Create Database"
3. Choose your location
4. Start in test mode (you can change rules later)

### Step 4: Set up Cloud Storage

1. Go to "Storage"
2. Click "Get started"
3. Choose your location
4. Start in test mode

### Step 5: Get Service Account Credentials

1. Go to "Project Settings" (gear icon) → "Service accounts"
2. Click "Generate new private key"
3. Download the JSON file
4. Extract the following values from the JSON:

```json
{
  "type": "service_account",
  "project_id": "your-project-id",
  "private_key_id": "your-private-key-id",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@your-project-id.iam.gserviceaccount.com",
  "client_id": "your-client-id",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs/firebase-adminsdk-xxxxx%40your-project-id.iam.gserviceaccount.com"
}
```

### Step 6: Get Web App Configuration

1. Go to "Project Settings" → "General"
2. In the "Your apps" section, click "Add app" → "Web"
3. Register your app with a nickname
4. Copy the configuration values:

```javascript
const firebaseConfig = {
  apiKey: "your-web-api-key",
  authDomain: "your-project-id.firebaseapp.com",
  databaseURL: "https://your-project-id-default-rtdb.firebaseio.com",
  projectId: "your-project-id",
  storageBucket: "your-project-id.appspot.com",
  messagingSenderId: "your-sender-id",
  appId: "1:your-sender-id:web:your-app-id"
};
```

## Laravel Configuration

### Step 1: Update Environment Variables

Add these variables to your `.env` file (use `firebase-env-template.txt` as reference):

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_PRIVATE_KEY_ID=your-private-key-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY_HERE\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@your-project-id.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=your-client-id
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_X509_CERT_URL=https://www.googleapis.com/oauth2/v1/certs/firebase-adminsdk-xxxxx%40your-project-id.iam.gserviceaccount.com

# Firebase Database URL
FIREBASE_DATABASE_URL=https://your-project-id-default-rtdb.firebaseio.com/

# Firebase Storage Bucket
FIREBASE_STORAGE_BUCKET=your-project-id.appspot.com

# Firebase Web Configuration
FIREBASE_WEB_API_KEY=your-web-api-key
FIREBASE_AUTH_DOMAIN=your-project-id.firebaseapp.com
FIREBASE_MESSAGING_SENDER_ID=your-sender-id
FIREBASE_APP_ID=1:your-sender-id:web:your-app-id
```

**Important Notes:**
- Replace all `your-project-id`, `your-private-key-id`, etc. with your actual values
- The `FIREBASE_PRIVATE_KEY` should include the `\n` characters for line breaks
- Keep the quotes around the private key value

### Step 2: Clear Configuration Cache

```bash
php artisan config:clear
php artisan config:cache
```

## Usage Examples

### Using Firebase Service

```php
use App\Services\FirebaseService;

class UserController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function createFirebaseUser(Request $request)
    {
        $firebaseUser = $this->firebaseService->createUser([
            'email' => $request->email,
            'password' => $request->password,
            'displayName' => $request->name,
        ]);

        if ($firebaseUser) {
            // User created successfully
            return response()->json($firebaseUser);
        }

        return response()->json(['error' => 'Failed to create user'], 500);
    }

    public function storeUserData(Request $request)
    {
        $success = $this->firebaseService->storeData(
            'users/' . $request->user_id,
            [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'created_at' => now()->toISOString(),
            ]
        );

        return response()->json(['success' => $success]);
    }
}
```

### Direct Firebase Usage

```php
use Illuminate\Support\Facades\App;

class SomeController extends Controller
{
    public function someMethod()
    {
        // Get Firebase Auth
        $auth = App::make('firebase.auth');
        
        // Get Firebase Database
        $database = App::make('firebase.database');
        
        // Get Firebase Storage
        $storage = App::make('firebase.storage');
        
        // Get Firebase Messaging
        $messaging = App::make('firebase.messaging');
    }
}
```

## Testing Firebase Connection

Create a test route to verify Firebase is working:

```php
// In routes/web.php
Route::get('/test-firebase', function () {
    $firebaseService = new App\Services\FirebaseService();
    
    if ($firebaseService->isConfigured()) {
        return 'Firebase is configured and working!';
    } else {
        return 'Firebase is not properly configured.';
    }
});
```

## Security Considerations

1. **Never commit your `.env` file** - It contains sensitive Firebase credentials
2. **Use Firebase Security Rules** - Configure proper read/write permissions
3. **Validate tokens** - Always verify Firebase ID tokens on the server side
4. **Use HTTPS** - Firebase requires HTTPS for production

## Troubleshooting

### Common Issues

1. **"Class 'Kreait\Firebase\Factory' not found"**
   - Make sure you've installed the Firebase SDK: `composer require kreait/firebase-php`
   - Check that the sodium extension is enabled

2. **"Firebase Auth not initialized"**
   - Check your environment variables are set correctly
   - Verify the private key format (should include `\n` characters)
   - Clear configuration cache: `php artisan config:clear`

3. **"Invalid private key"**
   - Ensure the private key includes the full content with proper line breaks
   - Check that quotes are properly escaped in the `.env` file

4. **"Permission denied"**
   - Check Firebase Security Rules
   - Verify the service account has proper permissions

### Debug Mode

Enable debug logging in your `.env`:

```env
LOG_LEVEL=debug
```

Check the logs at `storage/logs/laravel.log` for Firebase-related errors.

## Next Steps

1. Enable PHP sodium extension
2. Install Firebase PHP SDK
3. Set up Firebase project and services
4. Configure environment variables
5. Test the connection
6. Implement Firebase authentication in your application
7. Set up proper security rules

For more advanced usage, refer to the [Firebase PHP SDK documentation](https://firebase-php.readthedocs.io/). 