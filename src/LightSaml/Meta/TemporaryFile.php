<?php

namespace LightSaml\Meta;

class TemporaryFile
{
    /** @var  resource */
    protected $resource;

    /** @var  string */
    protected $path;

    /**
     * @param string $contents
     *
     * @return TemporaryFile
     */
    public static function fromContents($contents)
    {
        $result = new static();

        fwrite($result->getResource(), $contents);

        return $result;
    }

    /**
     *
     */
    public function __construct()
    {
        $this->resource = tmpfile();
        $data = stream_get_meta_data($this->resource);
        $this->path = $data['uri'];
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
