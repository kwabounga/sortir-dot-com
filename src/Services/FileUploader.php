<?php
namespace App\Services;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory = 'uploads/photo/')
    {
        $this->targetDirectory = $targetDirectory;

    }

    public function upload(UploadedFile $file, User $u)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

//        $fileName = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
        $fileName = 'profil_picture'.'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory().'/'.$u->getId().'/', $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            dump($e);
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}