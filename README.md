# Laravel Cloudinary Image Upload

This project demonstrates how to upload images to Cloudinary from a Laravel application using the `cloudinary/cloudinary_php` package.

## Prerequisites
- PHP 8.2 or higher
- Laravel 12.x
- Composer
- A Cloudinary account (sign up at [cloudinary.com](https://cloudinary.com))

## Step-by-Step Setup

### 1. Create a Laravel Project
If you don’t already have a Laravel project, create one:
```bash
composer create-project laravel/laravel cloudinary-upload
cd cloudinary-upload
```

### 2. Install Cloudinary PHP SDK
Install the `cloudinary/cloudinary_php` package via Composer:
```bash
composer require cloudinary/cloudinary_php
```
Verify it’s in your `composer.json`:
```json
"require": {
    "cloudinary/cloudinary_php": "^3.1",
    ...
}
```

### 3. Configure Cloudinary Credentials
1. Log in to your Cloudinary account and get your **Cloud Name**, **API Key**, and **API Secret** from the Dashboard.
2. Add the credentials to your `.env` file in the project root:
   ```
   CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
   ```
   Example:
   ```
   CLOUDINARY_URL=cloudinary://123456789012345:abcdef1234567890@mycloudname
   ```
   - Replace `API_KEY`, `API_SECRET`, and `CLOUD_NAME` with your actual values.
   - Ensure no spaces around `=`.

### 4. Set Up Routes
Edit `routes/web.php` to define routes for the upload form and submission:
```php
use App\Http\Controllers\ImageController;

Route::get('/upload', [ImageController::class, 'showForm'])->name('upload.form');
Route::post('/upload', [ImageController::class, 'upload'])->name('upload.image');
```

### 5. Create the Image Controller
Generate a controller:
```bash
php artisan make:controller ImageController
```
Update `app/Http/Controllers/ImageController.php` with the following:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Cloudinary as CloudinaryCore;

class ImageController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new CloudinaryCore(env('CLOUDINARY_URL'));
    }

    public function showForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedFile = $request->file('image');
        $uploadedImage = $this->cloudinary->uploadApi()->upload($uploadedFile->getRealPath(), [
            'folder' => 'laravel_uploads',
        ]);

        $imageUrl = $uploadedImage['secure_url'];

        return back()->with('success', 'Image uploaded successfully!')
                    ->with('image_url', $imageUrl);
    }
}
```
- The `uploadApi()->upload()` method uploads the image to Cloudinary in the `laravel_uploads` folder.
- The secure URL is returned and passed to the view.

### 6. Create the Upload View
Create `resources/views/upload.blade.php`:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Image Upload with Cloudinary</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Upload Image</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
                <br>
                <img src="{{ session('image_url') }}" alt="Uploaded Image" class="mt-2 max-w-full h-auto">
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('upload.image') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Select Image</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Upload</button>
        </form>
    </div>
</body>
</html>
```

### 7. Clear Configuration Cache
Ensure Laravel picks up the `.env` changes:
```bash
php artisan config:clear
php artisan cache:clear
```

### 8. Run the Application
Start the Laravel development server:
```bash
php artisan serve
```
Visit `http://localhost:8000/upload` in your browser.

### 9. Upload an Image
- Select an image (JPEG, PNG, JPG, or GIF, max 2MB).
- Click "Upload".
- On success, you’ll see a confirmation message and the uploaded image.

### 10. Find Your Images in Cloudinary
- Log in to [cloudinary.com](https://cloudinary.com).
- Go to **Media Library** in the left sidebar.
- Look for the `laravel_uploads` folder.
- Your uploaded images will be listed there with their public IDs (e.g., `test.jpg`).

## Troubleshooting
- **Error: "Invalid configuration"**:
  - Verify `CLOUDINARY_URL` in `.env` is correct and matches your Cloudinary credentials.
  - Clear cache: `php artisan config:clear`.
- **Error: "Class not found"**:
  - Ensure `composer require cloudinary/cloudinary_php` ran successfully.
  - Run `composer dump-autoload`.
- **Images not in Cloudinary**:
  - Check the `$imageUrl` returned after upload to confirm the cloud name matches your account.

## Notes
- Images are stored in the `laravel_uploads` folder in Cloudinary due to the `'folder' => 'laravel_uploads'` option.
- The secure URL (e.g., `https://res.cloudinary.com/your_cloud_name/image/upload/laravel_uploads/test.jpg`) is displayed in the app and points to the image in Cloudinary.