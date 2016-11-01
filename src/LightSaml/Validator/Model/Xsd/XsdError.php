<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Xsd;

class XsdError
{
    const WARNING = 'Warning';
    const ERROR = 'Error';
    const FATAL = 'Fatal';

    private static $levelMap = [
        LIBXML_ERR_WARNING => self::WARNING,
        LIBXML_ERR_ERROR => self::ERROR,
        LIBXML_ERR_FATAL => self::FATAL,
    ];

    /** @var string */
    private $level;

    /** @var string */
    private $code;

    /** @var string */
    private $message;

    /** @var string */
    private $line;

    /** @var string */
    private $column;

    /**
     * @param \LibXMLError $error
     *
     * @return XsdError
     */
    public static function fromLibXMLError(\LibXMLError $error)
    {
        return new self(
            isset(self::$levelMap[$error->level]) ? self::$levelMap[$error->level] : 'Unknown',
            $error->code,
            $error->message,
            $error->line,
            $error->column
        );
    }

    /**
     * @param string $level
     * @param string $code
     * @param string $message
     * @param string $line
     * @param string $column
     */
    public function __construct($level, $code, $message, $line, $column)
    {
        $this->level = $level;
        $this->code = $code;
        $this->message = $message;
        $this->line = $line;
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function __toString()
    {
        return sprintf(
            '%s %s: %s on line %s column %s',
            $this->level,
            $this->code,
            trim($this->message),
            $this->line,
            $this->column
        );
    }
}
