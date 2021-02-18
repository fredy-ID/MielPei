<?php

namespace App\DataFixtures;

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
        $user2 = new User();
        $user2->setEmail('email2@email2.fr');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword('$argon2id$v=19$m=65536,t=4,p=1$SlJXN3U3blUvblN3ZHBLSw$GUACfbBaR3VQb6b22fQmlhBdelMDMss+7zZWd+vjGiU');
        $manager->persist($user2); 

        $user = new User();
        $user->setEmail('email@email.fr');
        $user->setRoles(['ROLE_USER','ROLE_PRODUCER']);
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$SlJXN3U3blUvblN3ZHBLSw$GUACfbBaR3VQb6b22fQmlhBdelMDMss+7zZWd+vjGiU');
        $manager->persist($user);

        $producer = new Producer();
        $producer->setUser($user);
        $producer->setIntroduction('Un excellent producteur de viande au miel !');
        $manager->persist($producer);

        $manager->flush();

        for ($i=0; $i < 7; $i++) { 
            $product = new Product();
            $product->setName('Miel Mouche Charbon' . $i);
            $product->setDescription('mouche charbon de saint leu de mafate et de salazie sortie du guatemala monté en chine');
            $product->setPrice(1 . $i);
            $product->setNumberOfPurchases($i);
            $product->setOwner($producer);
            $product->setProductionArea('mafate');
            $product->setImage('miel.jpg');

            $manager->persist($product);
        }
        
        
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->flush();
    }
}
