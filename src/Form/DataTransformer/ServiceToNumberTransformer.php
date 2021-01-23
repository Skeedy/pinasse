<?php
namespace App\Form\DataTransformer;
use App\Entity\Service;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ServiceToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * Transforms an object (assoc) to a string (number).
     *
     * @param Service|null $service
     * @return string
     */
    public function transform($service)
    {
        if (null === $service) {
            return '';
        }
        return $service->getId();
    }
    /**
     * Transforms a string (number) to an object (assoc).
     *
     * @param string $serviceNumber
     * @return Service|null
     * @throws TransformationFailedException if object (assoc) is not found.
     */
    public function reverseTransform($serviceNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$serviceNumber) {
            return;
        }
        $service = $this->entityManager
            ->getRepository(Service::class)
            // query for the issue with this id
            ->find($serviceNumber);
        if (null === $serviceNumber) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'price with number "%s" does not exist!',
                $serviceNumber
            ));
        }
        return $service;
    }
}