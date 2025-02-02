<?php

namespace App\Serializer;

use App\Entity\Project;
use App\Storage\ProjectStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

/**
 * Adds favicon and thumbnail URLs to the data using the `ProjectStorage`.
 */
class ProjectNormalizer implements ContextAwareNormalizerInterface
{
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function __construct(
		private ContainerInterface $container,
		private ProjectStorage $projectStorage
	) {
	}

	/**
	 * @param Project $project
	 */
	public function normalize($project, string $format = null, array $context = [])
	{
		$data = $this->container->get('DefaultNormalizer')->normalize($project, $format, $context);

		if (is_array($data)) {
			// Add favicon and thumbnail URLs
			$data['faviconUrl'] = $this->projectStorage->faviconUrl($project);
			$data['thumbnailUrl'] = $this->projectStorage->thumbnailUrl($project);
			$data['status'] = $project->getStatus();
		}

		return $data;
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function supportsNormalization($data, string $format = null, array $context = [])
	{
		return $data instanceof Project;
	}
}
