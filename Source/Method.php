<?php
namespace WebCore;


use Traitor\TEnum;


class Method
{
	use TEnum;
	
	
	const GET 		= 'GET';
	const HEAD 		= 'HEAD';
	const POST 		= 'POST';
	const PUT 		= 'PUT';
	const DELETE 	= 'DELETE';
	const PATCH		= 'PATCH';
	const OPTIONS 	= 'OPTIONS';
	const CONNECT	= 'CONNECT';
	const TRACE		= 'TRACE';
	const UNKNOWN 	= 'UNKNOWN';
}