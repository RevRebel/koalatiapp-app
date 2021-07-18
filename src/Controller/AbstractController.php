<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as DefaultAbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends DefaultAbstractController
{
	protected TranslatorInterface $translator;

	/**
	 * @required
	 */
	public function setTranslator(TranslatorInterface $translator): void
	{
		$this->translator = $translator;
	}

	/**
	 * Get a user from the Security Token Storage.
	 *
	 * @return User|object|null
	 *
	 * @throws \LogicException If SecurityBundle is not available
	 *
	 * @see TokenInterface::getUser()
	 */
	protected function getUser()
	{
		return parent::getUser();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string                    $message
	 * @param array<string,string>|null $messageParams
	 */
	protected function addFlash(string $type, $message, ?array $messageParams = []): void
	{
		parent::addFlash($type, $this->translator->trans($message, $messageParams));
	}
}
