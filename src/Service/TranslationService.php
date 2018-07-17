<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RefUnit;
use Doctrine\ORM\EntityManagerInterface;
use Translation\Common\Model\Message;
use Translation\Common\Storage;

class TranslationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $cacheDir;


    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $cacheDir
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        $cacheDir = ''
    ) {
        $this->em = $entityManager;
        $this->cacheDir = $cacheDir;
    }

    public function clearTranslationCache()
    {
        $cacheDir = dirname($this->cacheDir);

        foreach (['prod', 'dev'] as $env) {
            array_map('unlink', glob("$cacheDir/$env/translations/*"));
            array_map('unlink', glob("$cacheDir/$env/app*ProjectContainer.php"));

        }
    }
}
