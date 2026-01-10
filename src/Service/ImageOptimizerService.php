<?php

declare(strict_types=1);

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;
use Imagine\Image\ImageInterface;

final class ImageOptimizerService
{
    /** Presets utiles (rond en CSS via border-radius:50%). */
    private const PRESETS = [
    'avatar_128'        => ['mode' => 'square', 'w' => 128,  'h' => 128,  'up' => false],
    'avatar_256'        => ['mode' => 'square', 'w' => 256,  'h' => 256,  'up' => false],
    'thumb_256'         => ['mode' => 'square', 'w' => 256,  'h' => 256,  'up' => true],
    'thumb_400'         => ['mode' => 'square', 'w' => 400,  'h' => 400,  'up' => true],
    'banner_1600x900'   => ['mode' => 'cover',  'w' => 1600, 'h' => 900,  'up' => true],
    'og_1200x630'       => ['mode' => 'cover',  'w' => 1200, 'h' => 630,  'up' => true],
    'max_1024'          => ['mode' => 'fit',    'w' => 1024, 'h' => 1024, 'up' => false],
    'max_2048'          => ['mode' => 'fit',    'w' => 2048, 'h' => 2048, 'up' => false],
    'logo_max_600'      => ['mode' => 'contain', 'w' => 600,  'h' => 600,  'up' => false],
    'logo_max_320'      => ['mode' => 'contain', 'w' => 320, 'h' => 320, 'up' => false],
    'logo_max_256'      => ['mode' => 'contain', 'w' => 256, 'h' => 256, 'up' => false],
    // services carrés
    'service_sq_480'        => ['mode' => 'square', 'w' => 480,  'h' => 480,  'up' => true],
    'service_sq_800'        => ['mode' => 'square', 'w' => 800,  'h' => 800,  'up' => true],
    // carousels 16:9
    'service_car_640x360'   => ['mode' => 'cover',  'w' => 640,  'h' => 360,  'up' => true],  // 🔴 NOUVEAU (mobile)
    'service_car_1280x720'  => ['mode' => 'cover',  'w' => 1280, 'h' => 720,  'up' => true],
    'service_car_1920x1080' => ['mode' => 'cover',  'w' => 1920, 'h' => 1080, 'up' => true],
];

    public function __construct(private readonly Imagine $imagine = new Imagine()) {}

    public function optimizePreset(string $filepath, string $preset): string
    {
        $p = self::PRESETS[$preset] ?? null;
        if (!$p) throw new \InvalidArgumentException("Preset inconnu: {$preset}");
        return $this->optimize($filepath, $p['mode'], (int)$p['w'], (int)$p['h'], (bool)$p['up']);
    }

    /**
     * Écrase le fichier source (même nom/extension). AUCUNE sauvegarde/copie.
     * Modes: square|cover|contain|fit
     */
    public function optimize(
        string $filepath,
        string $mode,
        int $targetW,
        int $targetH,
        bool $allowUpscale = true
    ): string {
        $rgb = new RGB();
        $image = $this->imagine->open($filepath)->usePalette($rgb);

        $this->autorotateIfNeeded($image, $filepath);

        $w = $image->getSize()->getWidth();
        $h = $image->getSize()->getHeight();

        if (!$allowUpscale) {
            $targetW = min($targetW, $w);
            $targetH = min($targetH, $h);
        }

        $mode = strtolower($mode);
        $image = match ($mode) {
            'square'  => $this->doSquare($image, $targetW, $targetH),
            'cover'   => $this->doCover($image,  $targetW, $targetH),
            'contain' => $this->doContain($image, $targetW, $targetH, $rgb),
            'fit'     => $this->doFit($image,    $targetW, $targetH),
            default   => throw new \InvalidArgumentException("Mode inconnu: {$mode}")
        };

        // ÉCRASEMENT DIRECT — pas de .tmp, pas de backup
        $this->saveOverwriteSameExt($image, $filepath);

        // Contrôle simple
        if (!is_file($filepath) || filesize($filepath) < 1024 || !@getimagesize($filepath)) {
            throw new \RuntimeException('Rendu image invalide (taille trop petite ou non lisible).');
        }
        return $filepath;
    }

    /* ---------- Stratégies ---------- */

    private function doSquare(ImageInterface $image, int $tw, int $th): ImageInterface
    {
        $w = $image->getSize()->getWidth();
        $h = $image->getSize()->getHeight();
        $side = min($w, $h);
        $x = (int)(($w - $side) / 2);
        $y = (int)(($h - $side) / 2);
        return $image->crop(new Point($x, $y), new Box($side, $side))->resize(new Box($tw, $th));
    }

    private function doCover(ImageInterface $image, int $tw, int $th): ImageInterface
    {
        $w = $image->getSize()->getWidth();
        $h = $image->getSize()->getHeight();
        $rS = $w / $h;
        $rD = $tw / $th;
        if ($rS > $rD) {
            $newH = $h;
            $newW = (int)round($h * $rD);
            $x = (int)(($w - $newW) / 2);
            $y = 0;
        } else {
            $newW = $w;
            $newH = (int)round($w / $rD);
            $x = 0;
            $y = (int)(($h - $newH) / 2);
        }
        return $image->crop(new Point($x, $y), new Box($newW, $newH))->resize(new Box($tw, $th));
    }

    private function doContain(ImageInterface $image, int $tw, int $th, RGB $rgb): ImageInterface
    {
        $w = $image->getSize()->getWidth();
        $h = $image->getSize()->getHeight();
        $scale = min(($w ? $tw / $w : 1), ($h ? $th / $h : 1));
        $nw = max(1, (int)floor($w * $scale));
        $nh = max(1, (int)floor($h * $scale));
        $resized = $image->resize(new Box($nw, $nh));
        $white = $rgb->color('ffffff');
        $canvas = (new Imagine())->create(new Box($tw, $th), $white)->usePalette($rgb);
        $x = (int)(($tw - $nw) / 2);
        $y = (int)(($th - $nh) / 2);
        return $canvas->paste($resized, new Point($x, $y));
    }

    private function doFit(ImageInterface $image, int $mw, int $mh): ImageInterface
    {
        $w = $image->getSize()->getWidth();
        $h = $image->getSize()->getHeight();
        $scale = min(($w ? $mw / $w : 1), ($h ? $mh / $h : 1), 1); // jamais agrandir
        $nw = max(1, (int)floor($w * $scale));
        $nh = max(1, (int)floor($h * $scale));
        return $image->resize(new Box($nw, $nh));
    }

    /* ---------- Sauvegarde & utils ---------- */

    private function saveOverwriteSameExt(ImageInterface $image, string $filepath): void
    {
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $opts = match ($ext) {
            'jpg', 'jpeg' => ['jpeg_quality' => 82],
            'png'        => ['png_compression_level' => 9],
            'webp'       => ['webp_quality' => 82],
            default      => ['jpeg_quality' => 82], // fallback
        };
        $image->save($filepath, $opts);
    }

    private function autorotateIfNeeded(ImageInterface $image, string $path): void
    {
        if (!function_exists('exif_read_data')) return;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg'], true)) return;
        $exif = @exif_read_data($path);
        $o = (int)($exif['Orientation'] ?? 1);
        switch ($o) {
            case 3:
                $image->rotate(180);
                break;
            case 6:
                $image->rotate(90);
                break;
            case 8:
                $image->rotate(-90);
                break;
        }
    }
}
