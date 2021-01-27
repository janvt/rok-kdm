<?php


namespace App\Service\Image;


use App\Entity\Image;
use App\Entity\User;
use App\Exception\ImageUploadException;
use App\Exception\NotFoundException;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    private $slugger;
    private $imageRepo;
    private $uploadsDir;
    private $uploadsPublicDir;

    public function __construct(
        SluggerInterface $slugger,
        ImageRepository $imageRepo,
        string $uploadsDir,
        string $uploadsPublicDir
    )
    {
        $this->slugger = $slugger;
        $this->imageRepo = $imageRepo;
        $this->uploadsDir = $uploadsDir;
        $this->uploadsPublicDir = $uploadsPublicDir;
    }

    /**
     * @param UploadedFile $uploadedImage
     * @param User|UserInterface $user
     * @param string $imageType
     * @return Image
     * @throws ImageUploadException
     */
    public function handleImageUpload(UploadedFile $uploadedImage, User $user, string $imageType): Image
    {
        try {
            $originalFileName = $uploadedImage->getClientOriginalName();
            $image = new Image();
            $image->setCreated(new \DateTime());
            $image->setOriginalFileName($originalFileName);
            $image->setUser($user);
            $image->setType($imageType);

            $fileName = $this->slugger->slug($originalFileName) .
                '-' .
                \bin2hex(\random_bytes(6)).
                '.'.
                $uploadedImage->guessExtension();

            $uploadedImage->move($this->uploadsDir, $fileName);

            $imagePath = new ImagePath(Image::PATH_TYPE_UPLOADS_DIR, $this->uploadsDir . $fileName);
            $image->setPath($imagePath->getFQPath());

            return $this->imageRepo->save($image);
        } catch (FileException $e) {
            throw new ImageUploadException($e->getMessage(), null, $e);
        }

    }

    /**
     * @param $imageId
     * @return string
     * @throws NotFoundException
     */
    public function getPublicPath($imageId): string
    {
        $image = $this->imageRepo->find($imageId);
        if (!$image) {
            throw new NotFoundException();
        }

        $imagePath = ImagePath::fromFQPath($image->getPath());
        if ($imagePath->getType() === Image::PATH_TYPE_UPLOADS_DIR) {
            $pathParts = \pathinfo($imagePath->getPath());

            return $this->uploadsPublicDir . $pathParts['basename'];
        }

        throw new \Exception('Unknown image path type!');
    }
}