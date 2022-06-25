<?php

namespace InsightMedia\StatamicGoogleAnalytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function credentialsJsonDoesNotExist(string $path): static
    {
        return new static("Could not find a credentials file at `{$path}`.");
    }

    public static function propertyIdDoesNotExist(): static
    {
        return new static("No property ID found.");
    }
}
