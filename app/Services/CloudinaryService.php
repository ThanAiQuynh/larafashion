<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    /**
     * Upload an image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string The secure URL of the uploaded image
     */
    public function uploadImage(UploadedFile $file, string $folder = 'products'): string
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => 'larafashion/' . $folder,
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ]);

        return $result['secure_url'];
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $url The Cloudinary URL of the image
     * @return bool
     */
    public function deleteImage(string $url): bool
    {
        $publicId = $this->extractPublicId($url);

        if ($publicId) {
            try {
                $this->cloudinary->uploadApi()->destroy($publicId);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Extract public ID from Cloudinary URL
     *
     * @param string $url
     * @return string|null
     */
    protected function extractPublicId(string $url): ?string
    {
        // Match Cloudinary URL pattern: .../larafashion/folder/filename
        if (preg_match('/\/larafashion\/([^\/.]+\/[^\/.]+)/', $url, $matches)) {
            return 'larafashion/' . $matches[1];
        }

        return null;
    }
}
