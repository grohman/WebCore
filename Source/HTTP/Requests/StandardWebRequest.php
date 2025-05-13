<?php
namespace WebCore\HTTP\Requests;


use WebCore\IInput;
use WebCore\Method;
use WebCore\IWebRequest;
use WebCore\HTTP\Utilities;
use WebCore\Base\HTTP\IRequestFiles;
use WebCore\Inputs\FromArray;
use WebCore\Exception\WebCoreFatalException;

use Structura\URL;


/**
 * @deprecated Use \WebCore\WebRequest 
 */
class StandardWebRequest implements IWebRequest
{
	private static $current = null;
	
	
	private $isHttps		= null;
	private $method			= null;
	private $params			= null;
	private $body			= null;
	private $requestParams 	= null;
	private $routeParams	= [];
	
	
	public function isMethod(string $method): bool 
	{ 
		return $this->getMethod() == $method; 
	}
	
	public function isGet(): bool 
	{ 
		return $this->getMethod() == Method::GET; 
	}
	
	public function isPost(): bool 
	{ 
		return $this->getMethod() == Method::POST; 
	}
	
	public function isPut(): bool 
	{ 
		return $this->getMethod() == Method::PUT; 
	}
	
	public function isDelete(): bool 
	{ 
		return $this->getMethod() == Method::DELETE; 
	}

	public function isHead(): bool
	{
		return $this->getMethod() == Method::HEAD;
	}

	public function isPatch(): bool
	{
		return $this->getMethod() == Method::PATCH;
	}

	public function isOptions(): bool
	{
		return $this->getMethod() == Method::OPTIONS;
	}

	public function isTrace(): bool
	{
		return $this->getMethod() == Method::TRACE;
	}
	
	public function isHttp(): bool 
	{ 
		return !$this->isHttps(); 
	}
	
	
	public function getHeaders(bool $caseSensitive = false): IInput 
	{ 
		return new FromArray($this->getHeadersArray($caseSensitive)); 
	}
	
	public function getCookies(): IInput 
	{ 
		return new FromArray($this->getCookiesArray()); 
	}
	
	public function getCookiesArray(): array 
	{ 
		return $_COOKIE;	
	}
	
	public function getCookie(string $cookie, ?string $default = null): ?string 
	{ 
		return $this->getCookies()->string($cookie, $default); 
	}
	
	public function hasCookie(string $cookie): bool 
	{ 
		return $this->getCookies()->has($cookie); 
	}
	
	public function getParams(): IInput 
	{ 
		return new FromArray($this->getParamsArray()); 
	}
	
	public function getParam(string $param, ?string $default = null): ?string 
	{ 
		return $this->getParams()->string($param, $default); 
	}
	
	public function hasParam(string $param): bool 
	{ 
		return $this->getParams()->has($param); 
	}
	
	public function getQuery(): IInput 
	{ 
		return new FromArray($this->getQueryArray()); 
	}
	
	public function getQueryArray(): array 
	{ 
		return $_GET; 
	}
	
	public function getQueryParam(string $param, ?string $default = null): ?string 
	{ 
		return $this->getQuery()->string($param, $default); 
	}
	
	public function hasQueryParam(string $param): bool 
	{ 
		return $this->getQuery()->has($param); 
	} 
	
	public function getPost(): IInput 
	{ 
		return new FromArray($this->getPostArray()); 
	}
	
	public function getPostArray(): array 
	{ 
		return $_POST; 
	}
	
	public function getPostParam(string $param, ?string $default = null): ?string 
	{ 
		return $this->getPost()->string($param, $default); 
	}
	
	public function hasPostParam(string $param): bool 
	{ 
		return $this->getPost()->has($param); 
	}
	
	
	public function getMethod(): string
	{
		if (is_null($this->method))
			$this->method = $_SERVER['REQUEST_METHOD'] ?? Method::UNKNOWN;
		
		return $this->method;
	}
	
	public function isHttps(): bool
	{
		if (is_null($this->isHttps))
			$this->isHttps = Utilities::isHTTPSRequest();
		
		return $this->isHttps;
	}
	
	public function getUserAgent(?string $default = null): ?string
	{
		return Utilities\UserAgentExtractor::get($this, $default);
	}
	
