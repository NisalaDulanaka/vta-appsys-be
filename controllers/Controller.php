<?php

use Rakit\Validation\Validator;

class Controller
{
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
     * Validates api request object against a validation schema
     * @param array $data the request data
     * @param string $schemaPath path to validation schema
     * @return array the error messages if the validation fails
     * @return null if the validation is successful
     */
    public function validate(array $data, string $schemaPath): ?array
    {
        [$file, $schemaName]= explode(".", $schemaPath);
        $schemas = require_once("./traits/dto/schemas/$file.php");
        $schema = $schemas[$schemaName];

        $validator = new Validator();
        if (is_callable($schema)) {
            $schema = $schema($validator);
        }

        $validation = $validator->validate($data, $schema);

        if ($validation->fails()) {
            return $validation->errors()->all();
        }
        return null;
    }
}
