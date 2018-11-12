<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{


    /**
     * Permet d'afficher la liste des commentaires
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setlimit(5)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }



    /**
     * Permet d'editer un commentaire dans le backoffice.
     * @Route("/admin/comments/edit/{id}", name="admin_comments_edit")
     */
    public function edit(Request $request, Comment $comment, ObjectManager $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications du commentaire : <strong> #{$comment->getId()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('admin_comments_edit', [
                'id' => $comment->getId()
            ]);
        }

        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);

    }

    /**
     * Permet de supprimer une annonce
     * @Route("/admin/comments/delete/{id}", name="admin_comments_delete")
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "La suppression du commentaire : <strong> #{$comment->getId()}</strong> a bien été effectué !"
        );

        return $this->redirectToRoute('admin_comments_index');
    }



}
