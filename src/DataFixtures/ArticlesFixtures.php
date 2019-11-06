<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // creer 3 category fake
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence());
            $category->setDescription($faker->paragraph());

            $manager->persist($category);

            // creer entre 4 et 6 articles
            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                $article->setTitle($faker->sentence());
                $article->setContent($content);
                $article->setImage($faker->imageUrl());
                $article->setCreatedAt($faker->dateTimeBetween('-6 months'));
                $article->setCategory($category);

                $manager->persist($article);

                // on donne des commentaires a un article
                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                    $comment->setAuthor($faker->name);
                    $comment->setContent($content);
                    $comment->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'));
                    $comment->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
