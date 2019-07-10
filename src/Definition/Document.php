<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use InvalidArgumentException;
use const JSON_THROW_ON_ERROR;
use JsonSerializable;

class Document implements DocumentInterface
{
    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array|JsonSerializable
     */
    private $data;

    /**
     * @var array
     */
    private $included = [];

    public function __construct($meta = [], $errors = [], $data = [])
    {
        $this->meta   = $meta;
        $this->errors = $errors;
        $this->data   = $data;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array|JsonSerializable
     */
    public function getData()
    {
        return $this->data;
    }

    public function getIncluded(): array
    {
        return $this->included;
    }

    public function withMeta($meta): DocumentInterface
    {
        $document       = clone $this;
        $document->meta = $meta;

        return $document;
    }

    public function withErrors(array $errors): DocumentInterface
    {
        $document         = clone $this;
        $document->errors = $errors;

        return $document;
    }

    /**
     * @param array|JsonSerializable $data
     *
     * @return DocumentInterface
     */
    public function withData($data): DocumentInterface
    {
        if (!is_array($data)) {
            if (!$data instanceof JsonSerializable) { // phpstan does not understand when on the same line
                throw new InvalidArgumentException('Must be an instance of ' . JsonSerializable::class);
            }
        }

        $document       = clone $this;
        $document->data = $data;

        return $document;
    }

    public function withIncluded(array $included): DocumentInterface
    {
        $document           = clone $this;
        $document->included = $included;

        return $document;
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        $document = [
            'meta' => $this->meta,
            'errors' => $this->errors,
            'data' => $this->data,
        ];

        if ($this->included) {
            $document['included'] = $this->included;
        }

        return $document;
    }
}
