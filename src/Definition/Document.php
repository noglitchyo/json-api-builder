<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use InvalidArgumentException;
use JsonSerializable;

class Document implements DocumentInterface
{
    /**
     * @var array|null
     */
    private $meta = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $included = [];

    public function __construct($meta = [], $errors = [], $data = [])
    {
        $this->meta = $meta;
        $this->errors = $errors;
        $this->data = $data;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getIncluded(): array
    {
        return $this->included;
    }


    public function withMeta($meta): DocumentInterface
    {
        $document = clone $this;
        $document->meta = $meta;

        return $document;
    }

    public function withErrors(array $errors): DocumentInterface
    {
        $document = clone $this;
        $document->errors = $errors;

        return $document;
    }

    public function withData($data): DocumentInterface
    {
        if (is_object($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException('Must be an instance of ' . JsonSerializable::class);
        }

        $document = clone $this;
        $document->data = $data;

        return $document;
    }

    public function withIncluded($included): DocumentInterface
    {
        $document = clone $this;
        $document->included = $included;

        return $document;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        $response = [
            'meta' => $this->meta,
            'errors' => $this->errors,
            'data' => $this->data,
        ];

        if ($this->included) {
            $response['included'] = $this->included;
        }

        return $response;
    }
}
