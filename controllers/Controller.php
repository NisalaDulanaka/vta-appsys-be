<?php
require_once('./traits/terminal.php');

class Controller
{
    use Terminal;

    /**
     * Generates a view (UI)
     * @param string $path the path of the view file. This can be relative to the views folder or the project root
     * @param array $data Data to be passed to the view for templating
     * @param bool $manualPath Indicates whether the path is relative to the views folder or the project root
     */
    public function view(string $path, array $data = [], bool $manualPath = false): void
    {

        foreach ($data as $key => $value) {
            ${$key} = $value;
        }

        if (! $manualPath) $path = "./views/$path";

        require($path);
    }

    /**
     * Sets the unauthorized response codes and sends a message
     * @param string $message The message to be shown in the response
     */
    public function Unauthorized(string $message = "Unauthorized access"): void
    {
        http_response_code(401);

        // Set content type to JSON or HTML as needed
        if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json') {
            header('Content-Type: application/json');
        }

        echo json_encode(['error' => $message]);

        // Terminate the script
        exit();
    }

    /**
     * Sets the not found response codes and sends a message
     * @param string $message The message to be shown in the response
     */
    public function NotFound(string $message = "Resource not found"): void
    {
        http_response_code(404);

        // Set content type to JSON or HTML as needed
        if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json') {
            header('Content-Type: application/json');
        }

        echo json_encode(['error' => $message]);

        // Terminate the script
        exit();
    }

    /**
     * Converts an associative array to a DTO instance
     * @template T
     * @param class-string<T> $className The DTO class name
     * @param array $data The associative array
     * @return T | null
     */
    public function mapToDto(string $className, array $data): object | null
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        $params = $constructor->getParameters();

        $args = [];
        foreach ($params as $param) {
            $name = $param->getName();
            $type = $param->getType();

            // Handle type casting (optional)
            if ($type && !$param->isOptional() && isset($data[$name])) {
                settype($data[$name], $type->getName());
            } else {
                return null;
            }

            $args[] = $data[$name] ?? null;
        }

        return new $className(...$args);
    }
}
