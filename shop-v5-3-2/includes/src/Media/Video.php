<?php declare(strict_types=1);

namespace JTL\Media;

use JTL\Shop;

/**
 * Class Video
 * @package JTL\Media
 */
class Video
{
    /**
     * Video types
     */
    public const TYPE_INVALID = -1;
    public const TYPE_FILE    = 0;
    public const TYPE_YOUTUBE = 1;
    public const TYPE_VIMEO   = 2;

    /**
     * @var int
     */
    protected int $type = self::TYPE_INVALID;

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @var string
     */
    protected string $id = '';

    /**
     * @var bool
     */
    protected bool $loop = false;

    /**
     * @var int|null
     */
    protected ?int $width = null;

    /**
     * @var int|null
     */
    protected ?int $height = null;

    /**
     * @var int|null
     */
    protected ?int $startSec = null;

    /**
     * @var int|null
     */
    protected ?int $endSec = null;

    /**
     * @var bool
     */
    protected bool $related = false;

    /**
     * @var bool
     */
    protected bool $allowFullscreen = true;

    /**
     * @var string
     */
    protected string $fileFormat = '';

    /**
     * @var array
     */
    protected array $extraGetArgs = [];

    /**
     * @param string $url
     * @return self
     */
    public static function fromUrl(string $url): self
    {
        return new self($url);
    }

    /**
     * @param \stdClass $mediaFile
     * @return self|null
     */
    public static function fromMediaFile(\stdClass $mediaFile): ?self
    {
        if ($mediaFile->nMedienTyp === 3) {
            $url   = Shop::getURL() . '/' . \PFAD_MEDIAFILES . $mediaFile->cPfad;
            $video = self::fromUrl($url);
            $video->setFileFormat($mediaFile->videoType);
        } elseif (!empty($mediaFile->cURL)) {
            $url   = $mediaFile->cURL;
            $video = self::fromUrl($url);
        } else {
            return null;
        }

        foreach ($mediaFile->oMedienDateiAttribut_arr as $attrib) {
            if ($attrib->cName === 'related') {
                $video->setRelated($attrib->cWert === '1');
            } elseif ($attrib->cName === 'width' && \is_numeric($attrib->cWert)) {
                $video->setWidth((int)$attrib->cWert);
            } elseif ($attrib->cName === 'height' && \is_numeric($attrib->cWert)) {
                $video->setHeight((int)$attrib->cWert);
            } elseif ($attrib->cName === 'fullscreen' && ($attrib->cWert === '0' || $attrib->cWert === 'false')) {
                $video->setAllowFullscreen(false);
            }
        }

        return $video;
    }

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        if (\str_starts_with($url, Shop::getURL())) {
            $this->type = self::TYPE_FILE;
            $this->url  = $url;
        }

        $parsedUrl = \parse_url($url);

        if ($parsedUrl === false || empty($parsedUrl['host'])) {
            $this->type = self::TYPE_INVALID;
            return;
        }