	public function getHeader(string $header, ?string $default = null, bool $caseSensitive = false): ?string
	{
		if (!$caseSensitive)
			$header = Utilities::getGenericHeaderName($header);
		
		$headers = Utilities::getAllHeaders($caseSensitive);
		return $headers[$header] ?? $default;
	}
	
	public function hasHeader(string $header, bool $caseSensitive = false): bool
	{
		if (!$caseSensitive)
			$header = Utilities::getGenericHeaderName($header);
		
		return key_exists($header, Utilities::getAllHeaders($caseSensitive));
	}
	
	public function getHeadersArray(bool $caseSensitive = false): array
	{
		return Utilities::getAllHeaders($caseSensitive);
	}
	
	public function getParamsArray(): array
	{
		if (is_null($this->params))
		{
			switch ($this->getMethod())
			{
				case Method::POST:
					if (str_contains($this->getHeader('Content-Type'), 'application/json'))
					{
						$this->params = $this->getJson();
					}
					else
					{
						$this->params = self::getPostArray();
					}
					
					break;
					
				case Method::PUT:
					parse_str($this->getBody(), $this->params);
					break;
					
				case Method::GET:
				case Method::OPTIONS:
				case Method::HEAD:
				case Method::DELETE:
				default:
					$this->params = $this->getQueryArray();
					break;
			}
		}
		
		return $this->params;
	}
	
	public function getRequestParams(): IInput 
	{
		return new FromArray($this->getRequestParamsArray());
	}
	
	public function getRequestParamsArray(): array 
	{
		if (is_null($this->requestParams))
		{
			$this->requestParams = array_merge($this->getPostArray(), $this->getQueryArray(), $this->getRouteParamsArray());
		}
		
		return $this->requestParams;
	}
	
	public function getRequestParam(string $param, ?string $default = null): ?string 
	{ 
		return $this->getRequestParams()->string($param, $default);
	}
	
	public function hasRequestParam(string $param): bool 
	{ 
		return $this->getRequestParams()->has($param); 
	}
	
	
	public function setRouteParams(array $params): void
	{
		$this->routeParams = $params;
	}
	
	public function getRouteParams(): IInput
	{
		return new FromArray($this->getRouteParamsArray());
	}
	
	public function getRouteParamsArray(): array
	{
		return $this->routeParams;
	}
	
	public function getRouteParam(string $param, ?string $default = null): ?string
	{
		return $this->getRouteParams()->string($param, $default);
	}
	
	public function hasRouteParam(string $param): bool
	{
		return $this->getRouteParams()->has($param);
	}
	
	
	public function getPort(): ?int
	{
		if (!isset($_SERVER['SERVER_PORT']))
			return null;
		
		return (int)$_SERVER['SERVER_PORT'];
	}
	
	public function getHost(): string
	{
		return $_SERVER['HTTP_HOST'] ?? '';
	}
	
	public function getIP(?string $default = null): string
	{
		return Utilities\UserIPExtractor::get($this, $default) ?: '';
	}
	
	public function getURI(): string
	{
		return $_SERVER['REQUEST_URI'] ?? '';
	}
	
	public function getURL(): string
	{
		$protocol = $this->isHttp() ? 'http' : 'https';
		return "{$protocol}://" . $this->getHost() . $this->getURI();
	}
	
	public function getPath(): string
	{
		return explode('?', explode('#', $this->getURI(), 2)[0], 2)[0];
	}
	
	public function getURLObject(): URL
	{
		return new URL($this->getURL());
	}
	
	
	public function files(): ?IRequestFiles
	{
		// TODO:
		return null;
	}
	
	public function hasFiles(): bool
	{
		// TODO:
		return false;
	}
	
	public function getBody(): string
	{
		if (is_null($this->body)) 
		{
			if (!defined('STDIN'))
				$source = fopen('php://input', 'r');
			else
				$source = STDIN;
			
			$this->body = stream_get_contents($source);
		}
		
		return $this->body;
	}
	
	public function getJson(): array
	{
		$body = $this->getBody();
		$json = jsondecode($body, JSON_OBJECT_AS_ARRAY);
		
		if (json_last_error() != 0)
			throw new WebCoreFatalException('Request body is not a valid json');
		
		return $json;
	}
	
	
	public static function current(): StandardWebRequest
	{
		if (!self::$current)
			self::$current = new static();
		
		return self::$current;
	}
}