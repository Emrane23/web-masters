<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ObjectFiller
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    /**
     * Fill object attributes with data from an associative array.
     *
     * @param object $object The object to be filled.
     * @param array  $data   Associative array containing attribute names as keys and values to set.
     * @param EntityManagerInterface $entityManager The entity manager to retrieve related entities.
     * @return object The updated object.
     */
    public function fill($object, array $data)
    {
        foreach ($data as $key => $value) {
            $methodName = "set" . ucfirst($key);

            if (method_exists($object, $methodName)) {
                $reflectionMethod = new \ReflectionMethod(get_class($object), $methodName);
                $parameters = $reflectionMethod->getParameters();

                if (count($parameters) === 1) {
                    $parameterType = $parameters[0]->getType();
                    if ($parameterType instanceof \ReflectionNamedType && !$parameterType->isBuiltin()) {
                        $relatedEntityClass = $parameterType->getName();
                        $relatedEntity = $this->entityManager->getRepository($relatedEntityClass)->find($value);
                        if ($relatedEntity) {
                            $object->{$methodName}($relatedEntity);
                        } else {
                            throw new \InvalidArgumentException("Entity $relatedEntity doesn't exist");
                        }
                    } else {
                        $object->{$methodName}($value);
                    }
                }
            }
        }

        return $object;
    }
}
