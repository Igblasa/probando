<?php

namespace App\Twig;

use App\Services\Varios;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $varios;

    public function __construct(Varios $varios)
    {
        $this->varios = $varios;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('generate_token', [$this, 'generateToken']),
        ];
    }

    public function generateToken(): string
    {
        return $this->varios->generateToken();
    }
}
