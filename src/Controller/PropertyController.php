<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repo, ManagerRegistry $manager){
        $this->repository = $repo;
        $this->em = $manager->getManager();
    }

    /**
     * @Route("/property", name="property.index")
     */
    public function index(): Response
    {

        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            'properties' => $properties
        ]);
    }

    /**
     * @Route("/biens/{slug}.{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(Property $property, string $slug): Response
    {
        if ($property->getSlug() !== $slug){
            return $this->redirectToRoute('property.show',[
                'id'=>$property->getId(),
                'slug'=>$property->getSlug()
            ]);
        }
        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'propereties'
        ]);
    }
}
