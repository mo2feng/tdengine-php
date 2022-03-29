<?php

use Mofengme\Tdengine\TdEngine;
use Mofengme\Tdengine\TdEngineException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TdEngineTest extends TestCase
{

    public function testBuildUri()
    {
        $method = new ReflectionMethod(TdEngine::class, 'buildUri');
        $method->setAccessible(true);

        $result = $method->invoke(new TdEngine("mock-key"));
        $this->assertEquals("http://127.0.0.1:6041", $result);
    }

    public function testGetHttpClient()
    {
        $t = new TdEngine("mock-key");

        $this->assertInstanceOf(HttpClientInterface::class, $t->getHttpClient());
    }

    public function testExecuteSomethingWithError()
    {
        $errorResponse = new MockResponse('{"status":"error","code":534,"desc":"syntax error near \'Incomplete SQL statement\'"}');
        $client        = new MockHttpClient($errorResponse);
        $t             = new TdEngine("mock-key");
        $this->expectException(TdEngineException::class);
        $this->expectExceptionMessage('syntax error near \'Incomplete SQL statement\'');
        $t->raw_query($client, 'show tablesss');

    }

    public function testExecuteSomethingWithSuccess()
    {
        $response = new MockResponse('{"status":"succ","head":["name","created_time","columns","tags","tables"],"column_meta":[["name",8,193],["created_time",9,8],["columns",3,2],["tags",3,2],["tables",4,4]],"data":[["switch_status","2022-03-26 09:41:11.040",36,2,1]],"rows":1}');
        $client   = new MockHttpClient($response);
        $t        = new TdEngine("mock-key");
        $result   = $t->raw_query($client, 'show stables');
        $this->AssertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('created_time', $result[0]);
        $this->assertArrayHasKey('columns', $result[0]);
        $this->assertArrayHasKey('tags', $result[0]);
        $this->assertArrayHasKey('tables', $result[0]);
    }

}
