<?php

namespace App\Manager\DataTransferObject;

interface InterfaceDTO {

    public static function transform(array $data): self;

}
