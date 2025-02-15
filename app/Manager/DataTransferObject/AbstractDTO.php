<?php

namespace App\Manager\DataTransferObject;

abstract class AbstractDTO {

    public function fill(): array {
        return get_object_vars($this);
    }

}
