<?php

namespace UCS\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JsonSchemaException extends ContentValidatorException
{
    /**
     * @param string[] $errors
     *
     * @return JsonSchemaException
     */
    public static function fromValidationErrors(array $errors)
    {
        $message = 'The validated document contains validation errors:';

        foreach ($errors as $error) {
            $message .= PHP_EOL.'  - '.$error;
        }

        return new JsonSchemaException($message);
    }
}