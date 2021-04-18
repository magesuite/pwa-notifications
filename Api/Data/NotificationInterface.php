<?php

namespace MageSuite\PwaNotifications\Api\Data;

interface NotificationInterface
{
    /**
     * @return int
     */
    public function getDeviceId();

    /**
     * @param int $deviceId
     * @return self
     */
    public function setDeviceId($deviceId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return self
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     * @return self
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     * @return self
     */
    public function setIcon($icon);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return self
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getBadge();

    /**
     * @param string $badge
     * @return self
     */
    public function setBadge($badge);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param string $json
     * @return string
     */
    public function fromString($json);
}
