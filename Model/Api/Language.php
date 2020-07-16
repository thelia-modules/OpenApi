<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\Lang;

/**
 * @OA\Schema(
 *     schema="Language",
 *     title="Language",
 *     description="Language model"
 * )
 */
class Language extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $code;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $locale;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $url;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $dateFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $timeFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $datetimeFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimalSeparator;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $thousandsSeparator;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $active;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $visible;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimals;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $byDefault;

    /**
     * Creates an OpenApi Language from a Thelia Lang model, then returns it
     *
     * @param Lang $lang
     * @return $this
     */
    public function createFromTheliaLang(Lang $lang)
    {
        $this->id = $lang->getId();
        $this->title = $lang->getTitle();
        $this->code = $lang->getCode();
        $this->locale = $lang->getLocale();
        $this->url = $lang->getUrl();
        $this->dateFormat = $lang->getDateFormat();
        $this->timeFormat = $lang->getTimeFormat();
        $this->datetimeFormat = $lang->getDatetimeFormat();
        $this->decimalSeparator = $lang->getDecimalSeparator();
        $this->thousandsSeparator = $lang->getThousandsSeparator();
        $this->active = $lang->getActive();
        $this->visible = $lang->getVisible();
        $this->decimals = $lang->getDecimals();
        $this->byDefault = $lang->getByDefault();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Language
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Language
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Language
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Language
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param mixed $dateFormat
     * @return Language
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @param mixed $timeFormat
     * @return Language
     */
    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * @param mixed $datetimeFormat
     * @return Language
     */
    public function setDatetimeFormat($datetimeFormat)
    {
        $this->datetimeFormat = $datetimeFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    /**
     * @param mixed $decimalSeparator
     * @return Language
     */
    public function setDecimalSeparator($decimalSeparator)
    {
        $this->decimalSeparator = $decimalSeparator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * @param mixed $thousandsSeparator
     * @return Language
     */
    public function setThousandsSeparator($thousandsSeparator)
    {
        $this->thousandsSeparator = $thousandsSeparator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     * @return Language
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     * @return Language
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param mixed $decimals
     * @return Language
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getByDefault()
    {
        return $this->byDefault;
    }

    /**
     * @param mixed $byDefault
     * @return Language
     */
    public function setByDefault($byDefault)
    {
        $this->byDefault = $byDefault;
        return $this;
    }


}