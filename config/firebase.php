<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Project Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for your Firebase project.
    | You can find these values in your Firebase Console under Project Settings.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID', ''),
    
    'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID', ''),
    
    'private_key' => env('FIREBASE_PRIVATE_KEY', ''),
    
    'client_email' => env('FIREBASE_CLIENT_EMAIL', ''),
    
    'client_id' => env('FIREBASE_CLIENT_ID', ''),
    
    'auth_uri' => env('FIREBASE_AUTH_URI', 'https://accounts.google.com/o/oauth2/auth'),
    
    'token_uri' => env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
    
    'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL', 'https://www.googleapis.com/oauth2/v1/certs'),
    
    'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | This is the URL to your Firebase Realtime Database.
    | You can find this in your Firebase Console under Database section.
    |
    */
    
    'database_url' => env('FIREBASE_DATABASE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | This is your Firebase Storage bucket name.
    | You can find this in your Firebase Console under Storage section.
    |
    */
    
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Web API Key
    |--------------------------------------------------------------------------
    |
    | This is your Firebase Web API Key used for client-side operations.
    | You can find this in your Firebase Console under Project Settings.
    |
    */
    
    'web_api_key' => env('FIREBASE_WEB_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Auth Domain
    |--------------------------------------------------------------------------
    |
    | This is your Firebase Auth Domain used for authentication.
    | Usually in the format: your-project-id.firebaseapp.com
    |
    */
    
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Messaging Sender ID
    |--------------------------------------------------------------------------
    |
    | This is used for Firebase Cloud Messaging (FCM).
    | You can find this in your Firebase Console under Project Settings.
    |
    */
    
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase App ID
    |--------------------------------------------------------------------------
    |
    | This is your Firebase App ID.
    | You can find this in your Firebase Console under Project Settings.
    |
    */
    
    'app_id' => env('FIREBASE_APP_ID', ''),

]; 