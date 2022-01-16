<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\Player;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $user = new User();
	$user
            ->setFirstName('kareem')
            ->setLastName('shareef')
            ->setPassword('$2y$13$f3sEat7ahUFK.4yvWImeYugL7D2xUBgJCyVYkOSMiuXMUH/Ndfetq')
            ->setEmail('kareem@gmail.com')
            ->setRoles(['ROLE_ADMIN'])->onPrePersist();
        $manager->persist($user);
        $manager->flush();


        $team = new Team();
        $team->setName('AFC AJAX');
        $team->setLogo('https://logo.clearbit.com/ajax.nl');
        $manager->persist($team);
        $manager->flush();

        // store reference to Ajax team for player relation
        $this->addReference('AJAX-TEAM', $team);

        $team = new Team();
        $team->setName('Go Ahead Eagles');
        $team->setLogo('https://upload.wikimedia.org/wikipedia/en/3/3c/Go_Ahead_Eagles.png');
        $manager->persist($team);
        $manager->flush();

        // store reference to Eagles team for player relation
        $this->addReference('EAGLES-TEAM', $team);


        $manager->flush();
	$player = new Player();
        $player->setFirstName('Hakim');
        $player->setLastName('Ziyech');
        $player->setTeam($this->getReference('AJAX-TEAM')); // load the stored reference
        $player->setImageURI('https://www.vtbl.nl/sites/default/files/styles/liggend/public/content/images/2019/06/16/ANP-58099208.jpg?itok=0YmHF_rX');
        $manager->persist($player);
        $manager->flush();

        $player = new Player();
        $player->setFirstName('David');
        $player->setLastName('Neres');
        $player->setTeam($this->getReference('AJAX-TEAM')); // load the stored reference
        $player->setImageURI('https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/David_Neres_2017.jpg/250px-David_Neres_2017.jpg');
        $manager->persist($player);
        $manager->flush();

        $player = new Player();
        $player->setFirstName('Thomas');
        $player->setLastName('Verheydt');
        $player->setTeam($this->getReference('EAGLES-TEAM')); // load the stored reference
        $player->setImageURI('https://img.a.transfermarkt.technology/portrait/big/297365-1492675420.jpg?lm=1');
        $manager->persist($player);
        $manager->flush();

        $player = new Player();
        $player->setFirstName('Maarten');
        $player->setLastName('Pouwels');
        $player->setTeam($this->getReference('EAGLES-TEAM')); // load the stored reference
        $player->setImageURI('https://www.dalfsennet.nl/static/img/normal-b/2018/08/maarten2_1534538079.png');
        $manager->persist($player);
        $manager->flush();

    }

/**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
