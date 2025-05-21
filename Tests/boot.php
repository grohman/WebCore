<?php
require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ERROR | E_PARSE | E_ALL);


function resetStaticDataMember(string $className, string $memberName)
{
	$reflectionClass = new ReflectionClass($className);
	$property = $reflectionClass->getProperty($memberName);
	/** @noinspection PhpExpressionResultUnusedInspection */
	$property->setAccessible(true);
	$property->setValue($reflectionClass, null);
}