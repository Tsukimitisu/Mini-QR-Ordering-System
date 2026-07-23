<?php
// api/qr.php
// Generates a QR code SVG using PHP only.

header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');

function renderQrError(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    echo '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="220" viewBox="0 0 220 220">'
        . '<rect width="100%" height="100%" fill="#fff"/>'
        . '<text x="12" y="32" font-family="Arial, sans-serif" font-size="16" fill="#111827">'
        . $safeMessage
        . '</text>'
        . '</svg>';
    exit;
}

$data = isset($_GET['data']) ? trim($_GET['data']) : '';
$size = isset($_GET['size']) ? intval($_GET['size']) : 220;

if ($data === '') {
    error_log('QR generation: missing data parameter');
    renderQrError('Missing QR data');
}

if (strlen($data) > 250) {
    error_log('QR generation: data exceeds max length of 250');
    renderQrError('QR data too long');
}

if (preg_match('/[\x00-\x1F\x7F]/', $data)) {
    error_log('QR generation: invalid characters detected in data');
    renderQrError('QR data contains unsupported characters');
}

$size = max(120, min(800, $size));

try {
    $qr = new SimpleQrCode($data);
    echo $qr->toSvg($size);
} catch (Exception $e) {
    renderQrError('QR error');
}

class SimpleQrCode
{
    private string $text;
    private int $version;
    private int $size;
    private array $modules = [];
    private array $isFunction = [];

    private array $versionInfo = [
        1 => ['ec' => 7,  'blocks' => [[1, 19]],             'align' => []],
        2 => ['ec' => 10, 'blocks' => [[1, 34]],             'align' => [6, 18]],
        3 => ['ec' => 15, 'blocks' => [[1, 55]],             'align' => [6, 22]],
        4 => ['ec' => 20, 'blocks' => [[1, 80]],             'align' => [6, 26]],
        5 => ['ec' => 26, 'blocks' => [[1, 108]],            'align' => [6, 30]],
        6 => ['ec' => 18, 'blocks' => [[2, 68]],             'align' => [6, 34]],
        7 => ['ec' => 20, 'blocks' => [[2, 78]],             'align' => [6, 22, 38]],
        8 => ['ec' => 24, 'blocks' => [[2, 97]],             'align' => [6, 24, 42]],
        9 => ['ec' => 30, 'blocks' => [[2, 116]],            'align' => [6, 26, 46]],
        10 => ['ec' => 18, 'blocks' => [[2, 68], [2, 69]],   'align' => [6, 28, 50]],
    ];

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->version = $this->chooseVersion(strlen($text));
        $this->size = 21 + ($this->version - 1) * 4;
        $this->modules = array_fill(0, $this->size, array_fill(0, $this->size, false));
        $this->isFunction = array_fill(0, $this->size, array_fill(0, $this->size, false));

        $this->drawFunctionPatterns();
        $codewords = $this->createCodewords();
        $this->drawCodewords($codewords, 0);
        $this->drawFormatBits(0);

