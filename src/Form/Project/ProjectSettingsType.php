<?php

namespace App\Form\Project;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectSettingsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'project_settings.form.field.name.label',
				'attr' => ['placeholder' => 'project_settings.form.field.name.placeholder', 'class' => 'medium'],
			])
			->add('url', UrlType::class, [
				'label' => 'project_settings.form.field.url.label',
				'attr' => ['placeholder' => 'project_settings.form.field.url.placeholder', 'class' => 'medium'],
			])
			->add('save', SubmitType::class, [
				'label' => 'project_settings.form.submit_label',
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Project::class,
		]);
	}
}
