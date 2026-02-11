<?php

namespace App\Service;

use App\Entity\FichiersSignaux;
use App\Entity\Signal;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class FileUploaderService
{
    private readonly string $targetDirectory;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire('%env(FICHIERS_SIGNAUX_DIRECTORY)%')] string $relativeTargetDirectory,
        private readonly SluggerInterface $slugger,
        private readonly Filesystem $filesystem
    ) {
        $this->targetDirectory = $projectDir . '/' . $relativeTargetDirectory;
    }

    /**
     * Gère l'upload d'un fichier, le déplace et retourne l'entité FichiersSignaux configurée.
     *
     * @param UploadedFile $file Le fichier uploadé depuis le formulaire.
     * @param Signal $signal Le signal auquel attacher le fichier.
     * @param string $userName L'utilisateur qui réalise l'upload.
     * @return FichiersSignaux L'entité configurée, prête à être persistée.
     */
    public function uploadFile(UploadedFile $file, Signal $signal, string $userName): FichiersSignaux
    {


        $originalClientFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileMimeType = $file->getMimeType() ?? 'application/octet-stream';

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // "slug" le nom du fichier pour le sécuriser
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $now = new \DateTimeImmutable();
        // Déplace le fichier dans le répertoire de destination

        try {
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            // Gérer l'exception si quelque chose se passe mal pendant le déplacement
            // par exemple, logger l'erreur, afficher un message, etc.
            throw new \Exception(sprintf('Une erreur est survenue lors de l\'upload du fichier : %s', $e->getMessage()), 0, $e);
        }

        // Crée et configure l'entité
        $fichierSignal = new FichiersSignaux();
        $fichierSignal->setsignalLie($signal);
        $fichierSignal->setUserCreate($userName);
        $fichierSignal->setUserModif($userName);
        $fichierSignal->setCreatedAt($now);
        $fichierSignal->setUpdatedAt($now);
        $fichierSignal->setNomFichier($newFilename);
        $fichierSignal->setNomOriginal($originalClientFilename);
        $fichierSignal->setTaille($fileSize);
        $fichierSignal->setMimeType($fileMimeType);

        return $fichierSignal;
    }

    /**
     * Supprime un fichier du répertoire d'upload.
     */
    public function deleteFile(string $filename): bool
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
            return true;
        }
        return false;
    }
    
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
