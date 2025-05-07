<?php
namespace WebCore;


use Structura\URL;
use WebCore\Base\HTTP\IRequestFiles;


interface IWebRequest
{
	public function getMethod(): string;
	public function isMethod(string $method): bool;
	public function isGet(): bool;
	public function isPost(): bool;
	public function isPut(): bool;
	public function isDelete(): bool;
	// public function isHead(): bool;
	// public function isPatch(): bool;
	// public function isOptions(): bool;
	// public function isTrace(): bool;
	
	public function isHttp(): bool;
	public function isHttps(): bool;
	public function getPort(): ?int;
	public function getHost(): string;
	public function getIP(): string;
	public function getURI(): string;
	public function getURL(): string;
	public function getPath(): string;
	
	public function getURLObject(): URL;
	
	
	public function getUserAgent(?string $default = null): ?string;
	
	public function getHeaders(bool $caseSensitive = false): IInput;
	public function getHeadersArray(bool $caseSensitive = false): array;
	public function getHeader(string $header, ?string $default = null, bool $caseSensitive = false): ?string;
	public function hasHeader(string $header, bool $caseSensitive = false): bool;
	
	public function getCookies(): IInput;
	public function getCookiesArray(): array;
	public function getCookie(string $cookie, ?string $default = null): ?string;
	public function hasCookie(string $cookie): bool;
	
	public function getParams(): IInput;
	public function getParamsArray(): array;
	public function getParam(string $param, ?string $default = null): ?string;
	public function hasParam(string $param): bool;
	
	public function getQuery(): IInput;
	public function getQueryArray(): array;
	public function getQueryParam(string $param, ?string $default = null): ?string;
	public function hasQueryParam(string $param): bool;
	
	public function getRequestParams(): IInput;
	public function getRequestParamsArray(): array;
	public function getRequestParam(string $param, ?string $default = null): ?string;
	public function hasRequestParam(string $param): bool;
	
	public function setRouteParams(array $params): void;
	public function getRouteParams(): IInput;
	public function getRouteParamsArray(): array;
	public function getRouteParam(string $param, ?string $default = null): ?string;
	public function hasRouteParam(string $param): bool;
	
	public function getPost(): IInput;
	public function getPostArray(): array;
	public function getPostParam(string $param, ?string $default = null): ?string;
	public function hasPostParam(string $param): bool;
	
	public function files(): ?IRequestFiles;
	public function hasFiles(): bool;
	
	public function getBody(): string;
	public function getJson(): array;
}