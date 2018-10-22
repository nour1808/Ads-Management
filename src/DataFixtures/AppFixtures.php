<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        //Gestion des users

        $users = [];
        $genres = ['male', 'female'];

        for ($k = 1; $k <= 10; $k++) {
            $user = new User();

            $genre = $faker->randomElement($genres);
            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $hash = $this->encoder->encodePassword($user, 'password');

            if ($genre == "male") $picture = $picture . 'men/' . $pictureId;
            else $picture = $picture . 'women/' . $pictureId;

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                //->setslug($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }


        // Gestion des annonces
        for ($i = 1; $i < 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 360))
                ->setRooms(mt_rand(1, 8))
                ->setAuthor($user);
            $manager->persist($ad);

            for ($j = 1; $j <= mt_rand(3, 12); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence)
                    ->setAd($ad);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}
