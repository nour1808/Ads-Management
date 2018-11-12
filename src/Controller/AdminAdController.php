<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\PaginationService;

class AdminAdController extends AbstractController
{
    /**
     * Permet d'afficher la liste des annonces
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index", requirements={"page":"\d+"})
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {

        $pagination->setEntityClass(Ad::class)
            ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'editer une annonce dans le backoffice.
     * @Route("/admin/ads/edit/{id}", name="admin_ads_edit")
     */
    public function edit(Request $request, Ad $ad, ObjectManager $manager)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'<strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('admin_ads_edit', [
                'id' => $ad->getId()
            ]);
        }

        return $this->render('admin/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);

    }

    /**
     * Permet de supprimer une annonce
     * @Route("/admin/ads/delete/{id}", name="admin_ads_delete")
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {

        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer cette annonce : <strong>{$ad->getTitle()}</strong>  parce qu'elle possède déjà des réservations ! "
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "La suppression de l'annonce : <strong>{$ad->getTitle()}</strong> a bien été effectué !"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }

}
