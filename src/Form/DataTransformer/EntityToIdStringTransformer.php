<?php

namespace App\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

class EntityToIdStringTransformer implements DataTransformerInterface
{
    private $manager;
    private $className;

    public function __construct(ObjectManager $manager, $className)
    {
        $this->manager = $manager;
        $this->className = $className;
    }

    public function transform($object)
    {
        if (null === $object) {
            return '';
        }

        if(!$object instanceof $this->className) {
            return '';
        }

        return $object->getId();
    }

    public function reverseTransform($idText)
    {
        // no issue number? It's optional, so that's ok
        if (!$idText) {
            return null;
        }

        return $this->manager->getRepository($this->className)->find($idText);
    }
}
