<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    public function __construct(private Filesystem $fs, private $profileFolder, private $profileFolderPublic) {}

    public function uploadProfileImage(UploadedFile $picture, string $username, string $oldPicturePath = null)
    {
        $folder = $this->profileFolder;
        $basename = $username . '_' . bin2hex(random_bytes(10));
        $extension = $picture->guessExtension() ?? 'bin';
        $filename = $basename . '.' . $extension;
        $picture->move($folder, $filename);

        if ($oldPicturePath) {
            $this->fs->remove($folder . '/' . pathinfo($oldPicturePath, PATHINFO_BASENAME));
        }

        return $this->profileFolderPublic . '/' . $filename;
    }
}
