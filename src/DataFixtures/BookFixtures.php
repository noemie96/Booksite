<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Books;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BookFixtures extends Fixture
{
    //gestion du hash de password
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder =  $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        //gestion des utilisateurs
        $users = []; //initialisation d'un tableau pour associer Books et User
        $genres = ['male', 'femelle'];

        for($u=1; $u <=10; $u++){
            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture= 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99).'.jpg';
            $picture .=($genre == 'male' ? 'men/' : 'women/').$pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('<p></p>', $faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture($picture);

                $manager->persist($user);
                $users[]= $user; //ajouter l'utilisateur fraichement créé dans le tableau pour l'association avec les annonces
        }


        //gestion des annonces
        for($a =1; $a<=10; $a++){
            $books = new Books();
            $title = $faker->sentence($nbWords = 1, $variableNbWords = true);
            $author = $faker->sentence($nbWords = 1, $variableNbWords = true);
            $genre = $faker->sentence($nbWords = 1, $variableNbWords = true);
            $resume = '<p>'.join('</p><p>',$faker->paragraphs(1)).'</p>';
            $user = $users[rand(0,count($users)-1)];
            

            $books->setTitle($title)
                  ->setImage('https://picsum.photos/150/350')
                    ->setAuthor($author)
                    ->setGenre($genre)
                    ->setResume($resume)
                    ->setPrice(rand(6,15))
                    ->setUtilisateur($user)
                    ;
                    


            $manager->persist($books);


          
           
        }

        $manager->flush();
    }
}
