<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Model\ErrorDebugDetails;
use App\Model\ErrorValidationDetails;

class ErrorResponse
{
    public function __construct(private readonly string $message, private readonly mixed $details = null)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @OA\Property(
     *     type="object",
     *     oneOf={
     *
     *          @OA\Schema(ref=@Model(type=ErrorDebugDetails::class)),
     *          @OA\Schema(ref=@Model(type=ErrorValidationDetails::class)),
     *     }
     * )
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }
}
