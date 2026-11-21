<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A shoe model: name + size range + photo. Stock is kept per colour (ProductColour). */
class Product extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'cost_price' => 'float', 'selling_price' => 'float'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function colours(): HasMany
    {
        // each colour's size ranges stay together, colours in the order they were first added
        return $this->hasMany(ProductColour::class)
            ->orderByRaw('(SELECT MIN(pc2.id) FROM product_colours pc2 WHERE pc2.product_id = product_colours.product_id AND pc2.colour = product_colours.colour)')
            ->orderBy('id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /** "30-42", "38" or "" */
    public function sizeLabel(): string
    {
        // size ranges typed on the colour lines, e.g. "5-7, 11-1, 2-5"
        $sizes = $this->colours->pluck('size')->filter()->unique()->values();
        if ($sizes->isNotEmpty()) {
            return $sizes->implode(', ');
        }

        $from = trim((string) $this->size_from);
        $to = trim((string) $this->size_to);
        if ($from !== '' && $to !== '' && $from !== $to) {
            return $from . '-' . $to;
        }

        return $from !== '' ? $from : $to;
    }

    public function totalStock(): int
    {
        return (int) ($this->relationLoaded('colours') ? $this->colours->sum('qty') : $this->colours()->sum('qty'));
    }

    public function isLow(): bool
    {
        return $this->totalStock() <= $this->low_stock_level;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset($this->image_path) : null;
    }

    /** Colours + size ranges that still have pairs, for sending to customers: ["Navy: 5-7, 11-1", "Black"]. */
    public function availableLines(bool $pairs = false): array
    {
        return array_map(function ($g) use ($pairs) {
            if (! $g['sized']) {
                return $g['colour'] . ($pairs ? ' (' . $g['qty'] . ' prs)' : '');
            }

            return $g['colour'] . ': ' . implode(', ', array_map(fn ($s) => ($s['size'] !== '' ? $s['size'] : 'other') . ($pairs ? ' (' . $s['qty'] . ')' : ''), $g['sizes']));
        }, colour_groups($this->colours->where('qty', '>', 0)));
    }

    /** WhatsApp text for one product (no price). */
    public function shareText(): string
    {
        $lines = ['*' . $this->name . '*', 'Item No: ' . $this->code];
        if ($extra = collect([$this->brand?->name, $this->category?->name])->filter()->implode(' · ')) {
            $lines[] = $extra;
        }
        if ($available = $this->availableLines()) {
            $lines[] = '';
            $lines[] = 'Available:';
            foreach ($available as $a) {
                $lines[] = '• ' . $a;
            }
        }
        $lines[] = '';
        $lines[] = trim(setting('company_name') . ' ' . setting('company_phone'));

        return implode("\n", $lines);
    }

    /**
     * Photo for the PDF catalogue: cut to the same square for every product (centre crop),
     * cached in storage/app/pdf-images. Without the GD extension the original JPEG is used.
     */
    public function pdfImagePath(int $size = 420): ?string
    {
        $path = $this->image_path ? public_path($this->image_path) : null;
        if (! $path || ! is_file($path)) {
            return null;
        }
        $isJpeg = in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg']);
        if (! function_exists('imagecreatefromstring')) {
            return $isJpeg ? $path : null;
        }
        $dir = storage_path('app/pdf-images');
        $out = $dir . '/' . $this->id . '-' . substr(md5($path . filemtime($path) . $size), 0, 10) . '.jpg';
        if (is_file($out)) {
            return $out;
        }
        $src = @imagecreatefromstring((string) file_get_contents($path));
        if (! $src) {
            return $isJpeg ? $path : null;
        }
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $side = min($w, $h);
        $dst = imagecreatetruecolor($size, $size);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, (int) (($w - $side) / 2), (int) (($h - $side) / 2), $size, $size, $side, $side);
        imagejpeg($dst, $out, 82);
        imagedestroy($src);
        imagedestroy($dst);

        return is_file($out) ? $out : ($isJpeg ? $path : null);
    }

    /** "black 20 · white 10 · blue 30" */
    public function colourSummary(): string
    {
        return $this->colours->map(fn ($c) => $c->title . ' ' . $c->qty)->implode(' · ');
    }

    public function marginPercent(): ?float
    {
        return $this->selling_price > 0 && $this->cost_price > 0
            ? round(($this->selling_price - $this->cost_price) / $this->selling_price * 100, 1)
            : null;
    }

    /** Next item number with the shop's prefix, e.g. ARD-1001 (prefix set in Settings). */
    public static function nextCode(): string
    {
        $prefix = strtoupper(trim((string) setting('item_prefix', 'ARD'))) ?: 'ARD';
        $max = static::where('code', 'like', $prefix . '-%')->pluck('code')
            ->map(fn ($c) => (int) preg_replace('/\D/', '', substr($c, strlen($prefix) + 1)))->max();
        $n = max(1000, (int) $max) + 1;
        while (static::where('code', $prefix . '-' . $n)->exists()) {
            $n++;
        }

        return $prefix . '-' . $n;
    }

    /** Data used by the item pickers on the purchase / sale screens. */
    public function toPicker(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'size' => $this->sizeLabel(),
            'brand' => $this->brand?->name,
            'category_id' => $this->category_id,
            'image' => $this->imageUrl(),
            'price' => (float) $this->selling_price,
            'cost' => (float) $this->cost_price,
            'colours' => $this->colours->map(fn ($c) => ['id' => $c->id, 'name' => $c->title, 'colour' => $c->colour, 'size' => $c->size, 'qty' => (int) $c->qty, 'hex' => ProductColour::hex($c->colour)])->values(),
        ];
    }

    public static function pickerList(): array
    {
        return static::with(['colours', 'brand'])->where('is_active', true)->orderByRaw('(SELECT COALESCE(SUM(qty),0) FROM product_colours pc WHERE pc.product_id = products.id) <= 0')->orderBy('name')->get()->map->toPicker()->values()->all();
    }
}
