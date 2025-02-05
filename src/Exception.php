<?php
/**
 * linkedin-client
 * Exception.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 23:11
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

use GuzzleHttp\Exception\RequestException;

/**
 * Class Exception
 * @package LinkedIn
 */
class Exception extends \Exception
{
    /**
     * Error's description
     *
     * @var string
     */
    protected $description;

    /**
     * LinkedIn raw response
     *
     * @var string
     */
    protected $rawResponse;

    /**
     * LinkedIn code so we can use strings
     *
     * @var string
     */
    protected $code;

    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param null $previousException
     * @param $description
     */
    public function __construct(
        $message = "",
        $code = 0,
        $previousException = null,
        $description = '',
        $rawResponse = ''
    ) {
        $this->code = $code;
        $parent_code = is_numeric($code) ? $code : 0;
        parent::__construct($message, $parent_code, $previousException);
        $this->description = $description;
        $this->rawResponse = $rawResponse;
    }

    /**
     * Get textual description that summarizes error.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get LinkedIn raw response.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @param RequestException $exception
     *
     * @return self
     */
    public static function fromRequestException($exception)
    {
        return new static(
            $exception->getMessage(),
            $exception->getCode(),
            $exception,
            static::extractErrorDescription($exception)
        );
    }

    /**
     * @param RequestException $exception
     *
     * @return null|string
     */
    private static function extractErrorDescription($exception)
    {
        $response = $exception->getResponse();
        if (!$response) {
            return null;
        }

        $json = Client::responseToArray($response);
        if (isset($json['error_description'])) {
            return $json['error_description'];
        }
        if (isset($json['message'])) {
            return $json['message'];
        }
        return null;
    }
}