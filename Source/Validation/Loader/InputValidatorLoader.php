<?php
namespace WebCore\Validation\Loader;


use Narrator\INarrator;
use Skeleton\Base\ISkeletonSource;
use WebCore\Base\Validation\IInputValidator;
use WebCore\Validation\InputValidator;


class InputValidatorLoader
{
	/** @var ISkeletonSource */
	private $skeleton;
	
	/** @var INarrator */
	private $narrator;
	
	
	/**
	 * @param ISkeletonSource|null $skeleton
	 * @param INarrator $narrator
	 */
	public function __construct(INarrator $narrator, ?ISkeletonSource $skeleton = null)
	{
		$this->skeleton = $skeleton;
		$this->narrator = $narrator;
	}


	public function get(\ReflectionParameter $p)
	{
		$class = get_param_class($p);
		
		/** @var IInputValidator $item */
		
		if (!$this->skeleton)
		{
			$item = $class->newInstance();
		}
		else if ($class->isInterface())
		{
			$item = $this->skeleton->get($p->getType()->getName());
		}
		else 
		{
			$item = $this->narrator->invokeCreateInstance($class);
			$this->skeleton->load($item);
		}
		
		if ($item instanceof InputValidator)
		{
			$item->setNarrator($this->narrator);
		}
		
		return $item;
	}
	
	
	public static function register(INarrator $narrator, ?ISkeletonSource $skeletonSource = null): void
	{
		$narrator->params()->bySubType(
			IInputValidator::class, 
			[new InputValidatorLoader($narrator, $skeletonSource), 'get']
		);
	}
}