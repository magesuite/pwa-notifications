<?php

namespace MageSuite\PwaNotifications\Model\Data;

class Notification implements \MageSuite\PwaNotifications\Api\Data\NotificationInterface
{
    /**
     * @var int
     */
    protected $deviceId;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $badge;

    /**
     * @inheritDoc
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @inheritDoc
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @inheritDoc
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
        return $this;
    }

    public function __toString()
    {
        return json_encode([
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
            'icon' => $this->getIcon(),
            'image' => $this->getImage(),
            'badge' => $this->getBadge(),
            'data' => [
                'url' => $this->getUrl(),
            ]
        ]);
    }
}
