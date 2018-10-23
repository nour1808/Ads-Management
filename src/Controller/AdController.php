<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {

        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }


    /**
     * Permet de créer une nouvelle annonce 
     * 
     * @Route("/ads/create", name="ads_create")
     * @IsGranted("ROLE_USER")
     * 
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();

        /*$image = new Image();
        $image->setUrl("https://picsum.photos/900/400");
        $image->setCaption("Image de annonce 25");

        $image2 = new Image();
        $image2->setUrl("https://picsum.photos/900/600");
        $image2->setCaption("Image de annonce 27");
        $ad->addImage($image)
            ->addImage($image2);*/

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }


        return $this->render('ad/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Permet d'editer une annonce.
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message= "Cette annonce ne vous apartient pas , vous ne pouvez pas la modifier")
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

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'adTitle' => $ad->getTitle()
        ]);

    }


    /**
     * Permet d'afficher le détail de l'annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }



    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message= "Cette annonce ne vous apartient pas , vous ne pouvez pas la suuprimer")
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "l'annonce : <strong>{$ad->getTitle()}</strong> a été bien supprimée !"
        );

        return $this->redirectToRoute("ads_index");
    }



}
