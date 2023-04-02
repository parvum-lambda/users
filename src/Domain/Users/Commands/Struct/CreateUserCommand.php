<?php

namespace Domain\Users\Commands\Struct;

use Support\CQRS\Interfaces\DataSet;

readonly class CreateUserCommand implements DataSet
{
    public function __construct(private string $name, private string $email, private string $document)
    {
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getDocument() : string
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    public function getData() : array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'document' => $this->document,
        ];
    }
}
