<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
	use TargetPathTrait;

	public const LOGIN_ROUTE = 'login';

	private UrlGeneratorInterface $urlGenerator;
	private EntityManagerInterface $entityManager;

	public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
	{
		$this->urlGenerator = $urlGenerator;
		$this->entityManager = $entityManager;
	}

	public function authenticate(Request $request): PassportInterface
	{
		$email = $request->request->get('email', '');

		$request->getSession()->set(Security::LAST_USERNAME, $email);

		return new Passport(
			new UserBadge($email),
			new PasswordCredentials($request->request->get('password', '')),
			[
				new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
			]
		);
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		$user = $token->getUser();

		if ($user instanceof User) {
			$user->setDateLastLoggedIn(new DateTime());
			$this->entityManager->persist($user);
			$this->entityManager->flush();
		}
		if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
			if (!str_contains($targetPath, "/api/")) {
				return new RedirectResponse($targetPath);
			}
		}

		return new RedirectResponse($this->urlGenerator->generate('dashboard'));
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function getLoginUrl(Request $request): string
	{
		return $this->urlGenerator->generate(self::LOGIN_ROUTE);
	}
}
