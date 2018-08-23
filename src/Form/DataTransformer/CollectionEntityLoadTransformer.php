<?php

namespace App\Form\DataTransformer;

use App\Entity\ImageFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class CollectionEntityLoadTransformer implements DataTransformerInterface
{
    private $em;
    private $propertyName;

    public function __construct(EntityManagerInterface $entityManager, $propertyName)
    {
        $this->em = $entityManager;
        $this->propertyName = $propertyName;
    }

    public function transform($object)
    {
        return $object;
    }

    public function reverseTransform($collection)
    {
        $collectionArray = $collection->toArray();

        foreach ($collectionArray as $index => &$object) {
            $accessor = 'get'.$this->propertyName;

            $oldObject = $this->em->getRepository(ImageFile::class)->findOneBy([
                $this->propertyName => $object->$accessor(),
            ]);

            if ($oldObject) {
                $values = (array) $object;

                foreach ($values as $name => $value) {
                    $shortNameParts = explode("\x00", $name);
                    $shortName = last($shortNameParts);
                    if ($value !== null) {
                        $method = 'set'.$shortName;
                        $oldObject->$method($value);
                    }
                }

                $object = $oldObject;
            }
        }

        return new ArrayCollection($collectionArray);
    }
}