        if (\str_contains($parsedUrl['host'], 'youtube')) {
            if (empty($parsedUrl['query'])) {
                $this->type = self::TYPE_INVALID;
                return;
            }

            \parse_str($parsedUrl['query'], $query);

            if (empty($query['v'])) {
                $this->type = self::TYPE_INVALID;
                return;
            }

            $this->type = self::TYPE_YOUTUBE;
            $this->id   = $query['v'];
            $this->url  = 'https://www.youtube-nocookie.com/embed/' . $this->id;
        } elseif (\str_contains($parsedUrl['host'], 'youtu.be')) {
            if (empty($parsedUrl['path'])) {
                $this->type = self::TYPE_INVALID;
                return;
            }

            $this->type = self::TYPE_YOUTUBE;
            $this->id   = \trim($parsedUrl['path'], '/');
            $this->url  = 'https://www.youtube-nocookie.com/embed/' . $this->id;
        } elseif (\str_contains($parsedUrl['host'], 'vimeo.com')) {
            if (empty($parsedUrl['path'])) {
                $this->type = self::TYPE_INVALID;
                return;
            }

            $videoId = \trim($parsedUrl['path'], '/');

            if (\str_starts_with($videoId, 'video/')) {
                $videoId = \substr($videoId, 6);
            }

            $this->type = self::TYPE_VIMEO;
            $this->id   = $videoId;
            $this->url  = 'https://player.vimeo.com/video/' . $this->id;
        }
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getEmbedUrl(): string
    {
        if ($this->type === self::TYPE_YOUTUBE) {
            $embedUrl  = $this->url;
            $arguments = [];

            if ($this->loop) {
                $arguments['playlist'] = $this->id;
                $arguments['loop']     = 1;
            }

            if ($this->startSec) {
                $arguments['start'] = $this->startSec;
            }

            if ($this->endSec) {
                $arguments['end'] = $this->endSec;
            }

            $arguments['rel']            = $this->related ? '1' : '0';
            $arguments['iv_load_policy'] = 3;
            $arguments                   = \array_merge($arguments, $this->extraGetArgs);

            if (!empty($arguments)) {
                $embedUrl .= '?' . \http_build_query($arguments);
            }

            return $embedUrl;
        }

        if ($this->type === self::TYPE_VIMEO) {
            $embedUrl  = $this->url;
            $arguments = [];

            if ($this->loop) {
                $arguments['loop'] = 1;
            }

            $arguments = \array_merge($arguments, $this->extraGetArgs);

            if (!empty($arguments)) {
                $embedUrl .= '?' . \http_build_query($arguments);
            }

            return $embedUrl;
        }

        return $this->url;
    }

    /**
     * @return bool
     */
    public function isLoop(): bool
    {
        return $this->loop;
    }

    /**
     * @param bool $loop
     * @return $this
     */
    public function setLoop(bool $loop): self
    {
        $this->loop = $loop;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRelated(): bool
    {
        return $this->related;
    }

    /**
     * @param bool $related
     * @return $this
     */
    public function setRelated(bool $related): self
    {
        $this->related = $related;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowFullscreen(): bool
    {
        return $this->allowFullscreen;
    }

    /**
     * @param bool $allowFullscreen
     * @return $this
     */
    public function setAllowFullscreen(bool $allowFullscreen): self
    {
        $this->allowFullscreen = $allowFullscreen;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileFormat(): string
    {
        return $this->fileFormat;
    }

    /**
     * @param string $fileFormat
     */
    public function setFileFormat(string $fileFormat): void
    {
        $this->fileFormat = $fileFormat;
    }

    /**
     * @return int|null
     */
    public function getStartSec(): ?int
    {
        return $this->startSec;
    }

    /**
     * @param int|null $startSec
     */
    public function setStartSec(?int $startSec): void
    {
        $this->startSec = $startSec;
    }

    /**
     * @return int|null
     */
    public function getEndSec(): ?int
    {
        return $this->endSec;
    }

    /**
     * @param int|null $endSec
     */
    public function setEndSec(?int $endSec): void
    {
        $this->endSec = $endSec;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function setExtraGetArg(string $name, mixed $value): self
    {
        $this->extraGetArgs[$name] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    protected function getYouTubePreviewImageUrl(): ?string
    {
        if (!empty($this->id)) {
            return 'https://i3.ytimg.com/vi/' . $this->id . '/maxresdefault.jpg';
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getPreviewImageUrl(): ?string
    {
        if ($this->type === self::TYPE_YOUTUBE) {
            $srcURL = 'https://i3.ytimg.com/vi/' . $this->id . '/maxresdefault.jpg';
        } elseif ($this->type === self::TYPE_VIMEO) {
            $videoXML = \json_decode(\file_get_contents('https://vimeo.com/api/v2/video/' . $this->id . '.json'));
            $srcURL   = $videoXML[0]->thumbnail_large ?? null;
        } else {
            return null;
        }

        $localPath = \PFAD_ROOT . \STORAGE_VIDEO_THUMBS . $this->id . '.jpg';
        $localUrl  = Shop::getURL() . '/' . \STORAGE_VIDEO_THUMBS . $this->id . '.jpg';

        if (!empty($srcURL) && !\is_file($localPath)) {
            if (!\is_writable(\PFAD_ROOT . \STORAGE_VIDEO_THUMBS)) {
                return null;
            }

            \file_put_contents($localPath, \file_get_contents($srcURL));
        }

        return $localUrl;
    }
}
