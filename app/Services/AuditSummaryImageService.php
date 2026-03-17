<?php

namespace App\Services;

use App\Models\Audits\InfrastructureAudit;
use App\Models\LiveAudit;
use App\Models\PmjayAudit;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * AuditSummaryImageService
 *
 * Generates a 1080×1920 WhatsApp-ready PNG summary card.
 *
 * Stack: Intervention Image v3 + PHP GD (built-in).
 * Zero OS deps — no Node, no Chrome, no Ghostscript, no Imagick.
 * Works identically on Windows/Laragon and Ubuntu/DigitalOcean.
 *
 * Install:
 *   composer require intervention/image
 *
 * GD check (already enabled in Laragon PHP 8.x):
 *   php -r "echo extension_loaded('gd') ? 'GD OK' : 'GD missing';"
 *
 * Font setup — place .ttf files in public/fonts/ :
 *   public/fonts/Inter-Regular.ttf
 *   public/fonts/Inter-Bold.ttf
 *   public/fonts/Inter-Black.ttf
 *   → Download free: https://fonts.google.com/specimen/Inter
 *
 * .env overrides (all optional):
 *   SUMMARY_FONT_REGULAR=public/fonts/Inter-Regular.ttf
 *   SUMMARY_FONT_BOLD=public/fonts/Inter-Bold.ttf
 *   SUMMARY_FONT_BLACK=public/fonts/Inter-Black.ttf
 */
class AuditSummaryImageService
{
    // ── Canvas ──────────────────────────────────────────────────────────────
    private const W  = 1080;
    private const H  = 1920;
    private const PX = 52;   // horizontal page padding

    // ── Colours (all without leading #) ─────────────────────────────────────
    private const BG           = '0b1120';
    private const CARD_BG      = '111c2e';
    private const CARD_BORDER  = '1e2d4a';
    private const STRIP_BG     = '1b2e1f';
    private const STRIP_BORDER = '1e4d2c';

    private const WHITE  = 'ffffff';
    private const MUTED  = '64748b';
    private const DIM    = '334155';
    private const LABEL  = '94a3b8';

    private const EMERALD  = '10b981';
    private const GREEN    = '4ade80';

    private const TELE     = '38bdf8';
    private const TELE_BG  = '0c1e2e';
    private const TELE_BD  = '0e3a5a';

    private const FIELD    = 'fbbf24';
    private const FIELD_BG = '1a160a';
    private const FIELD_BD = '3d2c0a';

    private const LIVE     = 'a78bfa';
    private const LIVE_BG  = '160d2a';
    private const LIVE_BD  = '3b1d6e';

    private const INFRA    = 'fb7185';
    private const INFRA_BG = '200d14';
    private const INFRA_BD = '5b1532';

    private const RED      = 'f43f5e';
    private const RED_BG   = '4c0519';
    private const RED_BD   = '881337';

