<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private readonly string $projectDir;
    private readonly array $configurations;
    private readonly SluggerInterface $slugger;
    private readonly Filesystem $filesystem;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        array $configurations,
        SluggerInterface $slugger,
        Filesystem $filesystem
    ) {
        $this->projectDir = $projectDir;
        $this->configurations = $configurations;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $type The configuration key for the upload type (e.g., 'signal', 'reunion_signal').
     * @param UploadedFile $file The uploaded file.
     * @param object $relatedEntity The entity to which the file is attached.
     * @param string $userName The name of the user performing the upload.
     * @return object The configured file entity, ready to be persisted.
     */
    public function upload(string $type, UploadedFile $file, object $relatedEntity, string $userName): object
    {
        $config = $this->getConfig($type);

        // Verify the type of the related entity
        if (!$relatedEntity instanceof $config['related_entity_class']) {
            throw new \InvalidArgumentException(sprintf('L\'entité associée doit être de type "%s" pour le type d\'upload "%s".', $config['related_entity_class'], $type));
        }

        $targetDirectory = $this->projectDir . '/' . $config['directory'];
        
        $originalClientFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileMimeType = $file->getMimeType() ?? 'application/octet-stream';

        $originalFilename = pathinfo($originalClientFilename, PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($targetDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception(sprintf('Une erreur est survenue lors de l\'upload du fichier : %s', $e->getMessage()), 0, $e);
        }

        $fileEntityClass = $config['file_entity_class'];
        $setter = $config['setter'];

        $fileEntity = new $fileEntityClass();
        $fileEntity->{$setter}($relatedEntity);

        // Set common properties, assuming they share these setters
        $now = new \DateTimeImmutable();
        $fileEntity->setUserCreate($userName);
        $fileEntity->setUserModif($userName);
        $fileEntity->setCreatedAt($now);
        $fileEntity->setUpdatedAt($now);
        $fileEntity->setNomFichier($newFilename);
        $fileEntity->setNomOriginal($originalClientFilename);
        $fileEntity->setTaille($fileSize);
        $fileEntity->setMimeType($fileMimeType);

        return $fileEntity;
    }

    
    //#[IsGranted(new Expression('is_granted("ROLE_REUSI_ADMIN") or is_granted("ROLE_REUSI_SURV_ADMIN")'))]
    #[IsGranted('ROLE_REUSI_ADMIN')]
    public function delete(string $type, string $filename): bool
    {
        $filePath = $this->getTargetDirectory($type) . '/' . $filename;
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
            return true;
        }
        return false;
    }

    public function getTargetDirectory(string $type): string
    {
        $config = $this->getConfig($type);
        return $this->projectDir . '/' . $config['directory'];
    }

    private function getConfig(string $type): array
    {
        if (!isset($this->configurations[$type])) {
            throw new \InvalidArgumentException(sprintf('Configuration d\'upload non valide : "%s".', $type));
        }
        return $this->configurations[$type];
    }
}

