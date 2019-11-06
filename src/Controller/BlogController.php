<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'nom' => 'julien',
        ]);
    }


    /**
     * @Route("/blog", name="blog")
     * @param ArticleRepository $repository
     * @return Response
     */
    public function index(ArticleRepository $repository) // <--- ca c'est l'injection de dependence
    {
        $articles = $repository->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     * @param Article|null $article
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        if(!$article){
            $article = new Article();
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }


    /**
     * @Route("/blog/{id}", name="blog_show", requirements={"id": "\d+"})
     * @param Article $article
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     * @internal param $id
     */
    public function show(Article $article = null, Request $request, ObjectManager $manager) // <--- ca c'est le param converter, il sait quel est l'article par rapport a l'id passé dans la route
    {
        $comment = new Comment();

        dump($comment);
        $form = $this->createForm(CommentType::class, $comment);
        dump($form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($article->getId() !== null) {
                $comment->setCreatedAt(new \DateTime());
            }
//            $test = $comment->getArticle($article->getId());
//            dump($test);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
        ]);
    }




}
