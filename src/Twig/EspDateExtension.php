<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EspDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('esp_date', [$this, 'formatEspDate']),
        ];
    }

    public function formatEspDate(\DateTimeInterface $date, string $format = '%a %d %b'): string
    {
        setlocale(LC_TIME, 'es_AR.utf8');
        // Usa strftime con el formato en estilo PHP:
        return strftime($format, $date->getTimestamp());
    }
}