<?php

namespace SportTrackerConnector\Tracker;

use DateTime;
use DateTimeZone;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Abstract tracker.
 */
abstract class AbstractTracker implements TrackerInterface
{

    use LoggerAwareTrait;

    /**
     * Username for polar.
     *
     * @var string
     */
    protected $username;

    /**
     * Password for polar.
     *
     * @var string
     */
    protected $password;

    /**
     * The tracker timezone.
     *
     * @var DateTimeZone
     */
    protected $timeZone;

    /**
     * The sport mapper.
     *
     * @var \SportTrackerConnector\Workout\Workout\SportMapperInterface
     */
    protected $sportMapper;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger The logger.
     * @param string $username Username for the tracker.
     * @param string $password Password for the tracker.
     */
    public function __construct(LoggerInterface $logger, $username = null, $password = null)
    {
        $this->logger = $logger;
        $this->username = $username;
        $this->password = $password;
        $this->timeZone = new DateTimeZone('UTC');
    }

    /**
     * Get a new instance using a config array.
     *
     * @param LoggerInterface $logger The logger.
     * @param array $config The config for the new instance.
     * @return TrackerInterface
     */
    public static function fromConfig(LoggerInterface $logger, array $config)
    {
        $tracker = new static($logger, $config['auth']['username'], $config['auth']['password']);

        $timeZone = new DateTimeZone($config['timezone']);
        $tracker->setTimeZone($timeZone);

        return $tracker;
    }

    /**
     * Get offset between the tracker time zone and UTC time zone in seconds.
     *
     * @return integer
     */
    public function getTimeZoneOffset()
    {
        $originDateTime = new DateTime('now', $this->getTimeZone());

        $utcTimeZone = new DateTimeZone('UTC');
        $utcDateTime = new DateTime('now', $utcTimeZone);

        return $utcTimeZone->getOffset($utcDateTime) - $this->getTimeZone()->getOffset($originDateTime);
    }

    /**
     * Get the timezone of the tracker.
     *
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set the timezone of the tracker.
     *
     * @param DateTimeZone $timeZone The timezone.
     */
    public function setTimeZone(DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * Get the sport mapper.
     *
     * @return \SportTrackerConnector\Workout\Workout\SportMapperInterface
     */
    public function getSportMapper()
    {
        if ($this->sportMapper === null) {
            $this->sportMapper = $this->constructSportMapper();
        }

        return $this->sportMapper;
    }

    /**
     * Construct the sport mapper.
     *
     * @return \SportTrackerConnector\Workout\Workout\SportMapperInterface
     */
    abstract protected function constructSportMapper();
}
