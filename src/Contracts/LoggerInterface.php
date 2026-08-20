<?php

declare(strict_types=1);

namespace OpinerMe\Contracts;

defined( 'ABSPATH' ) || exit;

interface LoggerInterface {

    public static function info( string $message ): void;
    public static function warning( string $message ): void;
    public static function error( string $message ): void;
    public static function debug( string $message ): void;
    public static function critical( string $message ): void;
    public static function alert( string $message ): void;
    public static function emergency( string $message ): void;
    public static function notice( string $message ): void;
}
