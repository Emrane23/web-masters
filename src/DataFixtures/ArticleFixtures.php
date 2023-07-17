<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $images = ["https://picsum.photos/id/237/300/150","https://picsum.photos/seed/picsum/300/150","https://picsum.photos/300/150?grayscale","https://picsum.photos/300/150/?blur"];
        $faker = Factory::create();
        // $product = new Product();
        // $manager->persist($product);
        for ($i=1; $i < 6; $i++) { 
            $category = new Category ;
            $category->setTitle("Category $i");
            $category->setDescription($faker->text);
            $manager->persist($category);
            
            for ($j=1; $j <3 ; $j++) { 
                $article = new Article ; 
                $article->setTitle($faker->title);
                $image = $images[array_rand($images)];
                $article->setImage($image);
                $article->setDescription($faker->text);
                $article->setCategory($category);
                $manager->persist($article);
            }
            $manager->flush();
        }

    }
}
