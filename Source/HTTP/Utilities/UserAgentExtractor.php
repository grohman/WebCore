<?php
namespace WebCore\HTTP\Utilities;


use Traitor\TStaticClass;
use WebCore\IWebRequest;


class UserAgentExtractor
{
	use TStaticClass;

	
	public static function get(IWebRequest $request, ?string $default): ?string 
	{
		if ($request->hasHeader('User-Agent'))
			return $request->getHeader('User-Agent');
		
		if ($request->hasHeader('User_Agent'))
			return $request->getHeader('User_Agent');
		
		return $default;
	}
}