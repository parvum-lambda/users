<?php

namespace Domain\Users\Queries\Struct;

use Support\CQRS\Interfaces\DataSet;

readonly class GetUserQuery implements DataSet
{
    public function __construct(private string $id)
    {
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    public function getData() : array
    {
        return [
            'id' => $this->id,
        ];
    }
}
