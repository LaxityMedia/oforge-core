<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Slim\Router;

class RegistrationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => User::class]);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(string $email, string $password) {
        $user = null;

        if (!$this->userExists($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);
            $this->entityManager()->persist($user);
            $this->entityManager()->flush();

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }

        return $user;
    }

    /**
     * @param array $user
     *
     * @return string
     */
    public function generateActivationLink(array $user) :string {
        /** @var Router $router */
        $activationLink = 'http://';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $activationLink = 'https://';
        }
        $activationLink .= $_SERVER['HTTP_HOST'];
        $router         = Oforge()->App()->getContainer()->get("router");
        $user           = $this->repository()->findOneBy(['email' => $user['email']]);
        $activationLink .= $router->pathFor('frontend_registration_activate') . '?activate=' . $user->getGuid();

        return $activationLink;
    }

    /**
     * @param string $guid
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function activate(string $guid) {
        /** @var User $user */
        $user = $this->repository()->findOneBy(['guid' => $guid]);
        if ($user) {
            $user->setActive(true);
            $this->entityManager()->persist($user);
            $this->entityManager()->flush();

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }
        return $user;

    }

    private function userExists(string $email) {
        return $this->repository()->findBy(['email' => $email]);
    }
}