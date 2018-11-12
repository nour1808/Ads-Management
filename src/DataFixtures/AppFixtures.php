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
use App\Entity\Role;
use App\Entity\Booking;
use App\Entity\Comment;

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

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Nour')
            ->setLastName('Berjaoui')
            ->setEmail('nour1808@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://picsum.photos/960/600')
            ->setIntroduction($faker->sentence)
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);

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
            $coverImage = $faker->imageUrl(1280, 490);
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

            for ($j = 1; $j <= mt_rand(6, 18); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence)
                    ->setAd($ad);

                $manager->persist($image);
            }

            //Gestion des réservations :
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $startDateClone = clone $startDate;

                $duration = mt_rand(2, 10);

                $endDate = $startDateClone->modify("+$duration days ");

                $amount = $ad->getPrice() * $duration;

                $booker = $users[mt_rand(0, count($users) - 1)];

                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setCreatedAt($createdAt)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setAmount($amount)
                    ->setComment($comment);

                $manager->persist($booking);

                if (mt_rand(0, 1)) {
                    $comment = new Comment();

                    $comment->setContent($faker->paragraph())
                        ->setAuthor($booker)
                        ->setRating(mt_rand(1, 5))
                        ->setAd($ad);
                    $manager->persist($comment);
                }
            }

        }

        $manager->flush();
    }
}
