<?php
namespace WebCore\HTTP\Utilities;


use PHPUnit\Framework\TestCase;
use WebCore\HTTP\Requests\StandardWebRequest;


class UserIPExtractorTest extends TestCase
{
	protected function setUp()
	{
		$_SERVER = [];
		resetStaticDataMember(HeadersLoader::class, 'headers');
	}
	
	
	public function test_get_IpInClientIpHeader_ReturnClientIp()
	{
		$_SERVER['HTTP_CLIENT_IP'] = '1.1.1.1';
		$request = new StandardWebRequest();
		$request->getHeaders();
		
		self::assertEquals('1.1.1.1', UserIPExtractor::get($request, '2.2.2.2'));
	}
	
	public function test_get_IpInXForwardedForHeader_ReturnFirstIp()
	{
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.1, 2.2.2.2';
		$request = new StandardWebRequest();
		$request->getHeaders();
		
		self::assertEquals('1.1.1.1', UserIPExtractor::get($request, '3.3.3.3'));
	}
	
	public function test_get_SingleIpInXForwardedForHeader_ReturnIp()
	{
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.1';
		$request = new StandardWebRequest();
		$request->getHeaders();
		
		self::assertEquals('1.1.1.1', UserIPExtractor::get($request, '2.2.2.2'));
	}
	
	public function test_get_IpInRemoteAddrHeader_ReturnRemoteAddrIp()
	{
		$_SERVER['REMOTE_ADDR'] = '1.1.1.1';
		$request = new StandardWebRequest();
		$request->getHeaders();
		
		self::assertEquals('1.1.1.1', UserIPExtractor::get($request, '2.2.2.2'));
	}
	
	public function test_get_NoIp_ReturnDefault()
	{
		$request = new StandardWebRequest();
		$request->getHeaders();
		
		self::assertEquals('2.2.2.2', UserIPExtractor::get($request, '2.2.2.2'));
	}
	
	
	public static function tearDownAfterClass()
	{
		$_SERVER = [];
	}
}