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
