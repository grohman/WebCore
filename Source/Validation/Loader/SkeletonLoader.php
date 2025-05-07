<?php
namespace WebCore\Validation\Loader;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Exceptions\ImplementerNotDefinedException;

use WebCore\IInput;
use Narrator\INarrator;


class SkeletonLoader
{
	/** @var ISkeletonSource */
	private $skeleton;
	
	
	public function __construct(ISkeletonSource $request)
	{
		$this->skeleton = $request;
	}
	
	
	public function get(\ReflectionParameter $p, bool &$isFound): ?IInput
	{
		if (get_param_class($p))
		{
			$isFound = false;
			return null;
		}
		
		try
		{
			$isFound = true;
			return $this->skeleton->get(get_param_class($p));
		}
		catch (ImplementerNotDefinedException $e)
		{
			$isFound = false;
			return null;
		}
	}
	
	
	public static function register(INarrator $narrator, ISkeletonSource $skeleton): void
	{
		$narrator->params()->addCallback([new SkeletonLoader($skeleton), 'get']);
	}
}