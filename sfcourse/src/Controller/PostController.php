<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\FileUploader;
use App\Services\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

/**
     * @Route("/post", name="post.")
     */
class PostController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        // dump($posts);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader, Notification $notification): Response
    {
        $post = new Post();
        $form = $this->createForm(type: PostType::class, data: $post, options: []);
        $form->handleRequest($request);

        $form->getErrors();
        if ($form->isSubmitted() && $form->isValid()) { 
            //entity manager
            $em = $doctrine->getManager();
            $file = $request->files->get(key:'post')['attachment'];
            if ($file) {
                $filename = $fileUploader->uploadFile($file);
                $post->setImage($filename);
                $em->persist($post);
                $em->flush();
            }

            return $this->redirect($this->generateUrl(route:'post.index'));
        }


        //return a response
        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]); 
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Post $post): Response
    {
        // dump($post); die;
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function remove(Post $post, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash(type: 'success', message: 'Your post was removed');
        return $this->redirect($this->generateUrl(route:'post.index'));
    }
}
