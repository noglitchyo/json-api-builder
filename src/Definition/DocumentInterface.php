<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use JsonSerializable;

/**
 * Representation of a JSON:API document as described in specification.
 *
 * @see     https://jsonapi.org/format/1.1/
 * @version 1.1
 */
interface DocumentInterface extends JsonSerializable
{
    public function getMeta(): array;

    /**
     * Return an instance with the specified meta.
     *
     * Can be used to include non-standard meta-information.
     * Each meta member MUST be an object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the document, and MUST return an instance that has the
     * updated meta member.
     */
    public function withMeta($meta): self;

    public function getErrors(): array;

    /**
     * Return an instance with the specified errors.
     *
     * Error objects provide additional information about problems encountered while performing an operation. Error
     * objects MUST be returned as an array keyed by errors in the top level of a JSON:API document.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the document, and MUST return an instance that has the
     * updated errors member.
     */
    public function withErrors(array $errors): self;

    public function getData(): array;

    /**
     * Return an instance with the specified data.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the document, and MUST return an instance that has the
     * updated data member.
     */
    public function withData($data): self;

    public function getIncluded(): array;

    /**
     * Return an instance with the specified included resources.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the document, and MUST return an instance that has the
     * updated included member.
     */
    public function withIncluded($included): self;
}
