<?php

declare(strict_types=1);

namespace League\Flysystem;

interface FilesystemOperationFailed extends FilesystemException
{
    public const OPERATION_WRITE = 'WRITE';
    public const OPERATION_UPDATE = 'UPDATE'; // not used
    public const OPERATION_EXISTENCE_CHECK = 'EXISTENCE_CHECK';
    public const OPERATION_DIRECTORY_EXISTS = 'DIRECTORY_EXISTS';
    public const OPERATION_FILE_EXISTS = 'FILE_EXISTS';
    public const OPERATION_CREATE_DIRECTORY = 'CREATE_DIRECTORY';
    public const OPERATION_DELETE = 'DELETE';
    public const OPERATION_DELETE_DIRECTORY = 'DELETE_DIRECTORY';
    public const OPERATION_MOVE = 'MOVE';
    public const OPERATION_RETRIEVE_METADATA = 'RETRIEVE_METADATA';
    public const OPERATION_COPY = 'COPY';
    public const OPERATION_READ = 'READ';
    public const OPERATION_SET_VISIBILITY = 'SET_VISIBILITY';
    public const OPERATION_LIST_CONTENTS = 'LIST_CONTENTS';

    public function operation(): string;
}