        if ($this->version >= 7) {
            $this->drawVersionBits();
        }
    }

    public function toSvg(int $pixelSize): string
    {
        $border = 4;
        $viewSize = $this->size + ($border * 2);
        $path = '';

        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                if ($this->modules[$y][$x]) {
                    $path .= 'M' . ($x + $border) . ',' . ($y + $border) . 'h1v1h-1z';
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $pixelSize . '" height="' . $pixelSize . '" viewBox="0 0 ' . $viewSize . ' ' . $viewSize . '" shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#fff"/>'
            . '<path d="' . $path . '" fill="#000"/>'
            . '</svg>';
    }

    private function chooseVersion(int $length): int
    {
        foreach ($this->versionInfo as $version => $info) {
            $dataCodewords = $this->getDataCodewordCount($version);
            $charCountBits = $version <= 9 ? 8 : 16;
            $neededBits = 4 + $charCountBits + ($length * 8);

            if ($neededBits <= $dataCodewords * 8) {
                return $version;
            }
        }

        throw new Exception('QR data is too long for supported versions.');
    }

    private function getDataCodewordCount(int $version): int
    {
        $total = 0;
        foreach ($this->versionInfo[$version]['blocks'] as $group) {
            $total += $group[0] * $group[1];
        }
        return $total;
    }

    private function createCodewords(): array
    {
        $dataCodewordCount = $this->getDataCodewordCount($this->version);
        $bits = [];

        $this->appendBits($bits, 0x4, 4);
        $this->appendBits($bits, strlen($this->text), $this->version <= 9 ? 8 : 16);

        foreach (array_values(unpack('C*', $this->text)) as $byte) {
            $this->appendBits($bits, $byte, 8);
        }

        $capacityBits = $dataCodewordCount * 8;
        $terminator = min(4, $capacityBits - count($bits));
        $this->appendBits($bits, 0, $terminator);

        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        $dataCodewords = [];
        for ($i = 0; $i < count($bits); $i += 8) {
            $value = 0;
            for ($j = 0; $j < 8; $j++) {
                $value = ($value << 1) | $bits[$i + $j];
            }
            $dataCodewords[] = $value;
        }

        $padBytes = [0xEC, 0x11];
        $padIndex = 0;
        while (count($dataCodewords) < $dataCodewordCount) {
            $dataCodewords[] = $padBytes[$padIndex % 2];
            $padIndex++;
        }

        return $this->addErrorCorrection($dataCodewords);
    }

    private function appendBits(array &$bits, int $value, int $length): void
    {
        for ($i = $length - 1; $i >= 0; $i--) {
            $bits[] = ($value >> $i) & 1;
        }
    }

    private function addErrorCorrection(array $dataCodewords): array
    {
        $info = $this->versionInfo[$this->version];
        $ecCodewordsPerBlock = $info['ec'];
        $blocks = [];
        $offset = 0;

        foreach ($info['blocks'] as $group) {
            [$count, $dataLength] = $group;
            for ($i = 0; $i < $count; $i++) {
                $data = array_slice($dataCodewords, $offset, $dataLength);
                $offset += $dataLength;
                $blocks[] = [
                    'data' => $data,
                    'ec' => $this->reedSolomonRemainder($data, $ecCodewordsPerBlock),
                ];
            }
        }

        $result = [];
        $maxDataLength = max(array_map(fn($block) => count($block['data']), $blocks));

        for ($i = 0; $i < $maxDataLength; $i++) {
            foreach ($blocks as $block) {
                if ($i < count($block['data'])) {
                    $result[] = $block['data'][$i];
                }
            }
        }

        for ($i = 0; $i < $ecCodewordsPerBlock; $i++) {
            foreach ($blocks as $block) {
                $result[] = $block['ec'][$i];
            }
        }

        return $result;
    }

    private function reedSolomonRemainder(array $data, int $degree): array
    {
        $generator = [1];
        for ($i = 0; $i < $degree; $i++) {
            $next = array_fill(0, count($generator) + 1, 0);
            for ($j = 0; $j < count($generator); $j++) {
                $next[$j] ^= $this->gfMultiply($generator[$j], 1);
                $next[$j + 1] ^= $this->gfMultiply($generator[$j], $this->gfPow(2, $i));
            }
            $generator = $next;
        }

        $remainder = array_fill(0, $degree, 0);
        foreach ($data as $byte) {
            $factor = $byte ^ $remainder[0];
            array_shift($remainder);
            $remainder[] = 0;

            for ($i = 0; $i < $degree; $i++) {
                $remainder[$i] ^= $this->gfMultiply($generator[$i + 1], $factor);
            }
        }

        return $remainder;
    }

    private function gfPow(int $x, int $power): int
    {
        $result = 1;
        for ($i = 0; $i < $power; $i++) {
            $result = $this->gfMultiply($result, $x);
        }
        return $result;
    }

    private function gfMultiply(int $x, int $y): int
    {
        $result = 0;
        while ($y > 0) {
            if (($y & 1) !== 0) {
                $result ^= $x;
            }
            $x <<= 1;
            if (($x & 0x100) !== 0) {
                $x ^= 0x11D;
            }
            $y >>= 1;
        }
        return $result & 0xFF;
    }

    private function drawFunctionPatterns(): void
    {
        $this->drawFinderPattern(3, 3);
        $this->drawFinderPattern($this->size - 4, 3);
        $this->drawFinderPattern(3, $this->size - 4);

        foreach ($this->versionInfo[$this->version]['align'] as $x) {
            foreach ($this->versionInfo[$this->version]['align'] as $y) {
                if ($this->isFunction[$y][$x]) {
                    continue;
                }
                $this->drawAlignmentPattern($x, $y);
            }
        }

        for ($i = 0; $i < $this->size; $i++) {
            if (!$this->isFunction[6][$i]) {
                $this->setFunctionModule($i, 6, $i % 2 === 0);
            }
            if (!$this->isFunction[$i][6]) {
                $this->setFunctionModule(6, $i, $i % 2 === 0);
            }
        }

        for ($i = 0; $i <= 8; $i++) {
            if ($i !== 6) {
                $this->setFunctionModule(8, $i, false);
                $this->setFunctionModule($i, 8, false);
            }
        }

        for ($i = 0; $i < 8; $i++) {
            $this->setFunctionModule($this->size - 1 - $i, 8, false);
            $this->setFunctionModule(8, $this->size - 1 - $i, false);
        }

        $this->setFunctionModule(8, $this->size - 8, true);

        if ($this->version >= 7) {
            for ($i = 0; $i < 6; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    $this->setFunctionModule($this->size - 11 + $j, $i, false);
                    $this->setFunctionModule($i, $this->size - 11 + $j, false);
                }
            }
        }
    }

    private function drawFinderPattern(int $centerX, int $centerY): void
    {
        for ($dy = -4; $dy <= 4; $dy++) {
            for ($dx = -4; $dx <= 4; $dx++) {
                $x = $centerX + $dx;
                $y = $centerY + $dy;
                if ($x < 0 || $x >= $this->size || $y < 0 || $y >= $this->size) {
                    continue;
                }

                $distance = max(abs($dx), abs($dy));
                $black = $distance !== 2 && $distance !== 4;
                $this->setFunctionModule($x, $y, $black);
            }
        }
    }

    private function drawAlignmentPattern(int $centerX, int $centerY): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $distance = max(abs($dx), abs($dy));
                $this->setFunctionModule($centerX + $dx, $centerY + $dy, $distance !== 1);
            }
        }
    }

    private function setFunctionModule(int $x, int $y, bool $black): void
    {
        $this->modules[$y][$x] = $black;
        $this->isFunction[$y][$x] = true;
    }

    private function drawCodewords(array $codewords, int $mask): void
    {
        $bits = [];
        foreach ($codewords as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $bits[] = ($byte >> $i) & 1;
            }
        }

        $bitIndex = 0;
        $upward = true;

        for ($right = $this->size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right--;
            }

            for ($vert = 0; $vert < $this->size; $vert++) {
                $y = $upward ? $this->size - 1 - $vert : $vert;

                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    if ($this->isFunction[$y][$x]) {
                        continue;
                    }

                    $bit = $bitIndex < count($bits) ? $bits[$bitIndex] === 1 : false;
                    $bitIndex++;

                    if ($this->maskApplies($mask, $x, $y)) {
                        $bit = !$bit;
                    }

                    $this->modules[$y][$x] = $bit;
                }
            }

            $upward = !$upward;
        }
    }

    private function maskApplies(int $mask, int $x, int $y): bool
    {
        return (($x + $y) % 2) === 0;
    }

    private function drawFormatBits(int $mask): void
    {
        $errorCorrectionLevel = 1;
        $data = ($errorCorrectionLevel << 3) | $mask;
        $remainder = $data;

        for ($i = 0; $i < 10; $i++) {
            $remainder = ($remainder << 1) ^ ((($remainder >> 9) & 1) * 0x537);
        }

        $bits = (($data << 10) | $remainder) ^ 0x5412;

        for ($i = 0; $i <= 5; $i++) {
            $this->modules[$i][8] = (($bits >> $i) & 1) !== 0;
        }
        $this->modules[7][8] = (($bits >> 6) & 1) !== 0;
        $this->modules[8][8] = (($bits >> 7) & 1) !== 0;
        $this->modules[8][7] = (($bits >> 8) & 1) !== 0;

        for ($i = 9; $i < 15; $i++) {
            $this->modules[8][14 - $i] = (($bits >> $i) & 1) !== 0;
        }

        for ($i = 0; $i < 8; $i++) {
            $this->modules[8][$this->size - 1 - $i] = (($bits >> $i) & 1) !== 0;
        }

        for ($i = 8; $i < 15; $i++) {
            $this->modules[$this->size - 15 + $i][8] = (($bits >> $i) & 1) !== 0;
        }

        $this->modules[$this->size - 8][8] = true;
    }

    private function drawVersionBits(): void
    {
        $remainder = $this->version;
        for ($i = 0; $i < 12; $i++) {
            $remainder = ($remainder << 1) ^ ((($remainder >> 11) & 1) * 0x1F25);
        }

        $bits = ($this->version << 12) | $remainder;

        for ($i = 0; $i < 18; $i++) {
            $bit = (($bits >> $i) & 1) !== 0;
            $a = $this->size - 11 + ($i % 3);
            $b = intdiv($i, 3);
            $this->modules[$b][$a] = $bit;
            $this->modules[$a][$b] = $bit;
        }
    }
}
?>
