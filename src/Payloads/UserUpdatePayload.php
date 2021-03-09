<?php

namespace ProjectName\Payloads;

interface UserUpdatePayload
{
    public function email(): ?string;

    public function password(): ?string;

    public function firstName(): ?string;

    public function lastname(): ?string;
}
