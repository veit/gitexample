<?php

interface RendererInterface
{
    public function render(string $message): string;
}

trait LoggerTrait
{
    protected function log(bool $text)
    {
        file_put_contents('hello.log', "[" . date('c') . "] " . $text . PHP_EOL, FILE_APPEND);
    }
}

class HtmlRenderer implements RendererInterface
{
    use LoggerTrait;

    public function render(string $message): string
    {
        $this->log("Rendering message: {$message}");
        return "<html><body><h1>{$message}</h1></body></html>";
    }
}

class HelloConfig
{
    private string $greeting;
    private string $target;

    public function __construct(string $greeting = "Hello", string $target = "World")
    {
        $this->greeting = $greeting;
        $this->target = $target;
    }

    public function getMessage(): string
    {
        return "{$this->greeting}, {$this->target}!";
    }
}

class HelloException extends Exception {}

class HelloService
{
    private RendererInterface $renderer;
    private HelloConfig $config;

    public function __construct(RendererInterface $renderer, HelloConfig $config)
    {
        $this->renderer = $renderer;
        $this->config = $config;
    }

    public function run(): string
    {
        $message = $this->config->getMessage();

        if (empty($message)) {
            throw new HelloException("Message cannot be empty!");
        }

        return $this->renderer->render($message);
    }
}

$renderer = new HtmlRenderer();
$config   = new HelloConfig("Hello", "Over-Engineered PHP World");
$service  = new HelloService($renderer, $config);

try {
    echo $service->run();
} catch (HelloException $e) {
    echo "Error: " . $e->getMessage();
}
