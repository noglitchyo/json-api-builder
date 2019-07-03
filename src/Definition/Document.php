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
    private $errors = [];
    private $data  = [];
    private $included;

    public function __construct($meta = [], $errors = [], $data = [])
    {
        $this->meta = $meta;
        $this->errors = $errors;
        $this->data = $data;
    }

    public function withMeta($meta): DocumentInterface
    {
        $response = clone $this;
        $response->meta = $meta;

        return $response;
    }

    public function withErrors(array $errors): DocumentInterface
    {
        $response = clone $this;
        $response->errors = $errors;

        return $response;
    }

    public function withData($data): DocumentInterface
    {
        if (is_object($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException('Must be an instance of ' . JsonSerializable::class);
        }

        $response = clone $this;
        $response->data = $data;

        return $response;
    }

    public function withIncluded($included): DocumentInterface
    {
        $response = clone $this;
        $response->included = $included;

        return $response;
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
