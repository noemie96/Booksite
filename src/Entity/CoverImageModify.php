<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class CoverImageModify
{
   
    /**
     * @Assert\NotBlank(message="Veuillez ajouter une image")
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif")
     * @Assert\File(maxSize="1024k", maxSizeMessage="Taille du fichier trop grande")
     *
     */
    private $CoverImageModify;

    public function getCoverImageModify()
    {
        return $this->CoverImageModify;
    }

    public function setCoverImageModify($CoverImageModify)
    {
        $this->CoverImageModify = $CoverImageModify;
        return $this;
    }
}
