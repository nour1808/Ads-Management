<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookController extends AbstractController
{
    /**
     * Permet d'afficher la liste des réservations
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_books_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
            ->setPage($page);

        return $this->render('admin/book/index.html.twig', [
            'pagination' => $pagination
        ]);
    }



    /**
     * Permet d'editer une réservation dans le backoffice.
     * @Route("/admin/bookings/edit/{id}", name="admin_book_edit")
     */
    public function edit(Request $request, Booking $book, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book->setAmount(0);
            //$manager->persist($book);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de la réservation : <strong> #{$book->getId()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('admin_books_index');
        }

        return $this->render('admin/book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);

    }

    /**
     * Permet de supprimer une réservation
     * @Route("/admin/bookings/delete/{id}", name="admin_book_delete")
     */
    public function delete(Booking $book, ObjectManager $manager)
    {
        $manager->remove($book);
        $manager->flush();

        $this->addFlash(
            'success',
            "La suppression de la réservation : <strong> #{$book->getId()}</strong> a bien été effectué !"
        );

        return $this->redirectToRoute('admin_books_index');
    }

}
