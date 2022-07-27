<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    

    class AdminPropertyController extends AbstractController{

        /**
         * @var PropertyRepository
         */
        private $repository;

        /**
         * @var ObjectManager
         */
        private $em;

        public function __construct(PropertyRepository $repository, ManagerRegistry $manager)
        {
            $this->repository = $repository;
            $this->em = $manager->getManager();
        }

        /**
         * @Route("/admin", name="admin.property.index")
         */
        public function index(): Response
        {
            $properties = $this->repository->findAll();
            return $this->render('admin/property/index.html.twig', compact('properties'));
        }

        /**
         * @Route("/admin/property", name="admin.property.new")
         */
        public function new( Request $request): Response
        {
            $property = new Property;
            $form = $this->createForm(PropertyType::class, $property);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $property->setCreatedAt(new \DateTime());
                $this->em->persist($property);
                $this->em->flush($property);
                $this->addFlash('success', 'Bien enregistré avec succés');
                return $this->redirectToRoute('admin.property.index');
            }
            return $this->render('admin/property/new.html.twig', [
                'property' => $property,
                'form' => $form->createView()
            ]);
        }

        /**
         * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
         */
        public function edit(Property $property, Request $request): Response
        {
            $form = $this->createForm(PropertyType::class, $property);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $this->em->flush($property);
                $this->addFlash('success', 'Bien modifié avec succés');
                return $this->redirectToRoute('admin.property.index');
            }
            return $this->render('admin/property/edit.html.twig', [
                'property' => $property,
                'form' => $form->createView()
            ]);
        }

        /**
         * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
         * @param Property $property
         * @return RedirectResponse
         */
        public function delete(Property $property, Request $request): Response
        {
            $submittedRequest = $request->request->get('_token');
            if ($this->isCsrfTokenValid('delete'.$property->getId(), $submittedRequest )){
                $this->em->remove($property);
                $this->em->flush();
                $this->addFlash('success', 'Bien supprimé avec succés');
            }
            return $this->redirectToRoute('admin.property.index');
        }
    }