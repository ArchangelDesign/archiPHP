<?php

namespace Archi\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    private $stream;
    private $meta;

    /**
     * Stream constructor.
     * @param resource $stream
     * @param array $meta
     */
    public function __construct($stream, array $meta = [])
    {
        if (!is_resource($stream)) {
            throw new \RuntimeException('Invalid stream provided');
        }
        $this->stream = $stream;
        $this->meta = $meta;
    }


    public function __toString()
    {
        return stream_get_contents($this->stream);
    }

    public function close()
    {
        fclose($this->stream);
    }

    public function detach()
    {
        // TODO: Implement detach() method.
    }

    public function getSize()
    {
        // TODO: Implement getSize() method.
    }

    public function tell()
    {
        return ftell($this->stream);
    }

    public function eof()
    {
        return feof($this->stream);
    }

    public function isSeekable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->stream, $offset, $whence);
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return false;
    }

    public function write($string)
    {
        // TODO: Implement write() method.
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        return fread($this->stream, $length);
    }

    public function getContents()
    {
        return $this->__toString();
    }

    public function getMetadata($key = null)
    {
        if (is_null($key)) {
            return $this->meta;
        }
        return $this->meta[$key] ?? null;
    }
}
