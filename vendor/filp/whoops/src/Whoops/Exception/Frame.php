<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use InvalidArgumentException;
use Serializable;

class Frame implements Serializable
{
    /**
     * @var array
     */
    protected $frame;

    /**
     * @var string
     */
    protected $fileContentsCache;

    /**
     * @var array[]
     */
    protected $comments = array();

    /**
     * @param array[]
     */
    public function __construct(array $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @param  bool $shortened
     * @return string|null
     */
    public function getFile($shortened = false)
    {
        if(empty($this->frame['file'])) {
            return null;
        }

        $file = $this->frame['file'];

        // Check if this frame occurred within an eval().
        // @todo: This can be made more reliable by checking if we've entered
        // eval() in a previous trace, but will need some more work on the upper
        // trace collector(s).
        if(preg_match('/^(.*)\((\d+)\) : eval\(\)\'d code$/', $file, $matches)) {
            $file = $this->frame['file'] = $matches[1];
            $this->frame['line'] = (int) $matches[2];
        }

        if($shortened && is_string($file)) {
            // Replace the part of the path that all frames have in common, and add 'soft hyphens' for smoother line-breaks.
            $dirname = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
            $file = str_replace($dirname, "…", $file);
            $file = str_replace("/", "/&shy;", $file);
        }

        return $file;
    }

    /**
     * @return int|null
     */
    public function getLine()
    {
        return isset($this->frame['line']) ? $this->frame['line'] : null;
    }

    /**
     * @return string|null
     */
    public function getClass()
    {
        return isset($this->frame['class']) ? $this->frame['class'] : null;
    }

    /**
     * @return string|null
     */
    public function getFunction()
    {
        return isset($this->frame['function']) ? $this->frame['function'] : null;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return isset($this->frame['args']) ? (array) $this->frame['args'] : array();
    }

    /**
     * Returns the full contents of the file for this frame,
     * if it's known.
     * @return string|null
     */
    public function getFileContents()
    {
        if($this->fileContentsCache === null && $filePath = $this->getFile()) {

            // Return null if the file doesn't actually exist - this may
            // happen in cases where the filename is provided as, for
            // example, 'Unknown'
            if(!is_file($filePath)) {
                return null;
            }

            $this->fileContentsCache = file_get_contents($filePath);
        }

        return $this->fileContentsCache;
    }

    /**
     * Adds a comment to this frame, that can be received and
     * used by other handlers. For example, the PrettyPage handler
     * can attach these comments under the code for each frame.
     *
     * An interesting use for this would be, for example, code analysis
     * & annotations.
     *
     * @param string $comment
     * @param string $context Optional string identifying the origin of the comment
     */
    public function addComment($comment, $context = 'global')
    {
        $this->comments[] = array(
            'comment' => $comment,
            'context' => $context
        );
    }

    /**
     * Returns all comments for this frame. Optionally allows
     * a filter to only retrieve comments from a specific
     * context.
     *
     * @param  string $filter
     * @return array[]
     */
    public function getComments($filter = null)
    {
        $comments = $this->comments;

        if($filter !== null) {
            $comments = array_filter($comments, function($c) use($filter) {
                return $c['context'] == $filter;
            });
        }

        return $comments;
    }

    /**
     * Returns the array containing the raw frame data from which
     * this Frame object was built
     * 
     * @return array
     */
    public function getRawFrame()
    {
        return $this->frame;
    }

    /**
     * Returns the contents of the file for this frame as an
     * array of lines, and optionally as a clamped range of lines.
     *
     * NOTE: lines are 0-indexed
     *
     * @example
     *     Get all lines for this file
     *     $frame->getFileLines(); // => array( 0 => '<?php', 1 => '...', ...)
     * @example
     *     Get one line for this file, starting at line 10 (zero-indexed, remember!)
     *     $frame->getFileLines(9, 1); // array( 10 => '...', 11 => '...')
     *
     * @throws InvalidArgumentException if $length is less than or equal to 0
     * @param  int $start
     * @param  int $length
     * @return string[]|null
     */
    public function getFileLines($start = 0, $length = null)
    {
        if(null !== ($contents = $this->getFileContents())) {
            $lines = explode("\n", $contents);

            // Get a subset of lines from $start to $end
            if($length !== null)
            {
                $start  = (int) $start;
                $length = (int) $length;
                if ($start < 0) {
                    $start = 0;
                }

                if($length <= 0) {
                    throw new InvalidArgumentException(
                        "\$length($length) cannot be lower or equal to 0"
                    );
                }

                $lines = array_slice($lines, $start, $length, true);
            }

            return $lines;
        }
    }

    /**
     * Implements the Serializable interface, with special
     * steps to also save the existing comments.
     * 
     * @see Serializable::serialize
     * @return string
     */
    public function serialize()
    {
        $frame = $this->frame;
        if(!empty($this->comments)) {
            $frame['_comments'] = $this->comments;
        }

        return serialize($frame);
    }

    /**
     * Unserializes the frame data, while also preserving
     * any existing comment data.
     * 
     * @see Serializable::unserialize
     * @param string $serializedFrame
     */
    public function unserialize($serializedFrame)
    {
        $frame = unserialize($serializedFrame);

        if(!empty($frame['_comments'])) {
            $this->comments = $frame['_comments'];
            unset($frame['_comments']);
        }

        $this->frame = $frame;
    }
}
