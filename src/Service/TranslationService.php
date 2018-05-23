<?php

namespace App\Service;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Translation\Common\Model\Message;
use Translation\Common\Storage;

class TranslationService implements Storage
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

    /**
     * @param string $locale
     * @param string $domain
     * @param string $key
     * @return Message
     */
    public function get($locale, $domain, $key)
    {
        $ingredient = $this->em->getRepository(Ingredient::class)->findBy(['name' => $key]);
        if (!$ingredient) {
            return new Message($key, $domain, $locale, $key);
        }
        $function = 'get' . $locale;
        return new Message($key, $domain, $locale, $ingredient->$function());
    }

    public function create(Message $message)
    {
        die('TranslationService create');
        // TODO: Implement create() method.
    }

    public function update(Message $message)
    {
        die('TranslationService update');
        // TODO: Implement update() method.
    }

    public function delete($locale, $domain, $key)
    {
        die('TranslationService delete');
        // TODO: Implement delete() method.
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