    // ── Font paths ───────────────────────────────────────────────────────────
    private string $fontRegular;
    private string $fontBold;
    private string $fontBlack;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());

        $this->fontRegular = base_path(env('SUMMARY_FONT_REGULAR', 'public/fonts/Inter-Regular.ttf'));
        $this->fontBold    = base_path(env('SUMMARY_FONT_BOLD',    'public/fonts/Inter-Bold.ttf'));
        $this->fontBlack   = base_path(env('SUMMARY_FONT_BLACK',   'public/fonts/Inter-Black.ttf'));
    }

    // ══════════════════════════════════════════════════════════════════════
    // Public API
    // ══════════════════════════════════════════════════════════════════════

    /** Generate and return raw PNG bytes. */
    public function generatePng(): string
    {
        return $this->drawImage($this->collectStats());
    }

    /**
     * Generate, save to public storage disk, return the public URL.
     * Pass the URL directly to WhatsApp Business API as the image link.
     */
    public function generateAndSave(string $storagePath = ''): string
    {
        if ($storagePath === '') {
            $storagePath = 'audit-summaries/' . now()->format('Y-m-d_His') . '.png';
        }
        Storage::disk('public')->put($storagePath, $this->generatePng());
        return Storage::disk('public')->url($storagePath);
    }

    // ══════════════════════════════════════════════════════════════════════
    // Drawing  (Intervention Image v3 — correct API)
    // ══════════════════════════════════════════════════════════════════════

    private function drawImage(array $d): string
    {
        $img = $this->manager->create(self::W, self::H)->fill('#' . self::BG);

        $y = 0;
        $y = $this->drawHeader($img, $d, $y);
        $y = $this->drawTotalStrip($img, $d, $y);
        $y = $this->drawSectionLabel($img, 'AUDIT TYPE BREAKDOWN', $y);
        $y = $this->drawAuditCards($img, $d, $y);

        if ($d['money_charged'] > 0) {
            $y = $this->drawMoneyAlert($img, $d, $y);
        }

        $y = $this->drawSectionLabel($img, 'TOP PERFORMING DMOs', $y);
        $y = $this->drawLeaderboard($img, $d, $y);
        $y = $this->drawSectionLabel($img, 'DISTRICT BREAKDOWN — ASSIGNED AUDITS', $y);
        $y = $this->drawDistrictBars($img, $d, $y);
        $this->drawFooter($img, $d);

        return $img->toPng()->toString();
    }

    // ── Header ───────────────────────────────────────────────────────────────

    private function drawHeader(ImageInterface $img, array $d, int $y): int
    {
        $y += 52;
        $pw = self::W - self::PX * 2;

        // Logo box
        $this->rect($img, self::PX, $y, 50, 50, self::EMERALD, self::EMERALD);
        $this->txt($img, 'P', self::PX + 25, $y + 34, 24, self::BG, 'black', 'center');

        // Brand
        $this->txt($img, 'AB PMJAY ASSAM  ·  AUDIT DIVISION', 116, $y + 15, 11, self::MUTED, 'regular');
        $this->txt($img, 'Monthly Audit Summary', 116, $y + 40, 22, self::WHITE, 'black');

        // Period pill
        $pillX = self::W - self::PX - 210;
        $this->rect($img, $pillX, $y + 4, 210, 44, '1e2d4a', '2d3f5e');
        $this->txt($img, 'PERIOD', $pillX + 105, $y + 20, 10, self::MUTED, 'regular', 'center');
        $this->txt($img, $d['from'] . ' – ' . $d['to'], $pillX + 105, $y + 38, 11, self::LABEL, 'bold', 'center');

        $y += 62;
        $this->hline($img, self::PX, self::W - self::PX, $y, self::CARD_BORDER);

        return $y + 26;
    }

    // ── Grand total strip ─────────────────────────────────────────────────────

    private function drawTotalStrip(ImageInterface $img, array $d, int $y): int
    {
        $pw = self::W - self::PX * 2;
        $ph = 118;

        $this->rect($img, self::PX, $y, $pw, $ph, self::STRIP_BG, self::STRIP_BORDER, 2);
        // Left accent
        $this->rect($img, self::PX, $y, 5, $ph, self::EMERALD, self::EMERALD);

        $cx = self::PX + 34;
        $this->txt($img, 'TOTAL AUDITS CONDUCTED', $cx, $y + 22, 11, self::GREEN, 'regular');
        $this->txt($img, number_format($d['grand_total']), $cx, $y + 82, 56, self::WHITE, 'black');
        $this->txt($img, 'All types  ·  last 30 days', $cx, $y + 108, 12, self::GREEN, 'regular');

        $rx = self::W - self::PX - 12;
        $this->txt($img, 'COMPLETION', $rx, $y + 22, 11, self::GREEN, 'regular', 'right');
        $this->txt($img, $d['comp_rate'] . '%', $rx, $y + 76, 46, self::EMERALD, 'black', 'right');
        $this->txt($img, number_format($d['grand_completed']) . ' / ' . number_format($d['grand_total']), $rx, $y + 108, 12, self::GREEN, 'regular', 'right');

        return $y + $ph + 22;
    }

    // ── Section label ─────────────────────────────────────────────────────────

    private function drawSectionLabel(ImageInterface $img, string $label, int $y): int
    {
        $y += 6;
        $this->txt($img, $label, self::PX, $y + 13, 10, self::MUTED, 'regular');
        $this->hline($img, self::PX, self::W - self::PX, $y + 20, self::CARD_BORDER);
        return $y + 32;
    }

    // ── 2×2 Audit cards ───────────────────────────────────────────────────────

    private function drawAuditCards(ImageInterface $img, array $d, int $y): int
    {
        $gap = 14;
        $cw  = (int) ((self::W - self::PX * 2 - $gap) / 2);
        $ch  = 220;

        // Card definitions
        $cards = [
            [
                'label'  => 'TELEPHONIC AUDIT',
                'count'  => number_format($d['tele_total']),
                'sub'    => 'Completed: ' . number_format($d['tele_completed']),
                'rate'   => $d['tele_rate'],
                'pend'   => number_format($d['tele_total'] - $d['tele_completed']) . ' pending',
                'accent' => self::TELE,
                'bg'     => self::TELE_BG,
                'border' => self::TELE_BD,
                'chips'  => [],
            ],
            [
                'label'  => 'FIELD VISITS',
                'count'  => number_format($d['field_total']),
                'sub'    => 'Completed: ' . number_format($d['field_completed']),
                'rate'   => $d['field_rate'],
                'pend'   => number_format($d['field_total'] - $d['field_completed']) . ' pending',
                'accent' => self::FIELD,
                'bg'     => self::FIELD_BG,
                'border' => self::FIELD_BD,
                'chips'  => [],
            ],
            [
                'label'  => 'LIVE AUDIT',
                'count'  => number_format($d['live_total']),
                'sub'    => 'On-site beneficiary verification',
                'rate'   => null,
                'accent' => self::LIVE,
                'bg'     => self::LIVE_BG,
                'border' => self::LIVE_BD,
                'chips'  => [
                    [number_format($d['money_charged']) . ' money charged', self::RED, self::RED_BG, self::RED_BD],
                ],
            ],
            [
                'label'  => 'INFRASTRUCTURE AUDIT',
                'count'  => number_format($d['infra_total']),
                'sub'    => 'Hospital facility inspections',
                'rate'   => null,
                'accent' => self::INFRA,
                'bg'     => self::INFRA_BG,
                'border' => self::INFRA_BD,
                'chips'  => [
                    ['Public: '  . $d['infra_public'],      self::TELE,  self::TELE_BG,  self::TELE_BD],
                    ['Private: ' . $d['infra_private'],     self::LIVE,  self::LIVE_BG,  self::LIVE_BD],
                    ['ICU: '     . $d['infra_icu'],         self::GREEN, self::STRIP_BG, self::STRIP_BORDER],
                    ['OT: '      . $d['infra_ot'],          self::FIELD, self::FIELD_BG, self::FIELD_BD],
                    ['Good: '    . $d['infra_hygiene_good'],self::GREEN, self::STRIP_BG, self::STRIP_BORDER],
                    ['Poor: '    . $d['infra_hygiene_poor'],self::RED,   self::RED_BG,   self::RED_BD],
                    ['Banner ok:'  . $d['infra_banner_passed'], self::GREEN, self::STRIP_BG, self::STRIP_BORDER],
                    ['Failed: '  . $d['infra_banner_failed'],   self::INFRA, self::INFRA_BG, self::INFRA_BD],
                ],
            ],
        ];

        // Row 1: cards 0 and 1
        $this->drawAuditCard($img, self::PX,          $y, $cw, $ch, $cards[0]);
        $this->drawAuditCard($img, self::PX + $cw + $gap, $y, $cw, $ch, $cards[1]);
        $y += $ch + $gap;

        // Row 2: cards 2 and 3
        $this->drawAuditCard($img, self::PX,          $y, $cw, $ch, $cards[2]);
        $this->drawAuditCard($img, self::PX + $cw + $gap, $y, $cw, $ch, $cards[3]);
        $y += $ch;

        return $y + 10;
    }

    private function drawAuditCard(
        ImageInterface $img,
        int $x, int $y, int $w, int $h,
        array $c
    ): void {
        // Background + top accent
        $this->rect($img, $x, $y, $w, $h, $c['bg'], $c['border']);
        $this->rect($img, $x, $y, $w, 4, $c['accent'], $c['accent']);

        $tx = $x + 16;

        $this->txt($img, $c['label'], $tx, $y + 22, 10, $c['accent'], 'regular');
        $this->txt($img, $c['count'], $tx, $y + 68, 40, self::WHITE, 'black');
        $this->txt($img, $c['sub'],   $tx, $y + 88, 12, self::MUTED, 'regular');

        if ($c['rate'] !== null) {
            $barW  = $w - 32;
            $fillW = (int) max(4, round($barW * $c['rate'] / 100));
            $barY  = $y + 106;
            $this->rect($img, $tx, $barY, $barW, 7, '1e2d4a', '1e2d4a');
            $this->rect($img, $tx, $barY, $fillW, 7, $c['accent'], $c['accent']);
            $this->txt($img, $c['rate'] . '%', $tx, $y + 132, 18, $c['accent'], 'bold');
            $this->txt($img, $c['pend'] ?? '', $tx + $barW, $y + 132, 11, self::MUTED, 'regular', 'right');
        }

        if (! empty($c['chips'])) {
            $cx = $tx;
            $cy = $y + ($c['rate'] !== null ? 152 : 108);
            foreach ($c['chips'] as [$lbl, $col, $bg, $bd]) {
                $cw = min($w - 32, $this->charWidth($lbl, 11) + 20);
                if ($cx + $cw > $x + $w - 16) { $cx = $tx; $cy += 28; }
                $this->rect($img, $cx, $cy, $cw, 22, $bg, $bd);
                $this->txt($img, $lbl, (int) ($cx + $cw / 2), $cy + 15, 11, $col, 'bold', 'center');
                $cx += $cw + 6;
            }
        }
    }

    // ── Money charged alert ───────────────────────────────────────────────────

    private function drawMoneyAlert(ImageInterface $img, array $d, int $y): int
    {
        $pw = self::W - self::PX * 2;
        $ph = 100;
        $y += 10;

        $this->rect($img, self::PX, $y, $pw, $ph, self::RED_BG, self::RED_BD, 2);
        $this->rect($img, self::PX, $y, 5, $ph, self::RED, self::RED);

        // Warning circle
        $this->circle($img, self::PX + 55, $y + 50, 24, '5b1532', self::RED_BD);
        $this->txt($img, '!', self::PX + 55, $y + 58, 22, self::RED, 'black', 'center');

        $tx = self::PX + 96;
        $this->txt($img, 'MONEY CHARGED FROM BENEFICIARIES', $tx, $y + 22, 11, self::RED, 'bold');
        $this->txt($img, number_format($d['money_charged']) . ' cases', $tx, $y + 60, 30, self::RED, 'black');
        $this->txt($img, 'Live audit cases where payment was collected — action required', $tx, $y + 82, 11, self::MUTED, 'regular');

        // Action badge
        $bx = self::W - self::PX - 124;
        $this->rect($img, $bx, $y + 34, 114, 32, self::RED, self::RED);
        $this->txt($img, 'Action Required', $bx + 57, $y + 54, 12, self::WHITE, 'bold', 'center');

        return $y + $ph + 18;
    }

    // ── DMO Leaderboard ───────────────────────────────────────────────────────

    private function drawLeaderboard(ImageInterface $img, array $d, int $y): int
    {
        $rh  = 80;
        $gap = 8;
        $pw  = self::W - self::PX * 2;

        $avatarBg  = ['1e3a2f', '0c1e2e', '1c102e', '1a160a', '200d14'];
        $avatarFg  = [self::GREEN, self::TELE, self::LIVE, self::FIELD, self::INFRA];
        $rankBg    = ['fbbf24', '94a3b8', 'b45309', self::MUTED, self::MUTED];

        if ($d['dmo_leaderboard']->isEmpty()) {
            $this->txt($img, 'No DMO activity in this period', self::PX + $pw / 2, $y + 28, 13, self::MUTED, 'regular', 'center');
            return $y + 50;
        }

        foreach ($d['dmo_leaderboard'] as $i => $dmo) {
            $ry  = $y + $i * ($rh + $gap);
            $mid = $ry + (int) ($rh / 2);

            // Row background
            $this->rect($img, self::PX, $ry, $pw, $rh, self::CARD_BG, self::CARD_BORDER);

            // Rank circle
            $this->circle($img, self::PX + 28, $mid, 15, $rankBg[$i] ?? self::MUTED, $rankBg[$i] ?? self::MUTED);
            $this->txt($img, (string) ($i + 1), self::PX + 28, $mid + 6, 12, '0b1120', 'black', 'center');

            // Avatar circle
            $avCx = self::PX + 64;
            $this->circle($img, $avCx, $mid, 17, $avatarBg[$i] ?? '111c2e', $avatarBg[$i] ?? '111c2e');
            $initials = collect(explode(' ', $dmo['name']))
                ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                ->take(2)->implode('');
            $this->txt($img, $initials, $avCx, $mid + 5, 12, $avatarFg[$i] ?? self::WHITE, 'bold', 'center');

            // Name + district
            $nx = self::PX + 98;
            $this->txt($img, $dmo['name'],     $nx, $ry + 26, 14, self::WHITE, 'bold');
            $this->txt($img, $dmo['district'], $nx, $ry + 44, 11, self::MUTED, 'regular');

            // Type chips
            $chipX = $nx;
            $chipY = $ry + 58;
            $chips = [
                ['T:' . $dmo['tele'],  self::TELE,  $dmo['tele']  > 0],
                ['F:' . $dmo['field'], self::FIELD, $dmo['field'] > 0],
                ['L:' . $dmo['live'],  self::LIVE,  $dmo['live']  > 0],
                ['I:' . $dmo['infra'], self::INFRA, $dmo['infra'] > 0],
                ['Rs' . $dmo['money'], self::RED,   $dmo['money'] > 0],
            ];
            foreach ($chips as [$lbl, $col, $show]) {
                if (! $show) continue;
                $cw = $this->charWidth($lbl, 10) + 14;
                $this->rect($img, $chipX, $chipY, $cw, 17, '0f172a', $col);
                $this->txt($img, $lbl, (int) ($chipX + $cw / 2), $chipY + 13, 10, $col, 'bold', 'center');
                $chipX += $cw + 5;
            }

            // Total
            $this->txt($img, number_format($dmo['total']), self::W - self::PX - 12, $mid + 8, 22, self::WHITE, 'black', 'right');
        }

        return $y + count($d['dmo_leaderboard']) * ($rh + $gap) + 12;
    }

    // ── District bars ─────────────────────────────────────────────────────────

    private function drawDistrictBars(ImageInterface $img, array $d, int $y): int
    {
        $rh  = 74;
        $gap = 8;
        $pw  = self::W - self::PX * 2;
        $max = $d['district_stats']->max('total') ?: 1;

        if ($d['district_stats']->isEmpty()) {
            $this->txt($img, 'No district data in this period', self::PX + $pw / 2, $y + 28, 13, self::MUTED, 'regular', 'center');
            return $y + 50;
        }

        foreach ($d['district_stats'] as $i => $dist) {
            $ry = $y + $i * ($rh + $gap);

            $this->rect($img, self::PX, $ry, $pw, $rh, self::CARD_BG, self::CARD_BORDER);

            $tx   = self::PX + 18;
            $barW = $pw - 100;
            $fill = (int) max(4, round($barW * $dist->total / $max));

            $this->txt($img, $dist->district, $tx, $ry + 22, 13, self::WHITE, 'bold');
            $this->txt($img, 'T: ' . $dist->tele . '   F: ' . $dist->field_cnt, $tx, $ry + 40, 11, self::MUTED, 'regular');

            $barY = $ry + 52;
            $this->rect($img, $tx, $barY, $barW, 8, '1e2d4a', '1e2d4a');
            $this->rect($img, $tx, $barY, $fill, 8, '0ea5e9', '0ea5e9');

            $this->txt($img, number_format($dist->total), self::W - self::PX - 12, $ry + $rh / 2 + 8, 18, self::WHITE, 'black', 'right');
        }

        return $y + count($d['district_stats']) * ($rh + $gap) + 18;
    }

    // ── Footer ────────────────────────────────────────────────────────────────

    private function drawFooter(ImageInterface $img, array $d): void
    {
        $fy = self::H - 58;
        $this->hline($img, self::PX, self::W - self::PX, $fy, self::CARD_BORDER);
        $this->txt($img, 'PMJAY Audit Intelligence  ·  Assam State Health Agency', self::PX, $fy + 26, 11, self::MUTED, 'regular');
        $this->txt($img, 'Generated ' . $d['generated_at'], self::W - self::PX, $fy + 26, 10, self::DIM, 'regular', 'right');
        // Bottom accent
        $this->rect($img, 0, self::H - 6, self::W, 6, self::EMERALD, self::EMERALD);
    }

    // ══════════════════════════════════════════════════════════════════════
    // Drawing primitives (Intervention Image v3 — verified API)
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Draw a filled rectangle.
     *
     * Intervention Image v3: drawRectangle(x1, y1, closure)
     * The closure receives a RectangleFactory — size(), background(), border()
     */
    private function rect(
        ImageInterface $img,
        int $x, int $y, int $w, int $h,
        string $bg, string $border,
        int $borderWidth = 1
    ): void {
        $img->drawRectangle($x, $y, function (RectangleFactory $r) use ($w, $h, $bg, $border, $borderWidth) {
            $r->size($w, $h)
              ->background('#' . $bg)
              ->border('#' . $border, $borderWidth);
        });
    }

    /**
     * Draw a filled circle.
     *
     * Intervention Image v3: drawCircle(x, y, closure)
     * The closure receives a CircleFactory — radius(), background(), border()
     */
    private function circle(
        ImageInterface $img,
        int $cx, int $cy, int $radius,
        string $bg, string $border,
        int $borderWidth = 1
    ): void {
        $img->drawCircle($cx, $cy, function (CircleFactory $c) use ($radius, $bg, $border, $borderWidth) {
            $c->radius($radius)
              ->background('#' . $bg)
              ->border('#' . $border, $borderWidth);
        });
    }

    /**
     * Draw a horizontal line.
     *
     * Intervention Image v3: drawLine(closure)
     * The closure receives a LineFactory — from(), to(), color(), width()
     */
    private function hline(
        ImageInterface $img,
        int $x1, int $x2, int $y,
        string $color,
        int $width = 1
    ): void {
        $img->drawLine(function (LineFactory $l) use ($x1, $x2, $y, $color, $width) {
            $l->from($x1, $y)
              ->to($x2, $y)
              ->color('#' . $color)
              ->width($width);
        });
    }

    /**
     * Write text onto the canvas.
     *
     * Intervention Image v3: text(text, x, y, closure)
     * The closure receives a FontFactory — filename(), size(), color(), align(), valign()
     */
    private function txt(
        ImageInterface $img,
        string $text,
        int    $x,
        int    $y,
        int    $size,
        string $color,
        string $weight = 'regular',
        string $align  = 'left'
    ): void {
        $file = match ($weight) {
            'black' => $this->fontBlack,
            'bold'  => $this->fontBold,
            default => $this->fontRegular,
        };

        // Graceful fallback if the font file is missing
        if (! file_exists($file)) {
            $file = $this->fontRegular;
        }
        if (! file_exists($file)) {
            return; // silently skip — avoids crash on first run without fonts
        }

        $img->text($text, $x, $y, function (FontFactory $font) use ($file, $size, $color, $align) {
            $font->filename($file);
            $font->size($size);
            $font->color('#' . $color);
            $font->align($align);
            $font->valign('bottom');
        });
    }

    /**
     * Rough character-width estimate for chip/pill sizing.
     * ~0.58× font size per character — close enough for layout math.
     */
    private function charWidth(string $text, int $size): int
    {
        return (int) (mb_strlen($text) * $size * 0.58);
    }

    // ══════════════════════════════════════════════════════════════════════
    // Data collection  (unchanged across all versions)
    // ══════════════════════════════════════════════════════════════════════

    public function collectStats(): array
    {
        $from = now()->subDays(30)->startOfDay();
        $to   = now()->endOfDay();

        $assigned = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->selectRaw("
                COUNT(*) AS grand_total,
                SUM(CASE WHEN status = 'completed'      THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN audit_type = 'telephonic' THEN 1 ELSE 0 END) AS tele_total,
                SUM(CASE WHEN audit_type = 'telephonic' AND status = 'completed' THEN 1 ELSE 0 END) AS tele_completed,
                SUM(CASE WHEN audit_type = 'field'      THEN 1 ELSE 0 END) AS field_total,
                SUM(CASE WHEN audit_type = 'field'      AND status = 'completed' THEN 1 ELSE 0 END) AS field_completed
            ")->first();

        $teleTotal  = (int) ($assigned->tele_total      ?? 0);
        $teleComp   = (int) ($assigned->tele_completed  ?? 0);
        $fieldTotal = (int) ($assigned->field_total     ?? 0);
        $fieldComp  = (int) ($assigned->field_completed ?? 0);

        $liveStats = LiveAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("COUNT(*) AS live_total, SUM(CASE WHEN money_charged = 'Yes' THEN 1 ELSE 0 END) AS money_charged_count")
            ->first();

        $liveTotal    = (int) ($liveStats->live_total          ?? 0);
        $moneyCharged = (int) ($liveStats->money_charged_count ?? 0);

        $infraStats = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("
                COUNT(*) AS infra_total,
                SUM(CASE WHEN hospital_type = 'Public'    THEN 1 ELSE 0 END) AS public_count,
                SUM(CASE WHEN hospital_type = 'Private'   THEN 1 ELSE 0 END) AS private_count,
                SUM(CASE WHEN overall_hygiene = 'Good'    THEN 1 ELSE 0 END) AS hygiene_good,
                SUM(CASE WHEN overall_hygiene = 'Average' THEN 1 ELSE 0 END) AS hygiene_average,
                SUM(CASE WHEN overall_hygiene = 'Poor'    THEN 1 ELSE 0 END) AS hygiene_poor,
                SUM(CASE WHEN icu_available   = 'Yes'     THEN 1 ELSE 0 END) AS icu_count,
                SUM(CASE WHEN ot_available    = 'Yes'     THEN 1 ELSE 0 END) AS ot_count,
                SUM(CASE WHEN ai_banner_pass  = 1         THEN 1 ELSE 0 END) AS banner_passed,
                SUM(CASE WHEN ai_banner_pass  = 0         THEN 1 ELSE 0 END) AS banner_failed
            ")->first();

        $infraTotal = (int) ($infraStats->infra_total ?? 0);

        $liveCounts  = LiveAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("submitted_by AS user_id, COUNT(*) AS n")->groupBy('submitted_by')->pluck('n', 'user_id');

        $infraCounts = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("submitted_by AS user_id, COUNT(*) AS n")->groupBy('submitted_by')->pluck('n', 'user_id');

        $teleByDistrict = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->where('audit_type', 'telephonic')
            ->selectRaw("district_id, COUNT(*) AS n")->groupBy('district_id')->pluck('n', 'district_id');

        $fieldByDistrict = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->where('audit_type', 'field')
            ->selectRaw("district_id, COUNT(*) AS n")->groupBy('district_id')->pluck('n', 'district_id');

        $moneyByUser = LiveAudit::whereBetween('created_at', [$from, $to])
            ->where('money_charged', 'Yes')
            ->selectRaw("submitted_by AS user_id, COUNT(*) AS n")->groupBy('submitted_by')->pluck('n', 'user_id');

        $dmoLeaderboard = User::role('dmo')->with(['districts:id,name'])->get()
            ->map(function (User $u) use ($liveCounts, $infraCounts, $moneyByUser, $teleByDistrict, $fieldByDistrict) {
                $did   = $u->districts->first()?->id;
                $live  = (int) ($liveCounts[$u->id]  ?? 0);
                $infra = (int) ($infraCounts[$u->id] ?? 0);
                $tele  = $did ? (int) ($teleByDistrict[$did]  ?? 0) : 0;
                $field = $did ? (int) ($fieldByDistrict[$did] ?? 0) : 0;
                $money = (int) ($moneyByUser[$u->id] ?? 0);
                return [
                    'name'     => $u->name,
                    'district' => $u->districts->first()?->name ?? '—',
                    'tele' => $tele, 'field' => $field,
                    'live' => $live, 'infra' => $infra,
                    'money' => $money,
                    'total' => $live + $infra + $tele + $field,
                ];
            })
            ->filter(fn ($r) => $r['total'] > 0)
            ->sortByDesc('total')->values()->take(5);

        $districtStats = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->join('districts', 'districts.id', '=', 'pmjay_audits.district_id')
            ->selectRaw("
                districts.name AS district, COUNT(*) AS total,
                SUM(CASE WHEN audit_type='telephonic' THEN 1 ELSE 0 END) AS tele,
                SUM(CASE WHEN audit_type='field'      THEN 1 ELSE 0 END) AS field_cnt,
                SUM(CASE WHEN status='completed'      THEN 1 ELSE 0 END) AS completed
            ")
            ->groupBy('districts.id', 'districts.name')
            ->orderByDesc('total')->limit(5)->get();

        $grandTotal = $teleTotal + $fieldTotal + $liveTotal + $infraTotal;
        $grandComp  = $teleComp  + $fieldComp  + $liveTotal + $infraTotal;
        $compRate   = $grandTotal > 0 ? round($grandComp / $grandTotal * 100) : 0;

        return [
            'from'                  => $from->format('d M Y'),
            'to'                    => $to->format('d M Y'),
            'generated_at'          => now()->format('d M Y, h:i A'),
            'grand_total'           => $grandTotal,
            'grand_completed'       => $grandComp,
            'comp_rate'             => $compRate,
            'tele_total'            => $teleTotal,
            'tele_completed'        => $teleComp,
            'tele_rate'             => $teleTotal  > 0 ? round($teleComp  / $teleTotal  * 100) : 0,
            'field_total'           => $fieldTotal,
            'field_completed'       => $fieldComp,
            'field_rate'            => $fieldTotal > 0 ? round($fieldComp / $fieldTotal * 100) : 0,
            'live_total'            => $liveTotal,
            'money_charged'         => $moneyCharged,
            'infra_total'           => $infraTotal,
            'infra_public'          => (int) ($infraStats->public_count    ?? 0),
            'infra_private'         => (int) ($infraStats->private_count   ?? 0),
            'infra_hygiene_good'    => (int) ($infraStats->hygiene_good    ?? 0),
            'infra_hygiene_average' => (int) ($infraStats->hygiene_average ?? 0),
            'infra_hygiene_poor'    => (int) ($infraStats->hygiene_poor    ?? 0),
            'infra_icu'             => (int) ($infraStats->icu_count       ?? 0),
            'infra_ot'              => (int) ($infraStats->ot_count        ?? 0),
            'infra_banner_passed'   => (int) ($infraStats->banner_passed   ?? 0),
            'infra_banner_failed'   => (int) ($infraStats->banner_failed   ?? 0),
            'dmo_leaderboard'       => $dmoLeaderboard,
            'district_stats'        => $districtStats,
        ];
    }
}
