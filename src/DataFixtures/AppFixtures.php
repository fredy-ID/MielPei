<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Producer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    public function load(ObjectManager $manager)
    {
        $main = new User();
        $main->setEmail('admin@admin.fr');
        $main->setFirstName('Admin_firstName');
        $main->setLastName('Admin_lastName');
        $main->setNumber('0692123456');
        $main->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $main->setPassword('$argon2id$v=19$m=65536,t=4,p=1$SlJXN3U3blUvblN3ZHBLSw$GUACfbBaR3VQb6b22fQmlhBdelMDMss+7zZWd+vjGiU');
        $manager->persist($main);

        $admin = new Admin();
        $admin->setUser($main);
        $manager->persist($admin);


        $user2 = new User();
        $user2->setEmail('client@client.fr');
        $user2->setFirstName('user2_firstName');
        $user2->setLastName('user2_lastName');
        $user2->setNumber('0692123456');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword('$argon2id$v=19$m=65536,t=4,p=1$SlJXN3U3blUvblN3ZHBLSw$GUACfbBaR3VQb6b22fQmlhBdelMDMss+7zZWd+vjGiU');
        $manager->persist($user2); 

        $user = new User();
        $user->setEmail('producteur@producteur.fr');
        $user->setFirstName('user_firstName');
        $user->setLastName('user_lastName');
        $user->setNumber('0692123456');
        $user->setRoles(['ROLE_USER','ROLE_PRODUCER']);
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$SlJXN3U3blUvblN3ZHBLSw$GUACfbBaR3VQb6b22fQmlhBdelMDMss+7zZWd+vjGiU');
        $manager->persist($user);

        $adminProducer = new Producer();
        $adminProducer->setUser($admin);
        $adminProducer->setIntroduction("L'administrateur peut également être producteur !");
        $manager->persist($adminProducer);

        $producer = new Producer();
        $producer->setUser($user);
        $producer->setIntroduction('Un excellent producteur de viande au miel !');
        $manager->persist($producer);

        $manager->flush();

        for ($i=0; $i < 7; $i++) { 
            $product = new Product();
            $product->setName('Exemple de miel ' . $i);
            $product->setDescription('Du miel tellement bon et tellement rare que les producteurs de toute la Réunion rêveraient y goûter au moins une fois dans leur vie.');
            $product->setPrice(1 . $i);
            $product->setNumberOfPurchases($i);
            $product->setOwner($producer);
            $product->setProductionArea('La Saline');
            $product->setImage('miel.jpg');

            $manager->persist($product);
        }
        
        
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->flush();
    }
}
